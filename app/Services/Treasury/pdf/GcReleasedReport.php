<?php

namespace App\Services\Treasury\Pdf;

class GcReleasedReport
{
    private $date;
    public function __construct() {
        
    }

    public function releasedDate($date){
        $this->date = $date;
        return $this;
    }


}