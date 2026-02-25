<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LogExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_tracking_id',
        'file_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobTracking(): BelongsTo
    {
        return $this->belongsTo(JobTracking::class);
    }
}
