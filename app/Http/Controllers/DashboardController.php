<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExternalForm;
use App\Models\InternalForm;
use App\Models\InternalFormResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalExternalForms = ExternalForm::count();
        $totalInternalForms = InternalForm::count();
        $totalForms = $totalExternalForms + $totalInternalForms;
        $totalResponses = InternalFormResponse::count();

        $recentUsers = User::latest()->take(5)->get();
        $recentExternalForms = ExternalForm::with('creator')->latest()->take(5)->get();
        $recentInternalForms = InternalForm::with('creator')->latest()->take(5)->get();
        $recentResponses = InternalFormResponse::with(['form', 'user'])->latest()->take(10)->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'totalExternalForms',
            'totalInternalForms',
            'totalForms',
            'totalResponses',
            'recentUsers',
            'recentExternalForms',
            'recentInternalForms',
            'recentResponses'
        ));
    }
}
