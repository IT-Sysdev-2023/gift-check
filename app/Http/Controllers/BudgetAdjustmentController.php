<?php

namespace App\Http\Controllers;

use App\Models\AllocationAdjustment;
use App\Models\BudgetAdjustment;
use App\Models\BudgetRequest;
use App\Models\GcAdjustment;
use Illuminate\Http\Request;

class BudgetAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bud = BudgetAdjustment::count();
        $production = GcAdjustment::count();
        $allocate = AllocationAdjustment::count();



        dd($bud);
    }

    public function countAdj(){
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
    public function show(BudgetAdjustment $budgetAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BudgetAdjustment $budgetAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BudgetAdjustment $budgetAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BudgetAdjustment $budgetAdjustment)
    {
        //
    }
}
