<?php

namespace App\Exports\DuplicateBarcodeExcel;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class allDuplicateExcel implements WithMultipleSheets
{
    protected $allDuplicateData;

    public function __construct($request)
    {
        $this->allDuplicateData = $request;
    }

    public function sheets(): array
    {
        $fileName = $this->allDuplicateData['barcodes'];
        // dd($fileName);
        $cleanedFileName = preg_replace('/[\r\n\t\s\x00-\x1F\x7F]+/', ',', $fileName); 
        // $baseDirectory = ('c:/Users/it personnel/downloads/' . $fileName); 

        $barcodes = array_filter(explode(',', $cleanedFileName));
        // $fileContents = file_get_contents($baseDirectory);
        // $processedData = explode(PHP_EOL, $fileContents);
        // $processedData = array_map('str_getcsv', $processedData);
        $barcodes = array_map('trim', $barcodes);
        // dd($barcodes);
        return [
            new tagbilaranExcel($barcodes),
            new talibonExcel($barcodes),
            new tubigonExcel($barcodes),
        ];
    }
}
