<?php

namespace App\Services\StoreAccounting;

use Carbon\Carbon;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\DatabaseConnectionService;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Query\Builder;
use App\Services\Documents\ExportHandler;
use App\Services\Documents\ImportHandler;
use App\Jobs\StoreAccounting\SpgcRedeemReport;
use App\Jobs\StoreAccounting\VerifiedGcReport;
use App\Jobs\StoreAccounting\StoreGcPurchasedReport;
use App\Services\Treasury\Reports\ReportHelper;
use Illuminate\Support\Facades\Log;

class ReportService
{

    const REMOTE_SERVER_DB = false;
    const LOCAL_DB = true;
    public function verifiedGcYearlySubmit(Request $request)
    {
        $isExists = Store::where([['has_local', 1], ['store_id', $request->selectedStore]])->exists();
        // dd($isExists);

        $isMonthtly = isset($request->month) ? $request->month : null;

        if ($isExists) { //OTHER SERVER

            if (ReportsHelper::checkReveriedData(self::REMOTE_SERVER_DB, $request->selectedStore, $request->year, $isMonthtly)) {
                VerifiedGcReport::dispatch($request->all(), self::REMOTE_SERVER_DB);
            } else {
                return response()->json('No record Found on this date', 404);
            }
        } else { //LOCAL
            if (ReportsHelper::checkReveriedData(self::LOCAL_DB, $request->selectedStore, $request->year, $isMonthtly)) {
                VerifiedGcReport::dispatch($request->all(), self::LOCAL_DB);
            } else {
                return response()->json('No record Found on this date', 404);
            }
        }
    }

    public function billingReport(Request $request)
    {
        $request->validate(
            [
                'year' => 'required',
                'month' => 'required_if:isMonthly,true',
                'selectedStore' => 'required',
                'StoreDataType' => 'required',
            ]
        );

        $isMonthtly = isset($request->month) ? $request->month : null;

        if ($request->StoreDataType === 'store-sales') {

            $isExists = Store::where([['has_local', 1], ['store_id', $request->selectedStore]])->exists();

            if ($isExists) { //OTHER SERVER

                if (ReportsHelper::checkRemoteDbBillingReport($request->selectedStore, $request->year, $isMonthtly, self::REMOTE_SERVER_DB)) {

                    StoreGcPurchasedReport::dispatch($request->all(), self::REMOTE_SERVER_DB);
                } else {
                    return response()->json('No record Found on this date', 404);
                }
            } else { //LOCAL
                if (ReportsHelper::checkLocalDbBillingReport($request->selectedStore, $request->year, $isMonthtly, self::LOCAL_DB)) {
                    StoreGcPurchasedReport::dispatch($request->all(), self::LOCAL_DB);
                } else {
                    return response()->json('No record Found on this date', 404);
                }
            }
        }
    }

    public function redeemReport(Request $request)
    {
        $request->validate([
            "year" => 'nullable',
            "selectedStore" => 'required',
            "SPGCDataType" => "required"
        ]);
        if ($request->SPGCDataType === 'srv') {

            $isExists = Store::where([['has_local', 1], ['store_id', $request->selectedStore]])->exists();


            if ($isExists) { //OTHER SERVER
                if (ReportsHelper::checkRedeemReport(self::REMOTE_SERVER_DB, $request->selectedStore, $request->year, $request->month, $request->day)) {
                    // dd();
                    SpgcRedeemReport::dispatch($request->all(), self::REMOTE_SERVER_DB);
                } else {
                    return response()->json('No record Found on this date', 404);
                }
            } else { //LOCAL

                if (ReportsHelper::checkRedeemReport(self::LOCAL_DB, $request->selectedStore, $request->year, $request->month, $request->day)) {
                    // dd();
                    // $this->dumm($request->all());
                    SPGCRedeemReport::dispatch($request->all(), self::LOCAL_DB);
                    // dd();
                } else {
                    return response()->json('No record Found on this date', 404);
                }
            }
        }
    }

    public function generatedReports(Request $request)
    {
        $getFiles = (new ImportHandler())->getFileReports($request->user()->usertype);
        return inertia('Treasury/Reports/GeneratedReports', [
            'files' => $getFiles
        ]);
    }

