<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsIntegration extends Model
{
    protected $fillable = [
        'name', 'dst_address', 'message','message_id'
    ];
}
