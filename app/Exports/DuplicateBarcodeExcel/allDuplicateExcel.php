<?php

namespace App\Exports\DuplicateBarcodeExcel;

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
        $fileName = $this->allDuplicateData['data'];

        $filePath = "C:/Users/it personnel/Downloads/{$fileName}";

        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        $fileContents = file_get_contents($filePath);

        $processedData = explode(PHP_EOL, $fileContents);
        $processedData = array_map('str_getcsv', $processedData);
        return [
            new tagbilaranExcel($processedData),
            new talibonExcel($processedData),
            new tubigonExcel($processedData),
        ];
    }
}
