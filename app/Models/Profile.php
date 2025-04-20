<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_id',
        'profile_picture',
        'bio',
        'social_link',
        'website'

    ];
    
}
