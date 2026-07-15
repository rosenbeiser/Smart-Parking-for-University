<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Show all applications (Breeze/Admin route).
     */
    public function applications()
    {
        // Stub: Redirect to existing AdminWebController's applications dashboard or return a simple view.
        return redirect()->route('admin.applications');
    }

    /**
     * Approve a parking application.
     */
    public function approve(Request $request, $id)
    {
        // Stub: Redirect to the existing review handler or mark approved
        return redirect()->back()->with('success', "Application #{$id} approved (stub).");
    }

    /**
     * Reject a parking application.
     */
    public function reject(Request $request, $id)
    {
        // Stub: Redirect to the existing review handler or mark rejected
        return redirect()->back()->with('info', "Application #{$id} rejected (stub).");
    }
}
