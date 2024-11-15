<?php

namespace App\Services\Documents;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
class ExcelHandler
{
    protected string $folderName = '';
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    private function folder()
    {
        return "$this->folderName/";
    }


    protected function saveExcel($request, $identifier, $model ){
        $date = now()->format('Y-m-d-His');
        $filename = "{$request->user()->user_id}-{$identifier}-" . $date . ".xlsx";
        return  Excel::store($model, "{$this->folder()}{$filename}", 'public'); 
    }
}