    public function billingReportPerDay(Request $request)
    {
        $validated = $request->validate([
            'dataType' => 'required|string',
            'storeSelected' => 'required',
            'dateSelected' => 'required|date'
        ]);

        $dataType = $validated['dataType'];
        $store = $validated['storeSelected'];
        $date = $validated['dateSelected'];

        if ($dataType === 'store-sales') {

            $isExists = Store::where([['has_local', 1], ['store_id', $store]])->exists();
            // dd($isExists);

            if ($isExists) { //Remote server

                $records = ReportsHelper::checkBillingReportPerDayRemote($store, $date, self::REMOTE_SERVER_DB);
                // dd($records);
                if (!$records->isEmpty()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Records found on ' . $date . '',
                        'data' => $records,
                        'count' => $records->count()
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'message' => 'No records found on ' . $date
                ]);
            }
            // proceed to local server if $isExists is false
            else {
                $records = ReportsHelper::checkBillingReportPerDayLocal($store, $date, self::LOCAL_DB);
                // dd($records);
                if (!$records->isEmpty()) {

                    return response()->json([
                        'success' => true,
                        'message' => 'Record found on  ' . $date . '',
                        'data' => $records,
                        'count' => $records->count()
                    ]);
                }
                return response()->json([
                    'error' => true,
                    'message' => 'No records found on ' . $date
                ]);
            }
        }
        return response()->json([
            'error' => true,
            'message' => 'No records found on both server'
        ]);
    }

    public function dumm($request)
    {
        $spgc =  StoreVerification::selectRaw(" DATE(store_verification.vs_date) as datever,
        DATE(store_verification.vs_reverifydate) as daterev,
        store_verification.vs_barcode,
        store_verification.vs_tf_denomination,
        store_verification.vs_tf_purchasecredit,
        store_verification.vs_tf_balance,
        special_external_gcrequest_emp_assign.spexgcemp_lname,
        special_external_customer.spcus_acctname,
        store_eod_textfile_transactions.seodtt_transno,
        store_eod_textfile_transactions.seodtt_bu,
        store_eod_textfile_transactions.seodtt_terminalno,
        store_verification.vs_store,
        store_verification.vs_gctype,
        store_verification.vs_date,
        customers.cus_fname,
        customers.cus_lname,
        customers.cus_mname,
        customers.cus_namext,
        store_verification.vs_time")
            ->join('special_external_gcrequest_emp_assign', 'special_external_gcrequest_emp_assign.spexgcemp_barcode', '=', 'store_verification.vs_barcode')
            ->join('special_external_gcrequest', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_emp_assign.spexgcemp_trid')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->join('store_eod_textfile_transactions', 'store_eod_textfile_transactions.seodtt_barcode', '=', 'store_verification.vs_barcode')
            ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $request['year'])
            ->when(isset($request['month']), fn($q) => $q->whereMonth('vs_date', $request['month']))
            ->where('vs_store', $request['selectedStore'])
            ->orderBy('vs_id')
            ->get();


     $first = $spgc->map(function ($item) {
            return [
                'datever' => $item->datever,
                'daterev' => $item->daterev,
                'vs_barcode' => $item->vs_barcode,
                'vs_tf_denomination' => $item->vs_tf_denomination,
                'vs_tf_purchasecredit' => $item->vs_tf_denomination,
                'vs_tf_balance' => $item->vs_tf_balance,
                "spexgcemp_lname" => $item->spexgcemp_lname,
                "spcus_acctname" => $item->spcus_acctname,
                "seodtt_transno" => $item->seodtt_transno,
                "seodtt_bu" => $item->seodtt_bu,
                "seodtt_terminalno" => $item->seodtt_terminalno,
                "vs_store" => $item->vs_store,
                "vs_gctype" => $item->vs_gctype,
                "vs_date" => $item->vs_date,
                "cus_fname" => $item->cus_fname,
                "cus_lname" => $item->cus_lname,
                "cus_mname" => $item->cus_mname,
                "cus_namext" => $item->cus_namext,
                "vs_time" => $item->vs_time,
            ];
        });
        // dd($spgc->toArray());

        $spgcdti = StoreVerification::selectRaw(" DATE(store_verification.vs_date) as datever,
        DATE(store_verification.vs_reverifydate) as daterev,
        store_verification.vs_barcode,
        store_verification.vs_tf_denomination,
        store_verification.vs_tf_purchasecredit,
        store_verification.vs_tf_balance,
        dti_barcodes.lname,
        special_external_customer.spcus_acctname,
        store_eod_textfile_transactions.seodtt_transno,
        store_eod_textfile_transactions.seodtt_bu,
        store_eod_textfile_transactions.seodtt_terminalno,
        store_verification.vs_store,
        store_verification.vs_gctype,
        store_verification.vs_date,
        customers.cus_fname,
        customers.cus_lname,
        customers.cus_mname,
        customers.cus_namext,
        store_verification.vs_time")
            ->join('dti_barcodes', 'dti_barcodes.dti_barcode', '=', 'store_verification.vs_barcode')
            ->join('dti_gc_requests', 'dti_gc_requests.dti_num', '=', 'dti_barcodes.dti_trid')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'dti_gc_requests.dti_company')
            ->join('stores', 'stores.store_id', '=', 'store_verification.vs_store')
            ->join('store_eod_textfile_transactions', 'store_eod_textfile_transactions.seodtt_barcode', '=', 'store_verification.vs_barcode')
            ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $request['year'])
            ->when(isset($request['month']), fn($q) => $q->whereMonth('vs_date', $request['month']))
            ->where('vs_store', $request['selectedStore'])
            ->orderBy('vs_id')
            ->get();

      $second = $spgcdti->map(function ($item) {
            return [
                'datever' => $item->datever,
                'daterev' => $item->daterev,
                'vs_barcode' => $item->vs_barcode,
                'vs_tf_denomination' => $item->vs_tf_denomination,
                'vs_tf_purchasecredit' => $item->vs_tf_denomination,
                'vs_tf_balance' => $item->vs_tf_balance,
                "spexgcemp_lname" => $item->spexgcemp_lname,
                "spcus_acctname" => $item->spcus_acctname,
                "seodtt_transno" => $item->seodtt_transno,
                "seodtt_bu" => $item->seodtt_bu,
                "seodtt_terminalno" => $item->seodtt_terminalno,
                "vs_store" => $item->vs_store,
                "vs_gctype" => $item->vs_gctype,
                "vs_date" => $item->vs_date,
                "cus_fname" => $item->cus_fname,
                "cus_lname" => $item->cus_lname,
                "cus_mname" => $item->cus_mname,
                "cus_namext" => $item->cus_namext,
                "vs_time" => $item->vs_time,
            ];
        });


        $merged = $first->merge($second);
    }
}
