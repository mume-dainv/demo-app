<?php

namespace App\Imports;

use App\Enums\RoleEnums;
use App\Mail\RegisterUserMail;
use App\Models\LogImport;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, ShouldBeUnique
{
    use Importable, RemembersChunkOffset;

    public function __construct(protected $fileName = '', protected $user)
    {
    }

    /**
     * @throws \Throwable
     */
    public function collection(Collection $collection): void
    {
        $collection = $collection->unique('email')->toArray();
        $dataInsert = [];
        $dataError = [];
        $mailSend = [];
        $rowNumber = $this->getChunkOffset() - 1;
        foreach ($collection as $index => $row) {
            $validate = Validator::make($row, $this->userImportRulesValidation());
            try {
                $validate->validate();
            } catch (\Exception $e) {
                $dataError[] = ['row ' . $rowNumber + $index => $validate->errors()];
                continue;
            }
            $password = Str::random(8);
            $row['password'] = Hash::make($password);
            $dataInsert[] = $row;
            $mailSend[] = ['email' => $row['email'], 'password' => $password];
        }

        DB::beginTransaction();
        try {
            User::upsert($dataInsert, ['email']);

            $log = LogImport::where([
                    ['user_id', $this->user->id],
                    ['file_name', $this->fileName]]
            )->first();

            if ($log) {
                LogImport::where([
                        ['user_id', $this->user->id],
                        ['file_name', $this->fileName]]
                )->update(['messages' => array_merge(json_decode($log->messages), $dataError)]);
            } else {
                LogImport::create([
                    'user_id' => $this->user->id,
                    'messages' => json_encode($dataError),
                    'file_name' => $this->fileName,
                ]);
            }
            DB::afterCommit(function () use ($mailSend) {
                foreach ($mailSend as $row) {
                    Mail::to($row['email'])
                        ->queue(new RegisterUserMail([
                            'email' => $row['email'],
                            'password' => $row['password'],
                        ]));
                }
            });
            DB::commit();
            return;
        } catch (\Exception $e) {
            Log::error('error: ' . $e->getMessage());
            DB::rollBack();
            return;
        }
    }

    public function userImportRulesValidation(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'role' => new Enum(RoleEnums::class)
        ];
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
