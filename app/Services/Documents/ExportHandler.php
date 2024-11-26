<?php

namespace App\Services\Documents;

use App\Jobs\DeleteFile;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\DashboardRoutesTrait;
class ExportHandler extends FileHandler
{
    use DashboardRoutesTrait;
    private $filename;
    private string $fullPath;
    public function __construct()
    {
        parent::__construct();
    }

    public function setFolder(string $folder)
    {
        $this->folderName = Str::finish($folder, '/');
        return $this;
    }
    public function setFileName($id, $identifier)
    {
        $date = now()->timestamp;
        $this->filename = "{$id}-{$identifier}-" . $date;

        return $this;
    }

    public function setSubfolderAsUsertype(int $userType)
    {
        $this->folderName = Str::finish($this->folderName . $this->roleDashboardRoutes[$userType], '/');
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
        $filename = $this->folder() . $this->filename . '.pdf';
        $this->disk->put($filename, $pdf);
        $this->fullPath = $filename;
        return $this;
    }

    public function exportDocument(string $format, $document)
    {
        if ($format === 'pdf') {
            $this->exportToPdf($document);
        } else { //excel
            $this->exportToExcel($document);
        }
    }

    public function deleteFileIn($date)
    {
        DeleteFile::dispatch($this->fullPath)->delay($date);
    }

}