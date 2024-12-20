<?php

namespace App\Services\StoreAccounting\Reports;

use App\Events\StoreAccountReportEvent;
use App\Services\Progress;
use App\Services\Treasury\Reports\ReportHelper;

class VerifiedGcReportGenerator extends Progress
{
    public function __construct() {
		parent::__construct();
		$this->progress['name'] = "Store Accounting Report";
	}

	
}