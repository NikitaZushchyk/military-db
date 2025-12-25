<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyRoster extends Model
{
    protected $fillable = ['soldier_id', 'duty_type_id', 'start_time', 'end_time'];

    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }

    public function dutyType()
    {
        return $this->belongsTo(DutyType::class);
    }
}
