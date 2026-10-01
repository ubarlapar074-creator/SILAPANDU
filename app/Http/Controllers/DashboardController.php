<?php
namespace App\Http\Controllers;

use App\Models\Registration;

class DashboardController extends Controller {
    public function index() {
        $totalRegistrations = Registration::count();
        $recentRegistrations = Registration::latest()->take(5)->get();

        return view('dashboard.index', compact('totalRegistrations', 'recentRegistrations'));
    }
}