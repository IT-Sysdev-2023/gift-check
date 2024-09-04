<?php

namespace App\Http\Controllers\Treasury\Transactions;

use App\Helpers\ArrayHelper;
use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PromoGcRequestResource;
use App\Models\Assignatory;
use App\Models\CustodianSrrItem;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\GcLocation;
use App\Models\PromoGcReleaseToDetail;
use App\Models\PromoGcReleaseToItem;
use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;
use App\Services\Treasury\Transactions\PromoGcReleasingService;
use Illuminate\Support\Facades\DB;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;

class PromoGcReleasingController extends Controller
{
    private string $sessionName;
    public function __construct(public PromoGcReleasingService $promoGcReleasingService)
    {
        $this->sessionName = 'scannedPromo';
    }
    public function index(Request $request)
    {
        $records = PromoGcRequest::select('pgcreq_reqby', 'pgcreq_reqnum', 'pgcreq_remarks', 'pgcreq_doc', 'pgcreq_datereq', 'pgcreq_id', 'pgcreq_dateneeded', 'pgcreq_total', 'pgcreq_relstatus')
            ->with('userReqby:user_id,firstname,lastname')
            ->withWhereHas(
                'approvedReq',
                fn($q) => $q->with('user:user_id,firstname,lastname')
                    ->select('reqap_preparedby', 'reqap_trid')
                    ->where('reqap_approvedtype', 'promo gc preapproved')
            )->where([['pgcreq_status', 'approved']])
            ->orderByDesc('pgcreq_id')->paginate()->withQueryString();


        // dd(PromoGcRequestResource::collection($records)->toArray($request));
        return inertia('Treasury/Transactions/PromoGcReleasing/PromoGcReleasingIndex', [
            'title' => 'Promo Gc Releasing',
            'data' => PromoGcRequestResource::collection($records),
            'columns' => ColumnHelper::$promoGcReleasing,
            'filters' => $request->only('date', 'search')

        ]);
    }

    public function denominationList(Request $request, $id)
    {
        // dd($id);
        $data = PromoGcRequestItem::
            join('denomination', 'denomination.denom_id', '=', 'promo_gc_request_items.pgcreqi_denom')
            ->selectRaw("pgcreqi_denom, pgcreqi_qty,pgcreqi_trid, pgcreqi_remaining,pgcreqi_denom, denomination.denomination, (denomination.denomination * pgcreqi_remaining) AS subtotal")
            ->where([['pgcreqi_trid', $id], ['pgcreqi_remaining', '>', 0]])->paginate(5)->withQueryString();

        $assignatories = Assignatory::assignatories($request);

        return response()->json([
            'denomination' => $data,
            'promo_releasing_no' => PromoGcReleaseToDetail::getMax(),
            'assignatories' => $assignatories
        ]);
    }

    public function scanBarcode(Request $request)
    {
        $request->validate([
            "barcode" => 'required_if:scanMode,false|nullable|digits:13',
            "bstart" => 'required_if:scanMode,true|nullable|digits:13',
            "bend" => 'required_if:scanMode,true|nullable|digits:13'
        ]);

        return $this->sessionScannedGc($request);
    }

    private function sessionScannedGc(Request $request)
    {
        $bstart = $request->bstart;
        $bend = $request->bend;
        $denom_id = $request->denom_id;
        $barcode = $request->barcode;
        $reqid = $request->reqid;

        $sessionName = $this->sessionName;
        $r = PromoGcReleaseToDetail::max('prrelto_relnumber');
        $relNum = $r ? $r + 1 : 1;


        $remainGc = PromoGcRequestItem::where([['pgcreqi_denom', $denom_id], ['pgcreqi_trid', $reqid]])->value('pgcreqi_remaining');
        $scannedGcSession = collect($request->session()->get($sessionName, []))->filter(function ($item) use ($reqid, $denom_id) {
            return ($item['reqid'] == $reqid) && ($item['denomid'] == $denom_id);
        })->count();

        if ($remainGc > $scannedGcSession) {

            $res = [];
            if ($request->scanMode) { //Range Scan
                foreach (range($bstart, $bend) as $barcode) {
                    $res[] = $this->validateBarcode($request, $barcode, $remainGc, $scannedGcSession, $relNum, $sessionName);
                }
            } else {
                $res[] = $this->validateBarcode($request, $barcode, $remainGc, $scannedGcSession, $relNum, $sessionName);
            }
            return response()->json(['barcodes' => $res, 'sessionData' => $request->session()->get($sessionName)]);
        } else {
            return response()->json("Number of GC Scanned has reached the maximum number to received.", 400);
        }
    }

