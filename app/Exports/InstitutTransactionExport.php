<?php

namespace App\Exports;

use App\Models\InstitutTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InstitutTransactionExport implements FromView,
{
    // /**
    // * @return \Illuminate\Support\Collection
    // */

    protected $records;
   
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function view(): View
    {
        return view('excel.institution', [
           'data' => $this->records
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Center-align the entire sheet
            'A1:Z1000' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Center-align specific cells if needed
                $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal('center');
            },
        ];
    }
    // public function collection()
    // {
    //     // $transform = $this->records->transform(function ($item){
    //     //     $item->institutrId;
    //     // });
    //     // dd($this->records);
    //     return $this->records['barcode'];
    //     // return InstitutTransaction::all();
    // }
    // public function registerEvents(): array
    // {
    //     return [
    //         BeforeSheet::class => function (BeforeSheet $event) {
    //             $sheet = $event->sheet;
    //             $sheet->setCellValue('A1', $this->records['company']['name']);
    //             $sheet->setCellValue('A2', $this->records['company']['department']);
    //             $sheet->setCellValue('A3', $this->records['company']['report']);

    //             $sheet->setCellValue('A4', 'GC Rel. No');
    //             $sheet->setCellValue('B4', $this->records['subheader']['gc_rel_no']);

    //             $sheet->setCellValue('D4', 'Released Date');
    //             $sheet->setCellValue('E4', $this->records['subheader']['date_released']);

    //             $sheet->setCellValue('A5', 'Customer');
    //             $sheet->setCellValue('B5', $this->records['subheader']['customer']);
    //         },
    //         // AfterSheet::class => function (AfterSheet $afterSheet){

    //         // }
    //     ];
    // }

}
