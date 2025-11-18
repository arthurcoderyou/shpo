<?php

namespace App\Http\Controllers;

use App\Models\ReReviewRequest;
use Illuminate\Http\Request;

class ReReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.rereview.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ReReviewRequest $reReviewRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReReviewRequest $reReviewRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReReviewRequest $reReviewRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReReviewRequest $reReviewRequest)
    {
        //
    }
}
