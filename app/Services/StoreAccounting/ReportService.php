<?php

namespace App\Services\StoreAccounting;
use App\Exports\StoreAccounting\StoreGcPurchasedReportExport;
use App\Exports\StoreAccounting\VerifiedGcReportMultiExport;
use App\Jobs\StoreAccounting\StoreGcPurchasedReport;
use App\Jobs\StoreAccounting\VerifiedGcReport;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Services\Documents\ExportHandler;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Documents\ImportHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;


class ReportService 
{

    const REMOTE_SERVER_DB = false;
    const LOCAL_DB = true;
    public function verifiedGcYearlySubmit(Request $request)
    {
        $isExists = Store::where([['has_local', 1], ['store_id', $request->selectedStore]])->exists();

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

                if (ReportsHelper::checkRemoteDbReport($request->selectedStore, $request->year, $isMonthtly, self::REMOTE_SERVER_DB)) {
                  
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
            "month" => 'required',
            "year" => 'required',
            "selectedStore" => 'required',
            "SPGCDataType" => "required"
        ]);
        
        if($request->SPGCDataType === 'srv'){
            
        }
        dd($request->toArray());
    }

    public function generatedReports(Request $request)
    {
        $getFiles = (new ImportHandler())->getFileReports($request->user()->usertype);
        return inertia('Treasury/Reports/GeneratedReports', [
            'files' => $getFiles
        ]);
    }


    
    // private static function getServerDatabase(string|int $store, bool $islocal)
    // {

    //     $lserver = StoreLocalServer::where('stlocser_storeid', $store)
    //         ->value('stlocser_ip');

    //     $parts = collect(explode('.', $lserver));
    //     $result = $islocal ? '' : '-' . $parts->slice(2)->implode('.');

    //     return 'mariadb' . $result;
    // }
    
    private static function getStoreVerification($model, Request $request)
    {
        return $model->where(fn($q) =>
            $q->whereYear('vs_date', $request->year)
                ->orWhereYear('vs_reverifydate', $request->year))
            ->where('vs_store', $request->selectedStore)
            ->when($request->user()->username === 'flora2', function (Builder $builder) {
                $builder->where('vs_gctype', 3);
            })
            ->limit(10)
            ->get();
    }
}