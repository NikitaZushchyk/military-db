<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Soldier extends Model
{
    use HasFactory, Searchable;
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
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'status' => $this->status,
            'rank_id' => $this->rank_id,
            'unit_id' => $this->unit_id,
        ];
    }
}
