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

class PerGcTypeAndBuExports implements FromCollection, WithHeadings, WithEvents, WithTitle, WithStyles
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
        $data = $this->getDataPerGCTypeAndBu();

        return collect($data);
    }

    private function getDataPerGCTypeAndBu()
    {
        $arr_perdate = [];

        $datedisplay = "";

        $specialgc = 0;
        $regulargc = 0;
        $bng       = 0;
        $promo     = 0;

        $arr_special = [];
        $arr_regular = [];
        $arr_terbng = [];
        $arr_promo  = [];

        $type = [
            'hasSM' => false,
            'hasHF' => false,
            'hasMP' => false,
            'hasFR' => false,
            'hasSOD'  => false,
            'hasWS'  => false,

            'amtSM' => 0,
            'amtHF' => 0,
            'amtMP' => 0,
            'amtFR' => 0,
            'amtSOD'  => 0,
            'amtWS'  => 0,
        ];


        $arr_terspecial[] =  [
            'amtSM'   =>    0,
            'amtHF'   =>    0,
            'amtMP'   =>    0,
            'amtFR'   =>    0,
            'amtSOD'  =>    0,
            'amtWS'   =>    0
        ];

        $arr_terregular[] =  [
            'amtSM'   =>    0,
            'amtHF'   =>    0,
            'amtMP'   =>    0,
            'amtFR'   =>    0,
            'amtSOD'  =>    0,
            'amtWS'   =>    0

        ];

        $arr_terbng[] =  [
            'amtSM'   =>    0,
            'amtHF'   =>    0,
            'amtMP'   =>    0,
            'amtFR'   =>    0,
            'amtSOD'  =>    0,
            'amtWS'   =>    0
        ];

        $arr_terpromo[] =  [
            'amtSM'   =>    0,
            'amtHF'   =>    0,
            'amtMP'   =>    0,
            'amtFR'   =>    0,
            'amtSOD'  =>    0,
            'amtWS'   =>    0
        ];

        $data = $this->getMonthYearVerifiedGc($this->requestedData);

        $cntarr = count($data);
        $cnter = 0;

        collect($data)->each(function ($item) use (
            $type,
            &$cnter,
            &$arr_terspecial,
            $specialgc,
            $regulargc,
            $arr_terbng,
            $bng,
            $promo,
            $arr_terpromo,
            $datedisplay,
            &$arr_terregular,
            &$cntarr,
        ) {
            // dd(!empty($item['date']));
            $explodedTerminalNo = explode(",", $item['terminalno']);

            $purchase = explode(",", $item['purchasecred']);

            // dd($purchase);

            foreach ($explodedTerminalNo as $index => $term) {
                // dump($index);

                // $term = explode("-", $term);


                // switch (trim($term[0])) {
                //     case 'SM':
                //         $hasSM = true;
                //         $type['amtSM'] += $purchase[$index];
                //         break;

                //     case 'HF':
                //         $hasHF = true;
                //         $type['amtHF'] += $purchase[$index];
                //         break;

                //     case 'MP':
                //         $hasMP = true;
                //         $type['amtMP'] += $purchase[$index];
                //         break;

                //     case 'FR':
                //         $hasFR = true;
                //         $type['amtFR'] += $purchase[$index];
                //         break;

                //     case 'SOD':
                //         $hasSOD = true;
                //         $type['amtSOD'] += $purchase[$index];
                //         break;

                //     case 'WHOLESALE':
                //         $hasWS = true;
                //         $type['amtWS'] += $purchase[$index];
                //         break;
                // }
            }


            if (!empty($item['date'])) {

                if ($cnter === 1) {

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

                    $arr_perdate[] =  array(
                        'arr_perdate'   =>  $datedisplay,
                        'regular'       =>  $regulargc,
                        'special'       =>  $specialgc,
                        'bng'           =>  $bng,
                        'promo'         =>  $promo,
                        'terminalreg'   =>  $arr_terregular,
                        'terminalspec'  =>  $arr_terspecial,
                        'terminalbng'   =>  $arr_terbng,
                        'terminalpromo' =>  $arr_terpromo
                    );


                    $arr_terspecial[0]['amtSM'] = 0;
                    $arr_terspecial[0]['amtHF'] = 0;
                    $arr_terspecial[0]['amtMP'] = 0;
                    $arr_terspecial[0]['amtFR'] = 0;
                    $arr_terspecial[0]['amtSOD'] = 0;
                    $arr_terspecial[0]['amtWS'] = 0;

                    $arr_terregular[0]['amtSM'] = 0;
                    $arr_terregular[0]['amtHF'] = 0;
                    $arr_terregular[0]['amtMP'] = 0;
                    $arr_terregular[0]['amtFR'] = 0;
                    $arr_terregular[0]['amtSOD'] = 0;
                    $arr_terregular[0]['amtWS'] = 0;

                    $arr_terbng[0]['amtSM'] = 0;
                    $arr_terbng[0]['amtHF'] = 0;
                    $arr_terbng[0]['amtMP'] = 0;
                    $arr_terbng[0]['amtFR'] = 0;
                    $arr_terbng[0]['amtSOD'] = 0;
                    $arr_terbng[0]['amtWS'] = 0;

                    $arr_terpromo[0]['amtSM'] = 0;
                    $arr_terpromo[0]['amtHF'] = 0;
                    $arr_terpromo[0]['amtMP'] = 0;
                    $arr_terpromo[0]['amtFR'] = 0;
                    $arr_terpromo[0]['amtSOD'] = 0;
                    $arr_terpromo[0]['amtWS'] = 0;

                    $datedisplay = $item['date'];

                    $specialgc = 0;
                    $regulargc = 0;
                    $bng       = 0;
                    $promo     = 0;

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
                }
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


                $amtSM = 0;
                $amtHF = 0;
                $amtMP = 0;
                $amtFR = 0;
                $amtSOD = 0;
                $amtWS  = 0;
                $cnter++;


                if($cntarr === $cnter)
                {
                    $arr_perdate[] =  array(
                        'arr_perdate'   =>  $datedisplay,
                        'regular'       =>  $regulargc,
                        'special'       =>  $specialgc,
                        'bng'           =>  $bng,
                        'promo'         =>  $promo,
                        'terminalreg'   =>  $arr_terregular,
                        'terminalspec'  =>  $arr_terspecial,
                        'terminalbng'   =>  $arr_terbng,
                        'terminalpromo' =>  $arr_terpromo
                    );
                }


            }

            return $arr_perdate;
        });

        dd($data->toArray());
    }
}
