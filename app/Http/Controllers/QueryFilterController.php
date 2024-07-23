<?php

namespace App\Http\Controllers;

use App\Models\Assignatory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueryFilterController extends Controller
{

    public function getCheckBy(Request $request)
    {
        return response()->json(
            Assignatory::whereIn('assig_dept', [$request->user()->usertype, 1])->get()
        );
    }
}
