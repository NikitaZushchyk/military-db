<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Log extends Model
{
    use Searchable;

    public $timestamps = false;

    protected $fillable = [
        'service',
        'action',
        'description',
        'created_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function searchableAs(): string
    {
        return 'logs';
    }

    public function toSearchableArray(): array
    {
        return [
            'service' => $this->service,
            'action' => $this->action,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
