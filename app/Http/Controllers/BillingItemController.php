<?php

namespace App\Http\Controllers;

use App\Models\BillingItem;
use App\Http\Requests\StoreBillingItemRequest;
use App\Http\Requests\UpdateBillingItemRequest;

class BillingItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreBillingItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BillingItem $billingItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BillingItem $billingItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBillingItemRequest $request, BillingItem $billingItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BillingItem $billingItem)
    {
        //
    }
}
