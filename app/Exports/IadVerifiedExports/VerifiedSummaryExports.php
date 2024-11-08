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

        $data = StoreVerification::with('customer:cus_id,cus_fname,cus_mname,cus_namext,cus_lname')
            ->when(str_contains($request['date'], '-'), function ($q) use ($request) {
                $q->whereLike('vs_date', '%' . $request['date'] . '%')
                    ->orWhereLike('vs_reverifydate', '%' . $request['date'] . '%');
            }, function ($q) use ($request) {
                $q->whereYear('vs_date', $request['date'])
                ->orWhere('vs_reverifydate', fn ($q) => $q->whereYear('vs_reverifydate', $request['date']));
            })
            ->where('vs_store', $request['store'])
            ->get()
            ->groupBy('vs_date');

            $array = [];

            $data->transform(function ($item, $key) use (&$array){
                $array[] = [
                    'date' => Date::parse($key)->toFormattedDateString(),
                    'totalver' => $item->sum('vs_tf_denomination'),
                    'balance' => $item->sum('vs_tf_balance'),
                    'redeem' => $item->sum('vs_tf_purchasecredit'),
                ];

                return $array;
            });
            dd($array);
        //     $link->query(
        //         "SELECT
        //             DATE(store_verification.vs_date) as datever,
        //             IFNULL(SUM(store_verification.vs_tf_denomination),00.0) as totverifiedgc,
        //             IFNULL(SUM(store_verification.vs_tf_balance),00.0) as balance,
        //             IFNULL(SUM(store_verification.vs_tf_purchasecredit),00.0) as redeem
        //         FROM
        //             store_verification
        //         LEFT JOIN
        //             customers
        //         ON
        //             customers.cus_id = store_verification.vs_cn
        //         WHERE
        //             ((YEAR(vs_date) = '$year'
        //         AND
        //             MONTH(vs_date) = '$month')
        //         OR
        //             (YEAR(vs_reverifydate) = '$year'
        //         AND
        //             MONTH(vs_reverifydate) = '$month'))
        //         AND
        //             vs_store='$stcus'
        //         GROUP BY vs_date
        // ");
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
