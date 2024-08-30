<?php

namespace App\Services\Finance;

use App\Models\BudgetRequest;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Support\Facades\Date;

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

    public function pendingBudgetGc()
    {

        $data = BudgetRequest::select('br_no', 'br_requested_at', 'br_request', 'br_requested_needed', 'br_requested_by')
            ->with('user:user_id,firstname,lastname')->where('br_request_status', '0')
            ->paginate(10)->withQueryString();

        $data->transform(function ($item) {
            $item->fullname = $item->user->full_name;
            $item->needed = Date::parse($item->br_requested_needed)->toFormattedDateString();
            $item->req_at = Date::parse($item->br_requested_at)->toFormattedDateString();
            return $item;
        });

        return $data;
    }
}
