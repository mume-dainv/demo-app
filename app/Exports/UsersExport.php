<?php

namespace App\Exports;

use App\Enums\JobStatusEnum;
use App\Models\JobTracking;
use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Queue\InteractsWithQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Excel;

class UsersExport implements FromCollection, WithHeadings, WithMapping, Responsable, WithEvents
{
    use Exportable, InteractsWithQueue;

    public $writerType = Excel::CSV;

    public function __construct(protected $conditions, protected $user, protected $jobName)
    {

    }

    public function collection()
    {
        $queries = User::with(['userLogging' => fn($q) => $q->latest('updated_at')]);
        if (count($this->conditions) > 0) {
//            Apply if many condition -> in example use name like
            if (isset($this->conditions['name_like'])) {
                $queries->where('name', 'like', '%' . $this->conditions['name_like'] . '%');
            }
            if (isset($this->conditions['role'])) {
                $queries->where('role', $this->conditions['role']);
            }
        }
        return $queries->get();
    }


    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Email',
            'Last login',
        ];
    }

    public function map($row): array
    {
        $logging = $row->userLogging()->orderBy('updated_at', 'DESC')->first();
        return [
            $row->id,
            $row->name,
            $row->email,
            $logging ? $logging->updated_at->format('d-m-Y H:i:s') : null,
        ];
    }

     public function registerEvents(): array
     {
         return [ImportFailed::class => function (ImportFailed $event) {
             JobTracking::where([
                 'user_id' => $this->user->id,
                 'job_name' => $this->jobName
             ])->update([
                 'status' => JobStatusEnum::Failed->value
             ]);
         }];
     }
}
