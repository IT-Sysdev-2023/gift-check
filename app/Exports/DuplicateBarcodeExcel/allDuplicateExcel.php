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
        $talibonData = $this->allDuplicateData['talibonData'] ?? [];
        $tubigonData = $this->allDuplicateData['tubigonData'] ?? [];

        $cleanedFileName = preg_replace('/[\r\n\t\s\x00-\x1F\x7F]+/', ',', $fileName);

        $barcodes = array_filter(explode(',', $cleanedFileName));

        $barcodes = array_map('trim', $barcodes);
        return [
            new tagbilaranExcel($barcodes),
            new talibonExcel($talibonData),
            new tubigonExcel($tubigonData),
        ];
    }
}
