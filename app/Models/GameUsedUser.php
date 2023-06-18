<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameUsedUser extends Model
{
    use HasFactory;

    public $fillable = ['name', 'email', 'avatar', 'facebook_id', 'text_data', 'shared', 'photo'];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
