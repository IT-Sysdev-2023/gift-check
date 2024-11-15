<?php

namespace App\Exports;

use App\Models\InstitutTransaction;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;

class InstitutTransactionExport implements FromCollection, WithEvents, WithCustomStartCell, ShouldAutoSize, WithDefaultStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }
    public function collection()
    {
        return $this->transform();

    }
    public function transform()
    {
        $formattedRecords = collect();
        $this->records['barcode']->each(function ($denomination, $key) use (&$formattedRecords) {

            $formattedRecords[] = ['Denomination: ₱ ' . number_format($key)];

            $barcodes = $denomination->map(fn($item) => $item['barcode']);
            $chunks = $barcodes->chunk(5); // Chunk by 5 devide barcodes

            $chunks->each(fn($i) => $formattedRecords[] = $i);

            $formattedRecords[] = ['No of GC: ' . $denomination->count() . ' pcs'];
            $formattedRecords[] = [null]; // Empty array creates an empty row
        });
        return $formattedRecords;
    }
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet;

                $sheet->setCellValue('J1', $this->records['company']['name']);
                $sheet->setCellValue('J2', $this->records['company']['department']);
                $sheet->setCellValue('J3', $this->records['company']['report']);
                $sheet->getStyle('J1:J3')->getFont()->setBold(true);
                $sheet->getStyle('J1:J3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('H5', 'GC Rel. No:');
                $sheet->getStyle('H5')->getFont()->setBold(true);

                $sheet->setCellValue('I5', $this->records['subheader']['gc_rel_no']);

                $sheet->setCellValue('K5', 'Released Date:');
                $sheet->getStyle('K5')->getFont()->setBold(true);

                $sheet->setCellValue('L5', $this->records['subheader']['date_released']);
    
                $sheet->setCellValue('H6', 'Customer:');
                $sheet->getStyle('H6')->getFont()->setBold(true);

                $sheet->setCellValue('I6', $this->records['subheader']['customer']);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $this->transform()->each(function ($i, $key) use ($sheet) {
                    $s = collect($i)->search(fn($arr) => Str::contains($arr, ['Denomination', 'No']));

                    $gap = $key + 8; // 8 is gap from the header
    
                    if ($s !== false) {
                        $sheet->getStyle('H' . $gap)->getFont()->setBold(true);
                    } else {

                        $sheet->getStyle('H' . $gap . ':L' . $gap)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_NUMBER);

                        $sheet->getStyle('H' . $gap . ':L' . $gap)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                });

                //FOOTER DETAILS
                $highestRow = $sheet->getHighestRow() + 1;
                $this->setFooterValue($sheet, $highestRow, 'Total No. of Gc: ', $this->records['summary']['total_no_of_gc']);
                
                $this->setFooterValue($sheet, $highestRow += 1, 'Payment Type: ', $this->records['summary']['payment_type']);
                
                $this->setFooterValue($sheet, $highestRow += 1, 'Cash Received: ', $this->records['summary']['cash_received']);
                
                $this->setFooterValue($sheet, $highestRow += 1, 'Total Gc Amount: ', $this->records['summary']['total_gc_amount']);
                
                $this->setFooterValue($sheet, $highestRow += 1, 'Change: ', $this->records['summary']['change']);
               
                $this->setFooterValue($sheet, $highestRow += 1, 'Payment Fund: ', $this->records['summary']['paymentFund']);

                //Signatures 
                $sheet->setCellValue('H' . $highestRow += 2, 'Signatures:');
                $sheet->getStyle('H' . $highestRow)->getFont()->setBold(true);
                
                $this->setFooterValue($sheet, $highestRow += 2, 'Prepared Released By: ', $this->records['signatures']['prepared_released_by']);
                $this->setFooterValue($sheet, $highestRow += 1, 'Checked By: ', $this->records['signatures']['checked_by']);
                $this->setFooterValue($sheet, $highestRow += 1, 'Received By: ', $this->records['signatures']['received_by']);
            },

        ];
    }

    private function setFooterValue($sheet, $highestRow, $label,  $data){

        $sheet->setCellValue('H' . $highestRow, $label);
        $sheet->getStyle('H' . $highestRow)->getFont()->setBold(true);
        $sheet->setCellValue('I' . $highestRow, $data);
    }

    public function startCell(): string
    {
        return 'H8';
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ];
    }

}
