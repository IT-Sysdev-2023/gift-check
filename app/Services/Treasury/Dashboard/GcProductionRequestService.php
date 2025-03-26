<?php

namespace App\Services\Treasury\Dashboard;

use App\Helpers\NumberHelper;
use App\Models\CancelledProductionRequest;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Models\RequisitionEntry;
use App\Services\Documents\FileHandler;
use Illuminate\Http\Request;
use App\Rules\DenomQty;
use Illuminate\Support\Facades\DB;

class GcProductionRequestService extends FileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'approvedProductionRequest';
    }
    public function pendingRequest() //pending_production_request.php
    {

        $dept = request()->user()->usertype;

        $pr = ProductionRequest::withWhereHas('user', fn($query) => $query->select('user_id', 'firstname', 'lastname')->where('usertype', $dept))
            ->select('pe_id', 'pe_file_docno', 'pe_date_needed', 'pe_remarks', 'pe_num', 'pe_date_request', 'pe_group')
            ->where('pe_status', 0)
            ->orderByDesc('pe_id')
            ->first();
        $denoms = Denomination::denomation();
    }

    public function approvedRequest(Request $request)
    {
        return ProductionRequest::with([
            'user:user_id,firstname,lastname',
            'approvedProductionRequest:ape_id,ape_pro_request_id,ape_approved_at,ape_approved_by'
        ])
            ->select('pe_id', 'pe_requested_by', 'pe_num', 'pe_date_request', 'pe_date_needed')
            ->filter($request->all('search', 'date'))
            ->where('pe_status', 1)
            ->orderByDesc('pe_id')
            ->paginate(10)
            ->withQueryString();
    }

    public function cancelledRequest() // cancelled-production-request.php
    {

        $record = CancelledProductionRequest::join('production_request', 'cancelled_production_request.cpr_pro_id', '=', 'production_request.pe_id')
            ->join('users as lreq', 'cancelled_production_request.pe_requested_by', '=', 'lreq.user_id')
            ->join('users as lcan', 'cancelled_production_request.cpr_by', '=', 'lcan.user_id')
            ->select('pe_id', 'pe_num', 'pe_date_request', 'pe_date_needed', 'lreq.firstname as lreqfname', 'lreq.lastname as lreqlname', 'cpr_at', 'lcan.firstname as lcanfname', 'lcan.lastname as lcanlname')
            ->orderByDesc('cpr_id')
            ->get();

        return $record;
    }

    public function viewApprovedProduction(string $id)
    {
        $productionRequest = ProductionRequest::find($id)
            ->load(
                'user:user_id,firstname,lastname',
                'approvedProductionRequest.user:user_id,firstname,lastname'
            );

        $items = ProductionRequestItem::selectRaw(
            "pe_items_quantity * denomination AS totalRow,
            denomination.denomination,
            (SUM(production_request_items.pe_items_quantity * denomination.denomination)) as total,
            (SELECT barcode_no FROM gc WHERE gc.denom_id = production_request_items.pe_items_denomination AND gc.pe_entry_gc = $id LIMIT 1) AS barcode_start,
            (SELECT barcode_no FROM gc WHERE gc.denom_id = production_request_items.pe_items_denomination AND gc.pe_entry_gc = $id ORDER BY barcode_no DESC LIMIT 1 ) AS barcode_end,
            production_request_items.pe_items_quantity,
            production_request_items.pe_items_denomination"
        )
            ->join('denomination', 'denomination.denom_id', '=', 'production_request_items.pe_items_denomination')
            ->where('pe_items_request_id', $id)
            ->groupBy('denomination.denomination', 'production_request_items.pe_items_quantity', 'production_request_items.pe_items_denomination')
            ->get();

        $sum = NumberHelper::currency($items->sum('totalRow'));

        $format = $items->map(function ($i) {
            $i->denomination = NumberHelper::currency($i->denomination);
            $i->totalRow = NumberHelper::currency($i->totalRow);
            return $i;
        });

        return (object) [
            'totalRow' => $sum,
            'productionRequest' => $productionRequest,
            'transformItems' => $format,
        ];
    }
    public function viewBarcodeGenerated(string $id)
    {
        $gc = Gc::select('denom_id', 'barcode_no')->with('denomination:denom_id,denomination')
            ->where([['pe_entry_gc', $id], ['gc_validated', '']])->get();

        $gcv = Gc::select('barcode_no', 'denom_id')
            ->has('custodianSrrItems.custodiaSsr')
            ->with([
                'denomination:denom_id,denomination',
                'custodianSrrItems.custodiaSsr:csrr_id,csrr_datetime'
            ])
            ->where([['pe_entry_gc', $id], ['gc_validated', '*']])
            ->get();

        return [
            'gcForValidation' => $gc,
            'gcValidated' => $gcv
        ];
    }
    public function viewRequisition(string $id)
    {
        return RequisitionEntry::select(
            'repuis_pro_id',
            'requis_supplierid',
            'requis_req_by',
            'requis_erno',
            'requis_req',
            'requis_loc',
            'requis_dept',
            'requis_rem',
            'requis_checked',
            'requis_approved'
        )->with([
                    'user:user_id,firstname,lastname',
                    'productionRequest:pe_id,pe_date_needed',
                    'supplier:gcs_id,gcs_companyname,gcs_contactperson,gcs_contactnumber,gcs_address'
                ])
            ->where('repuis_pro_id', $id)->first();
    }

    public function downloadFile(string $file)
    {
        return $this->download($file);
    }
    public function reprint($id)
    {
        $folder = "reports/marketing";
        return $this->retrieveFile($folder, "requis{$id}.pdf");
    }

    public function pendingSubmit(Request $request)
    {

        $request->validate([
            // 'dateNeeded' => 'required',
            'remarks' => 'required',
            'denom' => ['required', 'array', new DenomQty()],
        ]);

        DB::transaction(function () use ($request) {

            $file = $this->createFileName($request);

            ProductionRequest::where([['pe_id', $request->reqid], ['pe_status', '0']])->update([
                'pe_requested_by' => $request->user()->user_id,
                'pe_remarks' => $request->remarks,
                'pe_date_needed' => $request->dateNeeded,
                'pe_file_docno' => $file,
                'pe_group' => 0
            ]);

            $pr = ProductionRequest::orderByDesc('pe_id')->value('pe_status');
            if ($pr == 0) {
                $denoms = collect($request->denom);
                $denoms->each(function ($item) use ($request) {

                    if (!is_null($item['qty']) || $item['qty'] != 0) {
                        $pri = ProductionRequestItem::where([['pe_items_denomination', $item['id']], ['pe_items_request_id', $request->reqid]])->exists();
                        if ($pri) {
                            ProductionRequestItem::where([['pe_items_denomination', $item['id']], ['pe_items_request_id', $request->reqid]])->update([
                                'pe_items_quantity' => $item['qty'],
                                'pe_items_remain' => $item['qty']
                            ]);
                        } else {

                            ProductionRequestItem::insert([
                                'pe_items_id' => null,
                                'pe_items_denomination' => $item['id'],
                                'pe_items_quantity' => $item['qty'],
                                'pe_items_remain' => $item['qty'],
                                'pe_items_request_id' => $request->reqid
                            ]);
                        }
                    } else {
                        $hasProd = ProductionRequestItem::where([['pe_items_denomination', $item['id']], ['pe_items_request_id', $request->reqid]]);
                        if ($hasProd->exists()) {
                            $hasProd->delete();
                        }
                    }

                });

                $this->folderName = 'productionRequestFile';
                $this->saveFile($request, $file);

            } else {
                return redirect()->back()->with('error', 'Production request already approved/cancelled.');
            }

            return redirect()->back()->with('success', 'Successfully Submitted!');

        });
    }

    public function pending(Request $request)
    {
        $dept = $request->user()->usertype;

        $pr = ProductionRequest::select('pe_requested_by', 'pe_id', 'pe_file_docno', 'pe_date_needed', 'pe_remarks', 'pe_num', 'pe_date_request', 'pe_group')->withWhereHas('user', fn($q) => $q->select('user_id', 'firstname', 'lastname')
            ->where('usertype', $dept))
            ->where('pe_status', '0')->first();

        $denoms = Denomination::select('denomination', 'denom_id')->with([
            'productionRequestItems' => function ($query) use ($pr) {
                $query->select('pe_items_denomination', 'pe_items_quantity')
                    ->where('pe_items_request_id', $pr->pe_id);
            }
        ])
            ->where([
                ['denom_type', 'RSGC'],
                ['denom_status', 'active']
            ])
            ->get();

        return (object) [
            'production' => $pr,
            'denomination' => $denoms
        ];
    }
}
