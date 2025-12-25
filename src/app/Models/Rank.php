<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $fillable = ['name'];

    public function soldiers()
    {
        return $this->hasMany(Soldier::class);
    }
}
