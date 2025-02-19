<?php

namespace App\Services;

use App\Helpers\NumberHelper;
use App\Models\DtiDocument;
use App\Models\DtiGcRequest;
use App\Models\DtiGcRequestItem;
use App\Models\SpecialExternalCustomer;
use App\Services\Documents\FileHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DtiServices extends FileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'dtiExternalDocsRequest';
    }
    public function submissionForDti($request)
    {
        return DB::transaction(function () use ($request) {

            $latestId = self::getLatestId($request);

            $listofdenom = self::getDenomination($request, $latestId);

            $this->saveMultiFiles($request, $latestId, function ($id, $path) use ($request) {

                DtiDocument::create([
                    'dti_trid' => $id,
                    'dti_type' => 'Special External GC Request Dti',
                    'dti_fullpath' => $path
                ]);
            });

            return $this->dataForPdf($request, $listofdenom);
        });
    }

    private function dataForPdf(Request $request, $listOfDenom)
    {
        $gcMode = 'Special External Request Report Dti';
        $company = SpecialExternalCustomer::select('spcus_companyname', 'spcus_acctname')->find($request->customer);


        $amount = $listOfDenom->map(function ($item) {
            return $item['denomination'] * $item['qty'];
        })->sum();

        return [
            //Header
            'company' => [
                'name' => Str::upper('ALTURAS GROUP OF COMPANIES'),
                'department' => Str::title('Head Office - Treasury Department'),
                'report' => $gcMode,
            ],

            //SubHeader
            'subheader' => [
                'sgcReq' => $request->trans,
                'dateReceived' => today()->toFormattedDateString(),
                'customer' => $company->spcus_acctname,
                'accountName' => $company->spcus_companyname,
            ],

            'denom' => $listOfDenom,
            'totalGcQty' => $listOfDenom->sum('qty'),
            'totalGcAmount' => NumberHelper::format($amount),
            'receivedBy' => $request->user()->full_name
        ];
    }

    private function getLatestId($request)
    {
        $storing = DtiGcRequest::create([
            'dti_num' => $request->trans,
            'dti_reqby' => $request->user()->user_id,
            'dti_datereq' => now(),
            'dti_dateneed' => $request->date,
            'dti_remarks' => $request->remarks,
            'dti_company' => $request->customer,
            'dti_payment' => $request->amount,
            'dti_paymenttype' => "Ar",
            'dti_status' => 'pending',
            'dti_type' => 2,
            'dti_payment_stat' => 'paid',
            'dti_addemp' => 'pending',
            'dti_promo' => 'external',
            'dti_payment_arno' => $request->arNo
        ]);


        return $storing->dti_num;
    }
    private function getDenomination($request, $lid){

        $collect = collect($request->denomination);


        $collect->each(function ($denom) use ($lid) {
            DtiGcRequestItem::create([
                'dti_denoms' => $denom['denomination'],
                'dti_qty' => $denom['qty'],
                'dti_trid' => $lid
            ]);
        });

        return $collect;

    }
}
