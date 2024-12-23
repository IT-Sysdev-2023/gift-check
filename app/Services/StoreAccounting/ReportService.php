<?php

namespace App\Services\StoreAccounting;
use App\Exports\StoreAccounting\VerifiedGcReportMultiExport;
use App\Jobs\StoreAccounting\VerifiedGcReport;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Services\Documents\ExportHandler;
use App\Services\Documents\ImportHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function verifiedGcYearlySubmit(Request $request)
    {
        $isExists = Store::where([['has_local', 1], ['store_id', $request->selectedStore]])->exists();

        if ($isExists) { //OTHER SERVER
            $server = self::getServerDatabase($request->selectedStore, false);

            if (self::checkReveriedData($server, $request->selectedStore, $request->year)) {

                VerifiedGcReport::dispatch($request->all(), $server);
                
            } else {
                return response()->json('No record Found on this date', 404);
            }

        } else { //LOCAL

            $server = self::getServerDatabase($request->selectedStore, true);
            if (self::checkReveriedData($server, $request->selectedStore, $request->year)) {
                VerifiedGcReport::dispatch($request->all(), $server);
            } else {
                return response()->json('No record Found on this date', 404);
            }
        }
    }

    public static function checkReveriedData($db, $store, $year)
    {
        return DB::connection($db)->table('store_verification')
            ->leftJoin('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->whereYear('vs_date', $year)
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