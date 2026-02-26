<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LogImport extends Model
{
    use HasFactory;

    protected $table = 'log_import';

    protected $fillable = [
        'user_id',
        'messages',
        'file_name',
        'total_row',
        'row_fail',
        'row_success',
        'job_tracking_id'
    ];

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobTracking(): BelongsTo
    {
        return $this->belongsTo(JobTracking::class);
    }
}
