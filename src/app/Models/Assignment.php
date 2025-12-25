<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['soldier_id', 'warehouse_id', 'issue_date', 'return_date'];

    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }

    public function item()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
