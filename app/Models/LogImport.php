<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogImport extends Model
{
    use HasFactory;

    protected $table = 'log_import';

    protected $fillable = [
        'user_id',
        'messages',
        'file_name',
    ];

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
