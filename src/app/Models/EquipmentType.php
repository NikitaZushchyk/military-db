<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(Warehouse::class);
    }
}
