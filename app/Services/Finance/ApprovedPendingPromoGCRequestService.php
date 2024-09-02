<?php

namespace App\Services\Finance;

use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Http\Requests\PromoForApprovalRequest;
use App\Http\Resources\PromoGcDetailResource;
use App\Http\Resources\PromoGcRequestResource;
use App\Models\ApprovedRequest;
use App\Models\LedgerBudget;
use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Support\Facades\DB;

class ApprovedPendingPromoGCRequestService extends UploadFileHandler
{
    public function __construct()
    {
        parent::__construct();

        $this->folderName = "finance";
    }
    public function pendingPromoGCRequestIndex($request)
    {
        return inertia('Finance/PendingPromoGcRequest', [
            'data' => self::getPromoRequest($request),
            'columns' => ColumnHelper::app_pend_request_columns(true),
            'details' => self::getRequestDetails($request),
            'denomination' => self::getDenomination($request->id),
            'activeKey' => $request->activeKey,
            'reqid' => $request->id,
        ]);
    }

    public static function getPromoRequest($request)
    {
        $data = PromoGcRequest::with(['userReqby:user_id,firstname,lastname'])
            ->selectPromoRequest()
            ->whereFilterForPending()
            ->orderByDesc('pgcreq_id')
            ->searchFilter($request)
            ->paginate()
            ->withQueryString();

        $result = PromoGcRequestResource::collection($data);

        return $result;
    }

    public static function getRequestDetails($request)
    {

        $data = PromoGcRequest::selectPendingRequest()->with([
            'userReqby' => function ($query) {
                $query->select('usertype', 'user_id', 'firstname', 'lastname');
            },
            'userReqby.accessPage' => function ($query) {
                $query->select('access_no', 'title');
            }
        ])
            ->where('pgcreq_id', $request->id)
            ->get();

        $result = PromoGcDetailResource::collection($data);

        return $result;
    }

    public function approvedPromoGCRequestIndex($request)
    {
        return inertia('Finance/ApprovedPromoGcRequest', [
            'data' => PromoGcRequestResource::collection(
                PromoGcRequest::with(['userReqby:user_id,firstname,lastname'])
                    ->selectPromoRequest()
                    ->whereFilterForApproved()
                    ->orderByDesc('pgcreq_id')
                    ->searchFilter($request)
                    ->paginate()
                    ->withQueryString()
            ),
            'columns' => ColumnHelper::app_pend_request_columns(false),
        ]);
    }

    public static function getDenomination($id)
    {
        $data =  PromoGcRequestItem::select('pgcreqi_qty', 'pgcreqi_denom')
            ->where('pgcreqi_trid', $id)
            ->with('denomination:denom_id,denomination')
            ->get();

        $data->transform(function ($item) {
            $item->subt = $item->denomination->denomination * $item->pgcreqi_qty;
            $item->subtotal = NumberHelper::formatterFloat($item->denomination->denomination * $item->pgcreqi_qty);
            $item->denomination->denomination = NumberHelper::formatterFloat($item->denomination->denomination);
            return $item;
        });
        return (object)[
            'data' => $data,
            'total' => NumberHelper::currency($data->sum('subt')),
        ];
    }

    public function approveRequest(PromoForApprovalRequest $request)
    {
        $request->validated();

        $id = $request->reqid;

        DB::transaction(function () use ($request, $id) {

            PromoGcRequest::where('pgcreq_id', $id)->update([
                'pgcreq_status' => 'approved'
            ]);

            $file = $this->createFileName($request);

            $wasCreated = ApprovedRequest::create([
                'reqap_trid' => $id,
                'reqap_approvedtype' => 'promo gc approved',
                'reqap_remarks' => $request->remarks,
                'reqap_doc' => $file ?? '',
                'reqap_checkedby' => $request->checkby,
                'reqap_approvedby' => $request->appby,
                'reqap_date' => today(),
                'reqap_preparedby' => $request->user()->user_id,
            ]);

            if ($wasCreated->wasRecentlyCreated) {
                $this->saveFile($request, $file);
            }

            $getDenom = self::getDenomination($id);

            $result = NumberHelper::float($getDenom->total);

            $ledgerNumber = self::getLedgerNumber();

            LedgerBudget::create([
                'bledger_no' => $ledgerNumber,
                'bledger_trid' => $id,
                'bledger_datetime' => today(),
                'bledger_type' => 'RFGCPROM',
                'bcredit_amt' => $result,
            ]);
        });

        return redirect()->route('finance.dashboard')->with([
            'msg' => 'Successfully form submitted',
            'title' => 'Success',
            'status' => 'success'
        ]);
    }

    public static function getLedgerNumber()
    {
        $ledger = LedgerBudget::orderByDesc('bledger_id')->first();

        $ledgerNo = $ledger->bledger_no += 1;

        return $ledgerNo;
    }
}
