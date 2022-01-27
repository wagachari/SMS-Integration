<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    // protected $table = "sms";
    protected $fillable = [
        'source_address',
        'dst_address',
        'message',
        'message_id',
        'amount',
        'sent_at',
        'delivered_at',
        'response_message',
        'response_desc',
    ];
}
