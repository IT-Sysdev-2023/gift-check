<?php

namespace App\Http\Controllers;

use App\Models\Assignatory;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class QueryFilterController extends Controller
{

    public function getCheckBy(Request $request)
    {
        return response()->json(
            Assignatory::whereIn('assig_dept', [$request->user()->usertype, 1])->get()
        );
    }
    public function customer(Request $request)
    {
        $result = Customer::whereRaw("CONCAT(cus_lname, ' ', cus_fname) LIKE ?", ['%' . $request->search . '%'])
            ->orWhereRaw("CONCAT(cus_fname, ' ', cus_lname) LIKE ?", ['%' . $request->search . '%'])
            ->orWhereRaw('cus_mname LIKE ?', ['%' . $request->search . '%'])
            ->orWhereRaw('cus_namext LIKE ?', ['%' . $request->search . '%'])
            ->get();
        if ($result->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No customer found',
            ]);
        }
        $formattedResult = $result->map(function ($item) {
            return [
                'value' => $item->cus_id,
                'label' => trim("{$item->cus_lname}, {$item->cus_fname} {$item->cus_mname} {$item->cus_namext}")
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $formattedResult
        ]);
    }

    public function addCustomer(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'extension' => 'nullable|string|max:10',
        ]);

        $customer = Customer::create([
            'cus_fname' => $request->firstname,
            'cus_lname' => $request->lastname,
            'cus_mname' => $request->middlename ?? null,
            'cus_namext' => $request->extension ?? null,
            'cus_store_register' => 1,
            'cus_register_by' => $request->user()->user_id,
        ]);

        return back()->with([
            'success' => 'Customer added successfully',
            'data' => ['cus_id' => $customer->cus_id]
        ]);
    }
}
