<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function equipment()
    {
        // Ambil semua kategori dari master
        $categories = EquipmentCategory::orderBy('name', 'asc')->get();

        // Ambil semua equipment dan kelompokkan berdasarkan kategori
        $equipments = Equipment::orderBy('sort_order', 'asc')->get()->groupBy('category');

        return view('facilities.equipment', compact('equipments', 'categories'));
    }
}
