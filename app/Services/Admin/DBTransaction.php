<?php

namespace App\Services\Admin;

use App\Helpers\NumberHelper;
use App\Models\Denomination;
use App\Models\RequisitionForm;
use App\Models\RequisitionFormDenomination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DBTransaction
{
    public function createPruchaseOrders($request)
    {

        DB::transaction(function () use ($request) {

            RequisitionForm::create([
                'req_no' => $request->reqno,
                'sup_name' => $request->record['data']['supname'],
                'mop' => $request->record['data']['mop'],
                'rec_no' =>  $request->record['data']['recno'],
                'trans_date' => self::formattedDate($request->record['data']['transdate']),
                'ref_no' => $request->record['data']['refno'],
                'po_no' => $request->record['data']['pon'],
                'pay_terms' => $request->record['data']['payterms'],
                'loc_code' => $request->record['data']['locode'],
                'pur_date' => self::formattedDate($request->record['data']['purdate']),
                'ref_po_no' => $request->record['data']['refpon'],
                'dep_code' => $request->record['data']['depcode'],
                'remarks' => $request->record['data']['remarks'],
                'prep_by' => $request->record['data']['prepby'],
                'check_by' => $request->record['data']['checkby'],
                'srr_type' => $request->record['data']['srrtype'],
            ]);



            collect($request->denom)->each(function ($item) use (&$request) {
                if ($item['qty'] !== '0') {
                    RequisitionFormDenomination::create([
                        'form_id' => $request->reqno,
                        'denom_no' => $item['denom_fad_item_number'],
                        'quantity' => NumberHelper::float($item['qty']),
                    ]);
                };
            });
        });

        return true;
    }

    private static function formattedDate($date)
    {

        $explode = explode('/', $date);

        return $explode[2] . '-' . $explode[0] . '-' . $explode[1];
    }
}
