<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('ciudadano.dashboard');
    }
}