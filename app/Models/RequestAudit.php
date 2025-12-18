<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestAudit extends Model
{
    protected $fillable = 
    [
        'trace_id',
        'user_id',
        'method',
        'path',
        'status_code',
        'duration_ms',
        'ip',
        'user_agent'
    ];
}
