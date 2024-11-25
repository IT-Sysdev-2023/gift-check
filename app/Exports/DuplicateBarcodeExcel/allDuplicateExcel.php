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
        // dd($this->allDuplicateData);
        $fileName = $this->allDuplicateData['data'];
        // dd($fileName);

        // $filePath = "C:/Users/it personnel/Downloads/{$fileName}";

        // if (!file_exists($filePath)) {
        //     throw new \Exception("File not found: {$filePath}");
        // }

        // $fileContents = file_get_contents($fileName);

        $processedData = explode(PHP_EOL, $fileName);
        $processedData = array_map('str_getcsv', $processedData);
        // dd($processedData);
        return [
            new tagbilaranExcel($processedData),
            new talibonExcel($processedData),
            new tubigonExcel($processedData),
        ];
    }
}
