<?php

namespace App\Exports\IadVerifiedExports;

use App\Models\User;
use App\Traits\VerifiedExportsTraits\VerifiedTraits;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PerGcTypeAndBuExports implements FromCollection, WithHeadings, WithEvents, WithTitle, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    use VerifiedTraits;

    protected $requestedData;


    public function __construct($request)
    {
        $this->requestedData = $request;
    }
    public function title(): string
    {
        return 'Per Gc Type And BU';
    }
    public function headings(): array
    {
        return [
            'DATE',
            'GC TYPE',
            'AMOUNT',
            'BALANCE',
            'SM',
            'HF',
            'MP',
            'FR',
            'SOD',
            'WS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(8)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $lastRow = $this->collectionData()->count() + 8;


        // $data = $this->getDataStoreVerifivation();

        // $rowcount = $data->count();

        // $colcount = count($this->headings());

        // $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colcount);



        // $range = 'A8:' . $lastColumn  . $rowcount + 8;



        // $sheet->getStyle($range)->applyFromArray([
        //     'font' => [
        //         'size' => 9,
        //     ],
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //             'color' => ['argb' => '000000'],
        //         ],
        //     ],
        //     'alignment' => [
        //         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Center horizontally
        //         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Center vertically
        //     ],
        // ]);

        // for ($col = 1; $col <= $colcount; $col++) {
        //     $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        //     $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        // }


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
            "A8:J{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'], // Black border
                    ],
                ],
            ],
        ];
    }
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;
                $sheet->setCellValue('A1', 'ALTURAS GROUP OF COMPANIES');
                $sheet->setCellValue('A2', 'CUSTOMER FINANCIAL SERVICES CORP');
                $sheet->setCellValue('A3', 'MONTHLY REPORT ON GIFT CHECK (Per Gc Type And Business Unit)');
                $sheet->setCellValue('A4', 'PERIOD COVER: JANUARY 1 - 31, 2019');
                $sheet->setCellValue('A6', 'BUSINESS UNITS: ALTURAS MALL');
                $sheet->setCellValue('A7', '');
            },
        ];
    }
    public function collection()
    {
        $rows = $this->collectionData();
        return $rows;
    }

    private function collectionData()
    {
        $rows = collect();

        $data = $this->getDataPerGCTypeAndBu();

        $data->each(function ($item) use (&$rows) {

            $date = $item[0]['arr_perdate'];

            $rows->push([
                $date,
                'REGULAR',
                $item[0]['regular'],
                $item[0]['terminalreg'][0]['amtSM'],
                $item[0]['terminalreg'][0]['amtHF'],
                $item[0]['terminalreg'][0]['amtMP'],
                $item[0]['terminalreg'][0]['amtFR'],
                $item[0]['terminalreg'][0]['amtSOD'],
                $item[0]['terminalreg'][0]['amtWS'],
            ]);

            $rows->push([
                '',
                'SPECIAL EXTERNAL',
                $item[0]['special'],
                $item[0]['terminalspec'][0]['amtSM'],
                $item[0]['terminalspec'][0]['amtHF'],
                $item[0]['terminalspec'][0]['amtMP'],
                $item[0]['terminalspec'][0]['amtFR'],
                $item[0]['terminalspec'][0]['amtSOD'],
                $item[0]['terminalspec'][0]['amtWS'],
            ]);

            $rows->push([
                '',
                'BEAM AND GO',
                $item[0]['bng'],
                $item[0]['terminalbng'][0]['amtSM'],
                $item[0]['terminalbng'][0]['amtHF'],
                $item[0]['terminalbng'][0]['amtMP'],
                $item[0]['terminalbng'][0]['amtFR'],
                $item[0]['terminalbng'][0]['amtSOD'],
                $item[0]['terminalbng'][0]['amtWS'],
            ]);


            $rows->push([
                '',
                'PROMOTIONAL GC',
                $item[0]['promo'],
                $item[0]['terminalpromo'][0]['amtSM'],
                $item[0]['terminalpromo'][0]['amtHF'],
                $item[0]['terminalpromo'][0]['amtMP'],
                $item[0]['terminalpromo'][0]['amtFR'],
                $item[0]['terminalpromo'][0]['amtSOD'],
                $item[0]['terminalpromo'][0]['amtWS'],
            ]);
        });

        return $rows;
    }

    private function getDataPerGCTypeAndBu()
    {
        $arr_perdate = [];

        $datedisplay = "";

        $specialgc = '0';
        $regulargc = '0';
        $bng       = '0';
        $promo     = '0';

        $type = [
            'hasSM' => false,
            'hasHF' => false,
            'hasMP' => false,
            'hasFR' => false,
            'hasSOD'  => false,
            'hasWS'  => false,

            'amtSM' => '0',
            'amtHF' => '0',
            'amtMP' => '0',
            'amtFR' => '0',
            'amtSOD'  => '0',
            'amtWS'  => '0',
        ];


        $arr_terspecial[] =  [
            'amtSM'   =>    '0',
            'amtHF'   =>    '0',
            'amtMP'   =>    '0',
            'amtFR'   =>    '0',
            'amtSOD'  =>    '0',
            'amtWS'   =>    '0'
        ];

        $arr_terregular[] =  [
            'amtSM'   =>    '0',
            'amtHF'   =>    '0',
            'amtMP'   =>    '0',
            'amtFR'   =>    '0',
            'amtSOD'  =>    '0',
            'amtWS'   =>    '0'

        ];

        $arr_terbng[] =  [
            'amtSM'   =>    '0',
            'amtHF'   =>    '0',
            'amtMP'   =>    '0',
            'amtFR'   =>    '0',
            'amtSOD'  =>    '0',
            'amtWS'   =>    '0'
        ];

        $arr_terpromo[] =  [
            'amtSM'   =>    '0',
            'amtHF'   =>    '0',
            'amtMP'   =>    '0',
            'amtFR'   =>    '0',
            'amtSOD'  =>    '0',
            'amtWS'   =>    '0'
        ];

        $data = $this->getMonthYearVerifiedGc($this->requestedData);

        $cntarr = count($data);

        $cnter = 0;

        collect($data)->each(function ($item) use (
            $type,
            &$cnter,
            &$arr_terspecial,
            &$specialgc,
            &$regulargc,
            &$arr_terbng,
            &$bng,
            &$promo,
            &$arr_terpromo,
            &$datedisplay,
            &$arr_terregular,
            &$cntarr,
            &$arr_perdate,
        ) {


            if ((float) $item['purchasecred'] > 0) {

                $explodedTerminalNo = explode(",", $item['terminalno']);

                $purchase = explode(",", $item['purchaseamt']);

                foreach ($explodedTerminalNo as $index => $terminal) {

                    $term = explode("-", $explodedTerminalNo[0]);

                    if (trim($term[0]) === 'SM') {
                        $hasSM = true;
                        //$arr_terspecial[0]['amtSM'] += $purchase[$i];
                        $type['amtSM'] += (float)$purchase[0];
                        // var_dump(1);
                    }
                    if (trim($term[0]) === 'HF') {
                        $hasSM = true;
                        //$arr_terspecial[0]['amtSM'] += $purchase[$i];
                        $type['amtHF'] += (float)$purchase[0];
                        // var_dump(1);
                    }
                    if (trim($term[0]) === 'MP') {
                        $hasSM = true;
                        //$arr_terspecial[0]['amtSM'] += $purchase[$i];
                        $type['amtMP'] += (float)$purchase[0];
                        // var_dump(1);
                    }
                    if (trim($term[0]) === 'FR') {
                        $hasSM = true;
                        //$arr_terspecial[0]['amtSM'] += $purchase[$i];
                        $type['amtFR'] += (float)$purchase[0];
                        // var_dump(1);
                    }
                    if (trim($term[0]) === 'SOD') {
                        $hasSM = true;
                        //$arr_terspecial[0]['amtSM'] += $purchase[$i];
                        $type['amtSOD'] += (float)$purchase[0];
                        // var_dump(1);
                    }
                    if (trim($term[0]) === 'WHOLESALE') {
                        $hasSM = true;
                        //$arr_terspecial[0]['amtSM'] += $purchase[$i];
                        $type['amtWS'] += (float)$purchase[0];
                        // var_dump(1);
                    }

                    // switch (trim($term[0])) {
                    //     case 'SM':
                    //         $hasSM = true;
                    //         $type['amtSM'] += (float)$purchase[0];
                    //         break;

                    //     case 'HF':
                    //         $hasHF = true;
                    //         $type['amtHF'] += (float)$purchase[0];
                    //         break;

                    //     case 'MP':
                    //         $hasMP = true;
                    //         $type['amtMP'] += (float)$purchase[0];
                    //         break;

                    //     case 'FR':
                    //         $hasFR = true;
                    //         $type['amtFR'] += (float)$purchase[0];
                    //         break;

                    //     case 'SOD':
                    //         $hasSOD = true;
                    //         $type['amtSOD'] += (float)$purchase[0];
                    //         break;

                    //     case 'WHOLESALE':
                    //         $hasWS = true;
                    //         $type['amtWS'] += (float)$purchase[0];
                    //         break;
                    // }
                }
            }



            if ($datedisplay !== $item['date']) {

                if ($cnter === 1) {
                    // dump($cnter);
                    $datedisplay = $item['date'];


                    if ($item['gc_type'] === 'SPECIAL EXTERNAL') {

                        $arr_terspecial[0]['amtSM'] += $type['amtSM'];
                        $arr_terspecial[0]['amtHF'] += $type['amtHF'];
                        $arr_terspecial[0]['amtMP'] += $type['amtMP'];
                        $arr_terspecial[0]['amtFR'] += $type['amtFR'];
                        $arr_terspecial[0]['amtSOD'] += $type['amtSOD'];
                        $arr_terspecial[0]['amtWS'] += $type['amtWS'];

                        $specialgc += $item['purchasecred'];
                    }

                    if ($item['gc_type'] === 'REGULAR') {

                        $arr_terregular[0]['amtSM'] += $type['amtSM'];
                        $arr_terregular[0]['amtHF'] += $type['amtHF'];
                        $arr_terregular[0]['amtMP'] += $type['amtMP'];
                        $arr_terregular[0]['amtFR'] += $type['amtFR'];
                        $arr_terregular[0]['amtSOD'] += $type['amtSOD'];
                        $arr_terregular[0]['amtWS'] += $type['amtWS'];

                        $regulargc += $item['purchasecred'];
                    }
                    if ($item['gc_type'] === 'BEAM AND GO') {

                        $arr_terbng[0]['amtSM'] += $type['amtSM'];
                        $arr_terbng[0]['amtHF'] += $type['amtHF'];
                        $arr_terbng[0]['amtMP'] += $type['amtMP'];
                        $arr_terbng[0]['amtFR'] += $type['amtFR'];
                        $arr_terbng[0]['amtSOD'] += $type['amtSOD'];
                        $arr_terbng[0]['amtWS'] += $type['amtWS'];

                        $bng += $item['purchasecred'];
                    }
                    if ($item['gc_type'] === 'PROMOTIONAL GC') {

                        $arr_terpromo[0]['amtSM'] += $type['amtSM'];
                        $arr_terpromo[0]['amtHF'] += $type['amtHF'];
                        $arr_terpromo[0]['amtMP'] += $type['amtMP'];
                        $arr_terpromo[0]['amtFR'] += $type['amtFR'];
                        $arr_terpromo[0]['amtSOD'] += $type['amtSOD'];
                        $arr_terpromo[0]['amtWS'] += $type['amtWS'];

                        $promo += $item['purchasecred'];
                    }
                } else {

                    $arr_perdate[] = [
                        'arr_perdate'   =>  $datedisplay,
                        'regular'       =>  $regulargc === 0 ? '0.0' : $regulargc,
                        'special'       =>  $specialgc === 0 ? '0.0' : $specialgc,
                        'bng'           =>  $bng === 0 ? '0.0' : $bng,
                        'promo'         =>  $promo === 0 ? '0.0' : $promo,
                        'terminalreg'   =>  $arr_terregular,
                        'terminalspec'  =>  $arr_terspecial,
                        'terminalbng'   =>  $arr_terbng,
                        'terminalpromo' =>  $arr_terpromo
                    ];

                    // dd(collect($arr_perdate));

                    $arr_terspecial[0]['amtSM'] = '0';
                    $arr_terspecial[0]['amtHF'] = '0';
                    $arr_terspecial[0]['amtMP'] = '0';
                    $arr_terspecial[0]['amtFR'] = '0';
                    $arr_terspecial[0]['amtSOD'] = '0';
                    $arr_terspecial[0]['amtWS'] = '0';

                    $arr_terregular[0]['amtSM'] = '0';
                    $arr_terregular[0]['amtHF'] = '0';
                    $arr_terregular[0]['amtMP'] = '0';
                    $arr_terregular[0]['amtFR'] = '0';
                    $arr_terregular[0]['amtSOD'] = '0';
                    $arr_terregular[0]['amtWS'] = '0';

                    $arr_terbng[0]['amtSM'] = '0';
                    $arr_terbng[0]['amtHF'] = '0';
                    $arr_terbng[0]['amtMP'] = '0';
                    $arr_terbng[0]['amtFR'] = '0';
                    $arr_terbng[0]['amtSOD'] = '0';
                    $arr_terbng[0]['amtWS'] = '0';

                    $arr_terpromo[0]['amtSM'] = '0';
                    $arr_terpromo[0]['amtHF'] = '0';
                    $arr_terpromo[0]['amtMP'] = '0';
                    $arr_terpromo[0]['amtFR'] = '0';
                    $arr_terpromo[0]['amtSOD'] = '0';
                    $arr_terpromo[0]['amtWS'] = '0';

                    $datedisplay = $item['date'];

                    $specialgc = '0';
                    $regulargc = '0';
                    $bng       = '0';
                    $promo     = '0';

                    if ($item['gc_type'] === 'SPECIAL EXTERNAL') {

                        $arr_terspecial[0]['amtSM'] += $type['amtSM'];
                        $arr_terspecial[0]['amtHF'] += $type['amtHF'];
                        $arr_terspecial[0]['amtMP'] += $type['amtMP'];
                        $arr_terspecial[0]['amtFR'] += $type['amtFR'];
                        $arr_terspecial[0]['amtSOD'] += $type['amtSOD'];
                        $arr_terspecial[0]['amtWS'] += $type['amtWS'];


                        $specialgc += $item['purchasecred'];
                    }

                    if ($item['gc_type'] === 'REGULAR') {


                        $arr_terregular[0]['amtSM'] += $type['amtSM'];
                        $arr_terregular[0]['amtHF'] += $type['amtHF'] ;
                        $arr_terregular[0]['amtMP'] += $type['amtMP'];
                        $arr_terregular[0]['amtFR'] += $type['amtFR'];
                        $arr_terregular[0]['amtSOD'] += $type['amtSOD'];
                        $arr_terregular[0]['amtWS'] += $type['amtWS'] ;

                        $regulargc += $item['purchasecred'];
                    }
                    if ($item['gc_type'] === 'BEAM AND GO') {

                        $arr_terbng[0]['amtSM'] += $type['amtSM'];
                        $arr_terbng[0]['amtHF'] += $type['amtHF'];
                        $arr_terbng[0]['amtMP'] += $type['amtMP'];
                        $arr_terbng[0]['amtFR'] += $type['amtFR'];
                        $arr_terbng[0]['amtSOD'] += $type['amtSOD'];
                        $arr_terbng[0]['amtWS'] += $type['amtWS'];
                        $bng += $item['purchasecred'];
                    }
                    if ($item['gc_type'] === 'PROMOTIONAL GC') {

                        $arr_terpromo[0]['amtSM'] += $type['amtSM'];
                        $arr_terpromo[0]['amtHF'] += $type['amtHF'];
                        $arr_terpromo[0]['amtMP'] += $type['amtMP'];
                        $arr_terpromo[0]['amtFR'] += $type['amtFR'];
                        $arr_terpromo[0]['amtSOD'] += $type['amtSOD'];
                        $arr_terpromo[0]['amtWS'] += $type['amtWS'];
                        $promo += $item['purchasecred'];
                    }
                }

                // dd($arr_perdate);
            } else {
                if ($item['gc_type'] === 'SPECIAL EXTERNAL') {

                    $arr_terspecial[0]['amtSM'] += $type['amtSM'];
                    $arr_terspecial[0]['amtHF'] += $type['amtHF'];
                    $arr_terspecial[0]['amtMP'] += $type['amtMP'];
                    $arr_terspecial[0]['amtFR'] += $type['amtFR'];
                    $arr_terspecial[0]['amtSOD'] += $type['amtSOD'];
                    $arr_terspecial[0]['amtWS'] += $type['amtWS'];
                    $specialgc += $item['purchasecred'];
                }

                if ($item['gc_type'] === 'REGULAR') {
                    // dump($type['amtSM']);
                    $arr_terregular[0]['amtSM'] += $type['amtSM'] ;
                    $arr_terregular[0]['amtHF'] += $type['amtHF'] ;
                    $arr_terregular[0]['amtMP'] += $type['amtMP'] ;
                    $arr_terregular[0]['amtFR'] += $type['amtFR'] ;
                    $arr_terregular[0]['amtSOD'] += $type['amtSOD'] ;
                    $arr_terregular[0]['amtWS'] += $type['amtWS'] ;
                    $regulargc += $item['purchasecred'];
                }
                if ($item['gc_type'] === 'BEAM AND GO') {

                    $arr_terbng[0]['amtSM'] += $type['amtSM'];
                    $arr_terbng[0]['amtHF'] += $type['amtHF'];
                    $arr_terbng[0]['amtMP'] += $type['amtMP'];
                    $arr_terbng[0]['amtFR'] += $type['amtFR'];
                    $arr_terbng[0]['amtSOD'] += $type['amtSOD'];
                    $arr_terbng[0]['amtWS'] += $type['amtWS'];
                    $bng += $item['purchasecred'];
                }
                if ($item['gc_type'] === 'PROMOTIONAL GC') {

                    $arr_terpromo[0]['amtSM'] += $type['amtSM'];
                    $arr_terpromo[0]['amtHF'] += $type['amtHF'];
                    $arr_terpromo[0]['amtMP'] += $type['amtMP'];
                    $arr_terpromo[0]['amtFR'] += $type['amtFR'];
                    $arr_terpromo[0]['amtSOD'] += $type['amtSOD'];
                    $arr_terpromo[0]['amtWS'] += $type['amtWS'];
                    $promo += $item['purchasecred'];
                }


                $type['amtSM'] = '0';
                $type['amtHF'] = '0';
                $type['amtMP'] = '0';
                $type['amtFR']= '0';
                $type['amtSOD'] = '0';
                $type['amtWS']  = '0';
                $cnter++;


                if ($cntarr === $cnter) {

                    $arr_perdate[] =  [
                        'arr_perdate'   =>  $datedisplay,
                        'regular'       =>  $regulargc === 0 ? '0.0' : $regulargc,
                        'special'       =>  $specialgc === 0 ? '0.0' : $specialgc,
                        'bng'           =>  $bng === 0 ? '0.0' : $bng,
                        'promo'         =>  $promo === 0 ? '0.0' : $promo,
                        'terminalreg'   =>  $arr_terregular,
                        'terminalspec'  =>  $arr_terspecial,
                        'terminalbng'   =>  $arr_terbng,
                        'terminalpromo' =>  $arr_terpromo
                    ];
                }
            }
        });
        return collect($arr_perdate)->groupBy('arr_perdate')->values();
    }
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER, // Column B as a number
        ];
    }
}
