<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobExecution extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'job',
        'details',
        'params',
        'batch_id',
    ];

    protected $casts = [
        'params' => 'array',
    ];

    public function logs()
    {
        return $this->hasMany(JobExecutionLog::class);
    }
}
