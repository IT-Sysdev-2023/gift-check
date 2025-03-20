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

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if ($user && $user->user_status == 'active') {
                    if (($request->store_assigned == $user->store_assinged) || ($user->usertype == $request->usertype)) {
                        if ($user->user_role) {
                            return response()->json([
                                'status' => 'success',
                                'title' => 'Success',
                                'msg' => 'Access Granted!'
                            ]);
                        } else {
                            return response()->json([
                                'status' => 'error',
                                'title' => 'Error',
                                'msg' => 'Access Denied!'
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'title' => 'Error',
                            'msg' => 'This account is not Authorizes!'
                        ]);
                    }
                } else {

                    return response()->json([
                        'status' => 'error',
                        'title' => 'Opps Error',
                        'msg' => 'Manager Account is Inactive'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Incorrect',
                    'msg' => 'The Password is Incorrect'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'Manager Credentials Do not Exists!'
            ]);
        }
    }
}
