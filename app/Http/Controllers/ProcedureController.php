<?php

namespace App\Http\Controllers;

use App\Models\LabProcedure;
use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    public function index()
    {
        $procedures = LabProcedure::orderBy('sort_order')->get();
        return view('procedures.index', compact('procedures'));
    }
}