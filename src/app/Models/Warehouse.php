<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Warehouse extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['serial_number', 'equipment_type_id', 'status'];

    public function type()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'serial_number' => (string)$this->serial_number,
            'status' => $this->status,
            'equipment_type_id' => $this->equipment_type_id,
        ];
    }
}
