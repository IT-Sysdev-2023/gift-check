<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function searchEmployee(Request $request)
    {
        // dd($request->all());
        $query = DB::connection('pis')
            ->table('employee3')
            ->join('applicant', 'app_id', '=', 'emp_id')
            ->where('current_status', 'active');
        // ->where('tag_as', 'new');

        if ($request->filled('firstname')) {
            $query->where('firstname', $request->firstname);
        }

        if ($request->filled('lastname')) {
            $query->where('lastname', $request->lastname);
        }

        if ($request->filled('middlename')) {
            $query->where('middlename', $request->middlename);
        }

        if ($request->filled('school')) {
            $query->where('school', $request->school);
        }

        if ($request->filled('employee_type')) {
            $query->where('emp_type', $request->employee_type);
        }

        if ($request->filled('startdate')) {
            $formatDate = date('Y-m-d', strtotime($request->startdate));
            $query->where('startdate', $formatDate);
        }

        if ($request->filled('year')) {
            $yearFormat = date('Y', strtotime($request->year));
            $query->whereYear('year', $yearFormat);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        $result = $query->get();
        // dd($result);

        $data = $result->map(function ($item) {
            return array_map(function ($value) {
                return is_string($value) ? mb_convert_encoding($value, 'UTF-8', 'UTF-8') : $value;
            }, (array) $item);
        });
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


}
