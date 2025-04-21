<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {

        return view('dashboard.default', ['name' => auth()->user()->first_name, 'date' => auth()->user()->created_at->format('Y-m-d H:i:s')]);
    }
}
