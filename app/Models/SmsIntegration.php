<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsIntegration extends Model
{
    protected $fillable = [
        'source_address',
        'dst_address',
        'message',
        'message_id',
        'amount',
        'response_message',
        'response_desc',
    ];
}
