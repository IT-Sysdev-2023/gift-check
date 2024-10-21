<?php

namespace App\Services\Admin;

use App\Models\Denomination;
use App\Models\RequisitionForm;
use App\Models\RequisitionFormDenomination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DBTransaction
{
    public function createPruchaseOrders($request)
    {
        $tranDate = Carbon::createFromFormat('d/m/Y', $request->data['data']['transdate']);

        $transdateF = $tranDate->format('Y-m-d');

        $purDate =  Carbon::createFromFormat('d/m/Y', $request->data['data']['purdate']);

        $purDateF = $purDate->format('Y-m-d');

        dd($request->data['data']);

        DB::transaction(function () use ($request, $transdateF, $purDateF) {

            RequisitionForm::create([
                'req_no' => $request->reqno,
                'sup_name' => $request->data['data']['supname'],
                'mop' => $request->data['data']['mop'],
                'rec_no' =>  $request->data['data']['recno'],
                'trans_date' => $transdateF,
                'ref_no' => $request->data['data']['refno'],
                'po_no' => $request->data['data']['pon'],
                'pay_terms' => $request->data['data']['payterms'],
                'loc_code' => $request->data['data']['locode'],
                'pur_date' => $purDateF,
                'ref_po_no' => $request->data['data']['refpon'],
                'dep_code' => $request->data['data']['depcode'],
                'remarks' => $request->data['data']['remarks'],
                'prep_by' => $request->data['data']['prepby'],
                'check_by' => $request->data['data']['checkby'],
                'srr_type' => $request->data['data']['srrtype'],
            ]);

            collect($request->denom)->each(function ($item) use ($request) {

                if ($item['qty'] !== '0') {
                    RequisitionFormDenomination::create([
                        'form_id' => $request->reqno,
                        'denom_no' => $item['denom_fad_item_number'],
                        'quantity' => $item['qty'],
                    ]);
                };

            });
        });
        dd();

        return back()->with([
            'title' => 'Success',
            'msg' => 'Successfully Added Po Details',
            'status' => 'success'
        ]);
    }
}
