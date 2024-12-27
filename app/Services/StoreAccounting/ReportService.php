<?php

namespace App\Services\StoreAccounting;
use App\DatabaseConnectionService;
use App\Exports\StoreAccounting\StoreGcPurchasedReportExport;
use App\Exports\StoreAccounting\VerifiedGcReportMultiExport;
use App\Jobs\StoreAccounting\StoreGcPurchasedReport;
use App\Jobs\StoreAccounting\VerifiedGcReport;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Services\Documents\ExportHandler;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Documents\ImportHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReportService extends DatabaseConnectionService
{

    public function __construct(protected DatabaseConnectionService $databaseConnectionService)
    {
    }
    public function verifiedGcYearlySubmit(Request $request)
    {
        $isExists = Store::where([['has_local', 1], ['store_id', $request->selectedStore]])->exists();

        $isMonthtly = isset($request->month) ? $request->month : null;

        if ($isExists) { //OTHER SERVER
            $server = self::getServerDatabase($request->selectedStore, false);

            if (self::checkReveriedData($server, $request->selectedStore, $request->year, $isMonthtly)) {
                VerifiedGcReport::dispatch($request->all(), $server);
            } else {
                return response()->json('No record Found on this date', 404);
            }

        } else { //LOCAL

            $server = self::getServerDatabase($request->selectedStore, true);
            if (self::checkReveriedData($server, $request->selectedStore, $request->year, $isMonthtly)) {
                VerifiedGcReport::dispatch($request->all(), $server);
            } else {
                return response()->json('No record Found on this date', 404);
            }
        }
    }

    private static function checkReveriedData($db, $store, $year, $month)
    {
        return DB::connection($db)->table('store_verification')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $year)
            ->when(!is_null($month), fn($q) => $q->whereMonth('vs_date', $month))
            ->where('vs_store', $store)
            ->exists();
    }

    public function billingMonthlyReport(Request $request)
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

                if (self::checkBillingMonthlyReport($request->selectedStore, $request->year, $isMonthtly, false)) {
  
                    StoreGcPurchasedReport::dispatch($request->all(), 1);
                    // dd(1);
    

                } else {
                    return response()->json('No record Found on this date', 404);
                }

            } else { //LOCAL

                if (self::checkBillingMonthlyReport($request->selectedStore, $request->year, $isMonthtly, true)) {
                    // dd(2);
                    StoreGcPurchasedReport::dispatch($request->all(), 2);
                } else {
                    return response()->json('No record Found on this date', 404);
                }
            }

        }

        // dd($request->toArray());
    }

    private function checkBillingMonthlyReport($store, $year, $month, $isLocal)
    {
        $server = $this->databaseConnectionService->getLocalConnection($isLocal, $store);
        return $server->table('store_verification')
            ->join('store_eod_textfile_transactions', 'store_eod_textfile_transactions.seodtt_barcode', '=', 'store_verification.vs_barcode')

            ->where(
                fn($query) =>
                $query->whereYear('vs_date', $year)
                    ->when(!is_null($month), fn($q) => $q->whereMonth('vs_date', $month))
            )
            ->orWhere(
                fn($query) =>
                $query->whereYear('vs_reverifydate', $year)
                    ->when(!is_null($month), fn($q) => $q->whereMonth('vs_reverifydate', $month))
            )
            ->where('vs_store', $store)
            ->exists();
    }
    private static function getServerDatabase(string|int $store, bool $islocal)
    {

        $lserver = StoreLocalServer::where('stlocser_storeid', $store)
            ->value('stlocser_ip');

        $parts = collect(explode('.', $lserver));
        $result = $islocal ? '' : '-' . $parts->slice(2)->implode('.');

        return 'mariadb' . $result;
    }

    public function generatedReports(Request $request)
    {
        $getFiles = (new ImportHandler())->getFileReports($request->user()->usertype);
        return inertia('Treasury/Reports/GeneratedReports', [
            'files' => $getFiles
        ]);
    }
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