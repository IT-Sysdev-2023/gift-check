<?php

namespace App\Exports\VerifiedGCReportMonthly;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class verifiedReportMonthly implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $verifiedMonthly;

    public function __construct($request)
    {
        // dd($this->verifiedMonthly);
        $this->verifiedMonthly = $request;
    }

    public function title(): string
    {
        return 'Per Day';
    }

    public function headings(): array
    {
        return [
            'DATE',
            'BARCODE',
            'DENOMINATION',
            'AMOUNT REDEEM',
            'BALANCE',
            'CUSTOMER NAME',
            'BUSINESS UNIT',
            'TERMINAL #',
            'VALIDATION',
            'GC TYPE',
            'DATETIME GENERATION'
        ];
    }
    public function collection()
    {
        // dd($this->verifiedMonthly);
        $verifiedMonthlydata = $this->perDay($this->verifiedMonthly);
        // dd($verifiedMonthlydata);

        return collect($verifiedMonthlydata);
    }

    private function perDay($request)
    {
        $dataType = $request['dataTypeMonthly'];
        $storeId = $request['selectedStoreMonthly'];
        $month = $request['month'];
        $year = $request['yearMonthly'];
        
        $data = [];

        if ($dataType === 'verifiedGC') {
            

            $hasLocal = DB::table('stores')
                ->where('store_id', $storeId)
                ->where('has_local', 1)
                ->exists();

            if ($hasLocal) {
                $localServer = DB::table('store_local_server')
                    ->where('stlocser_storeid', $storeId)
                    ->first();

                if ($localServer) {
                    $localConnection = $this->connectToLocalServer($localServer);

                    if ($localConnection) {
                        $data = $this->getVerifiedDataByMonthAndYear($localConnection, $month, $year, $storeId);


                    }
                }
            } else {
                $data = $this->getVerifiedDataByMonthAndYear(DB::connection(), $month, $year, $storeId);
            }
        }

        return $data;
    }

    private function connectToLocalServer($localServer)
    {
        try {
            return DB::connection([
                'driver' => 'mysql',
                'host' => $localServer->stlocser_ip,
                'username' => $localServer->stlocser_username,
                'password' => $localServer->stlocser_password,
                'database' => $localServer->stlocser_db,
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getVerifiedDataByMonthAndYear($connection, $month, $year, $storeId)
    {

        return $connection->table('store_verification')
        ->where(function ($query) use ($year, $month) {
            $query->whereYear('vs_date', $year)
            ->whereMonth('vs_date', $month)
                ->orWhere(function ($subQuery) use ($year, $month) {
                    $subQuery->whereYear('vs_reverifydate', $year)
                    ->whereMonth('vs_reverifydate', $month);
                });
        })
        ->where('vs_store', $storeId)
        ->get();
    }
}
