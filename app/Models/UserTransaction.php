<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTransaction extends Model
{
    use HasFactory;

    public function sendBy()
    {
        return $this->belongsTo(User::class, 'send_by');
    }

    public function sendTo()
    {
        return $this->belongsTo(User::class, 'send_to');
    }
}
