<?php

namespace App\Http\Controllers;

use App\Models\Soldier;
use App\Services\SoldierService;
use Illuminate\Http\Request;

class SoldierController extends Controller
{
    public function __construct(SoldierService $soldierService)
    {
        $this->soldierService = $soldierService;
    }

    public function index(){}
    public function store(){}
    public function show(){}
    public function update(){}
    public function delete(){}
}
