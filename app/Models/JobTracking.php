<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\JobHelper;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobTracking extends Model
{
    use HasFactory;

    protected $table = 'job_trackings';

    protected $fillable = [
        'user_id',
        'job_name',
        'status',
        'log_import_id'
    ];

    public function jobName(): Attribute
    {
        return Attribute::make(fn($value) => JobHelper::getJobName($value));
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function logImport() : HasOne
    {
        return $this->hasOne(LogImport::class);
    }

    public function logExport() : hasOne
    {
        return $this->hasOne(LogExport::class);
    }
}
