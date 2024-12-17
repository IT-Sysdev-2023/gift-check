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
        $verifiedData = $this->perDay($this->verifiedMonthly);
        // dd($verifiedData);
        return collect($verifiedData);
    }

    private function perDay($request)
    {
        $dataType = $request['dataTypeMonthly'];
        $storeId = $request['selectedStoreMonthly'];
        $month = $request['month'];
        $year = $request['yearMonthly'];
        $data = [];

        if ($dataType === 'verifiedGC') {
            $connection = $this->getConnection($storeId);

            if ($connection) {
                $data = $this->getVerifiedDateByMonthAndYear($connection, $month, $year, $storeId);
            } else {
                $data = $this->getVerifiedDateByMonthAndYear(DB::connection(), $month, $year, $storeId);
            }
        }

        return $data;
    }

    private function getConnection($storeId)
    {
        $store = DB::table('stores')->where('store_id', $storeId)->first();

        if ($store && $store->has_local) {
            $localServer = DB::table('store_local_server')->where('stlocser_storeid', $storeId)->first();

            if ($localServer) {
                return $this->connectToLocalServer($localServer);
            }
        }

        return null; 
    }

    private function connectToLocalServer($localServer)
    {
        return @localserver(
            $localServer->stlocser_ip,
            $localServer->stlocser_username,
            $localServer->stlocser_password,
            $localServer->stlocser_db
        );
    }

    private function getVerifiedDateByMonthAndYear($connection, $month, $year, $storeId)
    {
        $query = $connection->table('store_verification')
        ->where(function ($query) use ($month, $year) {
            $query->whereYear('vs_date', $year)
                ->whereMonth('vs_date', $month)
                ->orWhereYear('vs_reverifydate', $year)
                ->whereMonth('vs_reverifydate', $month);
        })
            ->where('vs_store', $storeId);

        return $query->get();
    }
    
}
