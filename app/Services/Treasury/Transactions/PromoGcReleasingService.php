<?php

namespace App\Services\Treasury\Transactions;

use App\Helpers\NumberHelper;
use App\Models\ApprovedPromorequest;
use App\Models\Assignatory;
use App\Models\CustodianSrrItem;
use App\Models\Denomination;
use App\Models\Gc;
use App\Models\GcLocation;
use App\Models\PromoGcReleaseToDetail;
use App\Models\PromoGcReleaseToItem;
use App\Models\InstitutPayment;
use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoGcReleasingService extends UploadFileHandler
{
    private string $sessionName;
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'promoReleasedFile';
        $this->sessionName = 'scannedPromo';
    }
    public function index()
    {
        return PromoGcRequest::select('pgcreq_reqby', 'pgcreq_reqnum', 'pgcreq_remarks', 'pgcreq_doc', 'pgcreq_datereq', 'pgcreq_id', 'pgcreq_dateneeded', 'pgcreq_total', 'pgcreq_relstatus')
            ->with('userReqby:user_id,firstname,lastname')
            ->withWhereHas(
                'approvedReq',
                fn($q) => $q->with('user:user_id,firstname,lastname')
                    ->select('reqap_preparedby', 'reqap_trid')
                    ->where('reqap_approvedtype', 'promo gc preapproved')
            )->where([['pgcreq_status', 'approved']])
            ->orderByDesc('pgcreq_id')->paginate()->withQueryString();
    }

    public function denominations(Request $request, $id){
        $data = PromoGcRequestItem::
            join('denomination', 'denomination.denom_id', '=', 'promo_gc_request_items.pgcreqi_denom')
            ->selectRaw("pgcreqi_denom, pgcreqi_qty,pgcreqi_trid, pgcreqi_remaining,pgcreqi_denom, denomination.denomination, (denomination.denomination * pgcreqi_remaining) AS subtotal")
            ->where([['pgcreqi_trid', $id], ['pgcreqi_remaining', '>', 0]])->paginate(5)->withQueryString();

        $assignatories = Assignatory::assignatories($request);

        return [
            'denomination' => $data,
            'promo_releasing_no' => PromoGcReleaseToDetail::getMax(),
            'assignatories' => $assignatories
        ];
    }

    public function barcodeScanning(Request $request){
        $request->validate([
            "barcode" => 'required_if:scanMode,false|nullable|digits:13',
            "bstart" => 'required_if:scanMode,true|nullable|digits:13',
            "bend" => 'required_if:scanMode,true|nullable|digits:13'
        ]);

        return $this->sessionScannedGc($request);
    }

    public function submit(Request $request)
    {
        $request->validate([
            // 'file' => 'required',
            'remarks' => 'required',
            "receivedBy" => 'required',
            'paymentType.type' => 'required',

            'paymentType.amount' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('paymentType.type') == 'cash' && (is_null($value) || $value == 0)) {
                        $fail('The ' . $attribute . ' is required and cannot be 0 if type is not Cash.');
                    }
                },
            ],
            'paymentType.customer' => 'required_if:paymentType.type,jv',
            "checkedBy" => 'required',
            'approvedBy' => 'required',
        ], [
            'paymentType.customer' => 'The customer field is required when payment type is jv.',
            'paymentType.amount' => 'The amount field is required when payment type is cash.',
        ]);
        $scannedBc = collect($request->session()->get('scannedPromo', []))->where('reqid', $request->rid);

        $ap = ApprovedPromorequest::max('apr_request_relnum');
        $relid = $ap ? $ap + 1 : 1;

        $bankName = '';
        $bankAccountNo = '';
        $checkNum = '';
        $amount = '';
        $customerJv = '';

        if ($request->paymentType['type'] === 'check') {
            $bankName = $request->paymentType['bankName'] ?? '';
            $bankAccountNo = $request->paymentType['accountNumber'] ?? '';
            $checkNum = $request->paymentType['checkNumber'] ?? '';
            $amount = $request->paymentType['checkAmount'] ?? '';
        }

        if ($request->paymentType['type'] === 'cash') {
            $amount = $request->paymentType['amount'] ?? '';
        }

        if ($request->paymentType['type'] === 'jv') {
            $customerJv = $request->paymentType['customer'] ?? '';

            $amount = $scannedBc->sum(function ($sr) {
                return Denomination::where('denom_id', $sr['denomid'])->value('denomination');
            });
        }
        $denomList = PromoGcRequestItem::select('pgcreqi_denom', 'pgcreqi_qty', 'pgcreqi_trid')
            ->where([['pgcreqi_trid', $request->rid], ['pgcreqi_remaining', '>', 0]])->get();

        //check if the denominations gc already scanned
        $isScanned = $denomList->map(function ($sr) use ($scannedBc) {
            $scanned = $scannedBc->where('denomid', $sr->pgcreqi_denom)->count();
            return $sr->pgcreqi_qty === $scanned;
        });

        if ($isScanned->every(fn($n) => $n)) {

            $status = 'whole';
            if (PromoGcReleaseToDetail::where('prrelto_trid', $request->rid)->count() > 0) {
                $status = 'final';
            }

            DB::transaction(function () use ($request, $status, $scannedBc, $relid, $bankName, $bankAccountNo, $checkNum, $amount, $customerJv) {

                $filename = $this->createFileName($request);

                $latest = PromoGcReleaseToDetail::create([
                    'prrelto_trid' => $request->rid,
                    'prrelto_relnumber' => PromoGcReleaseToDetail::getMax(),
                    'prrelto_docs' => $filename,
                    'prrelto_checkedby' => $request->checkedBy,
                    'prrelto_approvedby' => $request->approvedBy,
                    'prrelto_relby' => $request->user()->user_id,
                    'prrelto_date' => now(),
                    'prrelto_recby' => $request->receivedBy,
                    'prrelto_status' => $status,
                    'prrelto_remarks' => $request->remarks
                ]);

                $scannedBc->each(function ($item) use ($latest, $request) {
                    PromoGcReleaseToItem::create([
                        'prreltoi_barcode' => $item['barcode'],
                        'prreltoi_relid' => $latest->prrelto_id
                    ]);
                    Gc::where('barcode_no', $item['barcode'])->update(['gc_ispromo' => '*']);

                    $recordToUpdate = PromoGcRequestItem::where([['pgcreqi_trid', $request->rid], ['pgcreqi_denom', $item['denomid']]])
                        ->first();
                    if ($recordToUpdate) {
                        $recordToUpdate->pgcreqi_remaining -= 1;
                        $recordToUpdate->save();
                    }
                    // $remain = PromoGcRequestItem::where([['pgcreqi_denom', $item['denomid']], ['pgcreqi_trid', $request->rid]])->value('pgcreqi_remaining');
                    // $remain--;
                    // PromoGcRequestItem::where([['pgcreqi_trid', $request->rid], ['pgcreqi_denom', $item['denomid']]])->update(['pgcreqi_remaining' => $remain]);
                });

                $rstatus = $status == 'partial' ? 'partial' : 'closed';

                PromoGcRequest::where('pgcreq_id', $request->rid)->update(['pgcreq_relstatus' => $rstatus]);

                $this->saveFile($request, $filename);

                $approvedLatest = ApprovedPromorequest::create([
                    'apr_request_id' => $request->rid,
                    'apr_rec' => 0,
                    'apr_approvedby' => $request->approvedBy,
                    'apr_checkedby' => $request->checkedBy,
                    'apr_remarks' => $request->remarks,
                    'apr_approved_at' => now(),
                    'apr_preparedby' => $request->user()->user_id,
                    'apr_recby' => $request->receivedBy,
                    'apr_file_docno' => $filename,
                    'apr_stat' => 0,
                    'apr_paymenttype' => $request->paymentType['type'],
                    'apr_request_relnum' => $relid
                ]);

                $ip = InstitutPayment::max('insp_paymentnum');
                $paynum = $ip ? $ip + 1 : 1;

                InstitutPayment::create([
                    'insp_trid' => $approvedLatest->apr_id,
                    'insp_paymentcustomer' => 'promo',
                    'institut_bankname' => $bankName,
                    'institut_bankaccountnum' => $bankAccountNo,
                    'institut_checknumber' => $checkNum,
                    'institut_amountrec' => $amount,
                    'insp_paymentnum' => $paynum,
                    'institut_jvcustomer' => $customerJv
                ]);

                //baw nganong gi upload balik ang image, ni gibase rani sa daan code 
                $this->folderName = 'approvedGCRequest';
                $this->saveFile($request, $filename);
            });
            return redirect()->back()->with('success', 'Successfully Submitted');
        } else {
            return redirect()->back()->with('error', 'Please scan the Barcode First');
        }
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
}