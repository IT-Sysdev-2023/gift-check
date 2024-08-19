<?php

namespace App\Services\Finance;

use App\Services\Documents\UploadFileHandler;

class FinanceService extends UploadFileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = "financeUpload";
    }
    public function uploadFileHandler($request)
    {
       $name = $this->getOriginalFileName($request, $request->file);

       return $this->saveFile($request, $name);
    }

}