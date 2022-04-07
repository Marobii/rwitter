<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    //Connect to correct table
    protected $table = 'useractivity';
    protected $fillable = [
        'message',
    ];

}
