<?php

namespace App\Http\Controllers;

use App\Models\SpecialExternalGcrequest;
use App\Services\SpecialExternalGcRequestService;
use Illuminate\Http\Request;

class SpecialExternalGcrequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() //for the <a href="#/special-external-request-approved/"> Approved GC
    {
        $approvedGc = SpecialExternalGcRequestService::approvedGc();


        return $approvedGc;
    }




    /**
     * Show the form for creating a new resource.
     */
    public function viewApprovedGcRecord()
    {
        //
        dd();
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
    public function show(SpecialExternalGcrequest $specialExternalGcrequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpecialExternalGcrequest $specialExternalGcrequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpecialExternalGcrequest $specialExternalGcrequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpecialExternalGcrequest $specialExternalGcrequest)
    {
        //
    }
}
