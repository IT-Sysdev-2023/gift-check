<?php

namespace App\Services\Documents;

use App\Helpers\Excel\ExcelWriter;

class DocumentService extends ExcelWriter
{
    protected $record;

    public function __construct()
    {
        parent::__construct();
    }

    public function record($record)
    {

        $this->record = $record;

        return $this;
    }

    public function writeResult()
    {
        // dd($this->record);

    }
}
