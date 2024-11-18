<?php

namespace App\Services\Documents;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
class ExportHandler extends FileHandler
{

    private $filename;
    public function __construct(string $folder)
    {
        parent::__construct();
        $this->folderName = $folder;
    }
    public function createExcelFileName($id, $identifier)
    {
        $date = now()->format('Y-m-d-His');
        $this->filename = "{$id}-{$identifier}-" . $date;

        return $this;
    }

    public function exportToExcel($model)
    {
        $folderName = $this->folder() . 'excel/';
        Excel::store($model, "{$folderName}{$this->filename}.xlsx", 'public');
        return $this;
    }

    public function exportToPdf($pdf)
    {
        $filename = $this->filename . ".pdf";
        return $this->disk->put("{$this->folder()}{$filename}", $pdf);
    }

}