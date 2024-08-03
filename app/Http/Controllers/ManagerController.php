<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    //
    public function managersKey(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'success',
                'title' => 'Success',
                'msg' => 'Successfully Validated'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'title' => 'Opps Error',
                'msg' => 'Invalid credentials'
            ]);
        }
    }
}