    private function validateBarcode(Request $request, $barcode, $remainGc, &$scannedGcSession, $relNum, $sessionName)
    {
        $reqid = $request->reqid;
        if ($denomId = Gc::where('barcode_no', $barcode)->value('denom_id')) {
            if (CustodianSrrItem::where('cssitem_barcode', $barcode)->exists()) {

                if (GcLocation::where('loc_barcode_no', $barcode)->doesntExist()) {

                    if (PromoGcReleaseToItem::where('prreltoi_barcode', $barcode)->doesntExist()) {

                        if (Gc::where([['barcode_no', $barcode], ['gc_treasury_release', '*']])->doesntExist()) {

                            $requestItem = PromoGcRequestItem::where('pgcreqi_trid', $reqid)
                                ->where('pgcreqi_denom', $denomId)
                                ->exists();

                            if ($requestItem) {

                                $remainingZero = PromoGcRequestItem::where([['pgcreqi_trid', $reqid], ['pgcreqi_remaining', 0]])
                                    ->where('pgcreqi_denom', $denomId)
                                    ->doesntExist();

                                if ($remainingZero) {

                                    $alreadyScanned = collect($request->session()->get($sessionName, []))->contains(function ($item) use ($barcode, $reqid, $denomId) {
                                        if (($item['denomid'] == $denomId)) {
                                            return $barcode === $item['barcode'];
                                        };
                                    });

                                    if (!$alreadyScanned) {
                                        //check again if the remain Gc still greater than the scanned gc otherwise $scannedGcSession++;
                                        if ($remainGc > $scannedGcSession) {

                                            $barcodearr = Gc::join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                                                ->select('gc.barcode_no', 'denomination.denomination', 'gc.pe_entry_gc')
                                                ->where('barcode_no', $barcode)->first();

                                            $request->session()->push($sessionName, [
                                                "barcode" => $barcode,
                                                "denomid" => $denomId,
                                                "relid" => NumberHelper::leadingZero($relNum),
                                                "reqid" => $reqid,
                                                "productionnum" => $barcodearr->pe_entry_gc,
                                                "denomination" => $barcodearr->denomination,
                                                "promo" => "Promo GC"
                                            ]);
                                            $scannedGcSession++;
                                            return [
                                                'message' => "GC Barcode {$barcode} successfully validated.",
                                                'status' => 200,
                                            ];
                                        } else {
                                            return [
                                                'message' => "Number of GC Scanned has reached the maximum number to received.",
                                                'status' => 400,
                                            ];
                                        }
                                    } else {
                                        return [
                                            'message' => "Barcode {$barcode} already scanned or used.",
                                            'status' => 400,
                                        ];
                                    }


                                } else {
                                    return [
                                        'message' => "There is no request for this denomination.",
                                        'status' => 400,
                                    ];
                                }
                            } else {
                                return [
                                    'message' => "There is no request for this denomination.",
                                    'status' => 400,
                                ];

                            }
                        } else {
                            return [
                                'message' => "GC Barcode # {$barcode} released as Institution GC.",
                                'status' => 400,
                            ];
                        }
                    } else {
                        return [
                            'message' => "GC Barcode # {$barcode} already received.",
                            'status' => 400,
                        ];
                    }
                } else {

                    return [
                        'message' => "GC Barcode # {$barcode} already allocated.",
                        'status' => 400,
                    ];
                }

            } else {
                return [
                    'message' => "GC Barcode # {$barcode} not yet registered.",
                    'status' => 400,
                ];
            }

        } else {
            return [
                'message' => "Barcode Number {$barcode} not found.",
                'status' => 400,
            ];
        }
    }

    public function viewScannedBarcode(Request $request)
    {
        $scannedBc = collect($request->session()->get($this->sessionName, []))->where('reqid', $request->id);

        $newArr = collect();
        $scannedBc->each(function ($item) use (&$newArr) {

            $gc = Gc::where('barcode_no', $item['barcode'])->value('pe_entry_gc');

            $gcLocation = GcLocation::where('loc_barcode_no', $item['barcode'])->value('loc_gc_type');
            $denomination = Denomination::where('denom_id', $item['denomid'])->value('denomination');

            $type = $gcLocation == 1 ? 'Regular' : 'Special';

            $newArr[] = [
                'barcode' => $item['barcode'],
                'pro' => $gc,
                'denomination' => NumberHelper::currency($denomination),
                'type' => $type
            ];
        });

        return response()->json(ArrayHelper::paginate($newArr, 5));
    }

    public function formSubmission(Request $request)
    {
        return $this->promoGcReleasingService->submit($request);
        
    }
}
