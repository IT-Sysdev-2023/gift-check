<?php

namespace App\Services\Treasury\Transactions;

use App\Models\ApprovedPromorequest;
use App\Models\Denomination;
use App\Models\Gc;
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
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'promoReleasedFile';
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

                    $remain = PromoGcRequestItem::where([['pgcreqi_denom', $item['denomid']], ['pgcreqi_trid', $request->rid]])->value('pgcreqi_remaining');
                    $remain--;

                    PromoGcRequestItem::where([['pgcreqi_trid', $request->rid], ['pgcreqi_denom', $item['denomid']]])->update(['pgcreqi_remaining' => $remain]);
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
}