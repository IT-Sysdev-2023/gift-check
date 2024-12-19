<?php

namespace App\Services\StoreAccounting;
use App\Exports\StoreAccounting\VerifiedGcReportMultiExport;
use App\Jobs\StoreAccounting\VerifiedGcReport;
use App\Models\Store;
use App\Models\StoreLocalServer;
use App\Models\StoreVerification;
use App\Services\Documents\ExportHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReportService{
    public function verifiedGcYearlySubmit(Request $request)
    {
        // dd();
        // $isExists = Store::where([['has_local', 1], ['store_id', $request->selectedStore]])->exists();

        // if ($isExists) {
        //     $lserver = StoreLocalServer::where('stlocser_storeid', $request->selectedStore)
        //         ->value('stlocser_ip');

        //     $parts = collect(explode('.', $lserver));
        //     $result = $parts->slice(2)->implode('.');

        //     $server = DB::connection('mariadb-' . $result)->table('store_verification');
        //     $data = self::getStoreVerification($server, $request);
        // } else {
        //     $data = self::getStoreVerification(new StoreVerification, $request);
        // }

      

        VerifiedGcReport::dispatch($request->all());
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