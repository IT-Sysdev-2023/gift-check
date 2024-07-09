<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    //

    public function index()
    {
        return Inertia::render('Admin/AdminDashboard');
    }
    public function scanGcStatusIndex()
    {
        return Inertia::render('Admin/ScanGcStatuses');
    }
}
