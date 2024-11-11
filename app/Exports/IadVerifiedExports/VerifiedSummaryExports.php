<?php

namespace App\Exports\IadVerifiedExports;

use App\Models\StoreVerification;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VerifiedSummaryExports implements FromCollection, WithTitle, WithEvents, WithStyles, WithHeadings
{

    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    protected $requestData;

    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }
    public function title(): string
    {
        return 'By Month Summary per day';
    }
    public function headings(): array
    {
        return [
            'Date',
            'Barcode',
            'Denomination',
            'Amount Redeem',
            'Balance',
            'Customer Name',
            'Business Unit',
            'Terminal #',
            'Validation',
            'Gc Type',
            'Date',
            'Time',
        ];
    }

    public function collection()
    {
        dd($this->getDataStoreVerifivation());
        $data[] = [
            'data' => 'kanding',
            'kanding' => 'kanding',
        ];

        return collect($data);
    }
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $sheet->setCellValue('A1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('A2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $sheet->setCellValue('A3', 'MONTHLY REPORT ON GIFT CHECK (GC)');
                $sheet->setCellValue('A4', 'PERIOD COVER: JANUARY 1 - 31, 2019');
                $sheet->setCellValue('A6', 'BUSINESS UNITS: ALTURAS MALL');
                $sheet->setCellValue('A7', '');
            },
        ];
    }

    private function getDataStoreVerifivation(): Builder
    {

        $request = $this->requestData;

        $vsdata = StoreVerification::select(
            'vs_date',
            'vs_cn',
            'vs_tf_denomination',
            'vs_tf_balance',
            'vs_tf_purchasecredit'
        )->with('customer:cus_id,cus_fname,cus_mname,cus_namext,cus_lname')
            ->when(str_contains($request['date'], '-'), function ($q) use ($request) {
                $q->whereLike('vs_date', '%' . $request['date'] . '%')
                    ->orWhereLike('vs_reverifydate', '%' . $request['date'] . '%');
            }, function ($q) use ($request) {
                $q->whereYear('vs_date', $request['date'])
                    ->orWhere('vs_reverifydate', fn($q) => $q->whereYear('vs_reverifydate', $request['date']));
            })
            ->where('vs_store', $request['store'])
            ->get()
            ->groupBy('vs_date');


        $revdata = StoreVerification::select(
            'vs_date',
            'vs_cn',
            'vs_tf_denomination',
            'vs_reverifydate',
            'vs_tf_balance',
            'vs_tf_purchasecredit'
        )->with('customer:cus_id,cus_fname,cus_mname,cus_namext,cus_lname')
            ->when(str_contains($request['date'], '-'), function ($q) use ($request) {
                $q->whereLike('vs_reverifydate', '%' . $request['date'] . '%');
            }, function ($q) use ($request) {
                $q->whereYear('vs_reverifydate', $request['date']);
            })
            ->where('vs_store', $request['store'])
            ->get()
            ->groupBy('vs_date');

        $array = [];

        $vsdata->transform(function ($item, $key) use (&$array) {
            // dd($item->sum('vs_tf_denomination'));
            $array[] = [
                'date' => $key,
                'totalver' => $item->sum('vs_tf_denomination'),
                'balance' => $item->sum('vs_tf_balance'),
                'redeem' => $item->sum('vs_tf_purchasecredit'),
            ];

            return $array;
        });

        $revdata->transform(function ($item, $itemkey) use (&$array) {

           return collect($array)->each(function ($each, $eachkey) use (&$item, &$itemkey) {

                if ($itemkey === $each['date']) {

                    $total = $item->sum('vs_tf_denomination') + $each['totalver'];
                    $totalbal = $item->sum('vs_tf_balance') + $each['balance'];
                    $totalred = $item->sum('vs_tf_purchasecredit') + $each['redeem'];

                    $each[$eachkey]['totalver'] = $total;
                    $each[$eachkey]['balance'] = $totalbal;
                    $each[$eachkey]['redeem'] = $totalred;

                    return false;
                }

                return $each;
            });

            return $array;
        });

        dd($array);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(8)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        return [
            'A1' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
            'A2' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
            'A3' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
            'A4' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
            'A6' => [
                'font' => [
                    'bold' => true,
                    'name' => 'Fira Code',
                    'size' => 8,
                ],
            ],
        ];
    }
}
