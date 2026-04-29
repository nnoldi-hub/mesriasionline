<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicJobRequestResponse extends Model
{
    protected $fillable = [
        'public_job_request_id',
        'craftsman_id',
        'action',
        'message',
    ];

    public function jobRequest()
    {
        return $this->belongsTo(PublicJobRequest::class, 'public_job_request_id');
    }

    public function craftsman()
    {
        return $this->belongsTo(User::class, 'craftsman_id');
    }
}
