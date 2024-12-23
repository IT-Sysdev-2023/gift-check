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
        // dd();
        $isExists = Store::where([['has_local', 1], ['store_id', $request->selectedStore]])->exists();
       
        if ($isExists) { //OTHER SERVER
            VerifiedGcReport::dispatch($request->all(), self::getServerDatabase($request->selectedStore, false));

        } else { //LOCAL

            VerifiedGcReport::dispatch($request->all(), self::getServerDatabase($request->selectedStore, true));
        }
    }
    private static function getServerDatabase(string| int $store, bool $islocal)
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