<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::get();

        // dd(->toArray());
        return inertia('SearchEmployee', [
            'users' => $users->transform(function ($name) {
                $name->value = $name->emp_id;
                $name->label = $name->full_name;

                return $name;
            })
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function getEmp(Request $request)
    {
        $data = Http::timeout(5)->get(config('app.hrms_employee_api'), $request->q)->json();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addEmp(Request $request, User $id)
    {
        $request->validate([
            'api' => 'required'
        ]);

        // dd($id->user_id);
        if($id){
            UserDetails::create([
                'user_id' => $id->user_id,
                'details' => $request->api
            ]);

            return redirect()->back()->with('success', 'Update Successfully');
        }
        return redirect()->back()->with('error', 'Error Successfylly');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDetails $userDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDetails $userDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserDetails $userDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDetails $userDetails)
    {
        //
    }
}
