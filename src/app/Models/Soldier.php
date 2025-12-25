<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soldier extends Model
{
    use HasFactory;
    protected $fillable = ['first_name', 'last_name', 'rank_id', 'unit_id', 'status'];

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function duties()
    {
        return $this->hasMany(DutyRoster::class);
    }
}
