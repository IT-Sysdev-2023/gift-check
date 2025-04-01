<?php

namespace App\Services\RetailStore;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\Denomination;
use App\Models\DtiBarcodes;
use App\Models\Gc;
use App\Models\GcRelease;
use App\Models\InstitutTransactionsItem;
use App\Models\LedgerCheck;
use App\Models\LedgerStore;
use App\Models\LostGcBarcode;
use App\Models\PromogcReleased;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\Store;
use App\Models\StoreGcrequest;
use App\Models\StoreReceived;
use App\Models\StoreReceivedGc;
use App\Models\StoreVerification;
use App\Models\TempReceivestore;
use App\Models\TransactionRevalidation;
use Illuminate\Support\Facades\Date;
use App\Services\RetailStore\RetailDbServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class RetailServices
{
    public function __construct(public RetailDbServices $dbservices)
    {
    }
    public function getDataApproved()
    {
        $data = ApprovedGcrequest::select(
            'agcr_request_relnum',
            'agcr_approved_at',
            'agcr_request_id',
            'agcr_approvedby',
            'agcr_preparedby',
            'agcr_rec',
            'agcr_id',
        )
            ->with(
                'storeGcRequest:sgc_id,sgc_store,sgc_date_request',
                'storeGcRequest.store:store_id,store_name',
                'user:user_id,firstname,lastname'
            )
            ->whereHas('storeGcRequest', function ($query) {
                $query->where('sgc_store', request()->user()->store_assigned);
            })
            ->leftJoin('users', 'users.user_id', '=', 'approved_gcrequest.agcr_approvedby')
            ->orderByDesc('agcr_request_relnum')
            ->paginate(10)->withQueryString();
        // dd( $data);

        $data->transform(function ($item) {
            $item->spgc_date_request = Date::parse($item->storeGcRequest->sgc_date_request)->toFormattedDateString();
            $item->agcr_date = Date::parse($item->agcr_approved_at)->toFormattedDateString();
            $item->storename = $item->storeGcRequest->store->store_name;
            $item->fullname = $item->user->full_name;
            return $item;
        });

        return $data;
    }
    public static function transanctionType($type)
    {
        $types = [
            '1' => 'partial',
            '2' => 'whole',
            '3' => 'final',
        ];

        return $types[$type] ?? 'none';
    }

    public function details($request)
    {
        $store = StoreReceived::where('srec_store_id', $request->user()->store_assigned)
            ->where('srec_receivingtype', 'treasury releasing')
            ->orderByDesc('srec_recid')
            ->first();

        if ($store) {
            $store = $store->srec_recid + 1;
        } else {
            $store = 1;
        }

        // dd($store);



        $approved = ApprovedGcrequest::select(
            'agcr_request_relnum',
            'agcr_id',
            'agcr_request_id',
            'agcr_preparedby',
            'agcr_approved_at',
            'agcr_stat',
        )
            ->with('storeGcRequest:sgc_id,sgc_num,sgc_num,sgc_date_request', 'user:user_id,firstname,lastname')
            ->where('agcr_request_relnum', $request->agc_num)
            ->first();

        if ($approved) {

            $approved->dateReq = Date::parse($approved->storeGcRequest->sgc_date_request)->toFormattedDateString();
            $approved->dateApp = Date::parse($approved->agcr_approved_at)->toFormattedDateString();
            $approved->type = self::transanctionType($approved->agcr_stat);
        }


        $release = GcRelease::with(['gc:denom_id,barcode_no', 'gc.denomination:denom_id,denomination', 'store:store_id,store_name'])
            ->where('rel_num', $request->agc_num)
            ->get()
            ->groupBy('gc.denom_id');

        $release->map(function ($group) use ($request) {

            return $group->map(function ($item) use ($request) {

                $count = TempReceivestore::where('trec_denid', $item->gc->denom_id)->where('trec_by', $request->user()->user_id)->count();

                $item->scanned = $count;

                $item->denom = $item->gc->denomination->denomination;

                $item->quantity = self::getQuantity($request, $item);

                $item->sub = $item->quantity * $item->denom;

                return $item;
            });
        });


        $total = 0;
        $totscanned = 0;
        $totquant = 0;

        foreach ($release as $key => $value) {
            $total += $value[0]->sub;
            $totscanned += $value[0]->scanned;
            $totquant += $value[0]->quantity;
        }


        return (object) [
            'store' => $store,
            'approved' => $approved,
            'release' => $release,
            'total' => NumberHelper::currency($total),
            'totscanned' => $totscanned,
            'totquant' => $totquant,
        ];
    }

    public static function getQuantity($request, $item)
    {

        return GcRelease::whereHas('gc', function ($query) use ($item) {
            $query->where('denom_id', $item->gc->denom_id);
        })->where('rel_num', $request->agc_num)->count();
    }

    public function GcPendingRequest()
    {
        $storeId = request()->user()->store_assigned;

        $results = StoreGcrequest::join('stores', 'store_gcrequest.sgc_store', '=', 'stores.store_id')
            ->join('users', 'users.user_id', '=', 'store_gcrequest.sgc_requested_by')
            ->where(function ($query) {
                $query->where('store_gcrequest.sgc_status', 0)
                    ->orWhere('store_gcrequest.sgc_status', 1);
            })
            ->where('store_gcrequest.sgc_store', $storeId)
            ->where('store_gcrequest.sgc_cancel', '')
            ->get();

        $results->transform(function ($item) {
            $item->dateRequest = Date::parse($item->sgc_date_request)->format('F-d-y');
            $item->dateNeeded = Date::parse($item->sgc_date_needed)->format('F-d-y');
            $item->requestedBy = $item->firstname . ' ' . $item->lastname;

            return $item;
        });

        return $results;
    }

    public function validateBarcode($request)
    {

        $existInGc = Gc::where('barcode_no', $request->barcode);

        $released = GcRelease::where('re_barcode_no', $request->barcode)
            ->where('rel_store_id', $request->user()->store_assigned)
            ->exists();

        $scanned = TempReceivestore::where('trec_barcode', $request->barcode)->where('trec_recnum', $request->recnum)
            ->exists();

        $received = StoreReceivedGc::where('strec_barcode', $request->barcode)->exists();

        if ($existInGc->exists()) {

            if ($existInGc->first()->denom_id == $request->denom_id) {

                if ($released) {
                    if (!$scanned) {
                        if (!$received) {

                            $this->dbservices->temReceivedStoreCreation($request);
                        } else {
                            return back()->with([
                                'msg' => 'Barcode Already Received',
                                'title' => 'Received',
                                'status' => 'warning',
                            ]);
                        }
                    } else {
                        return back()->with([
                            'msg' => 'Barcode Already Scanned',
                            'title' => 'Scanned',
                            'status' => 'warning',
                        ]);
                    }
                } else {

                    return back()->with([
                        'msg' => 'Opps Barcode is Invalid in this Location',
                        'title' => 'Wrong Location!',
                        'status' => 'error',
                    ]);
                }
            } else {
                return back()->with([
                    'msg' => 'Opps Its Looks like Denomination mismatch',
                    'title' => 'Wrong Denomination',
                    'status' => 'error',
                ]);
            }
        } else {

            return back()->with([
                'msg' => 'Barcode Dont Exists',
                'title' => 'Error Not Found',
                'status' => 'error',
            ]);
        }
    }

    public function submitEntry($request)
    {

        $gcs = TempReceivestore::where('trec_recnum', $request->recnum)
            ->where('trec_store', $request->user()->store_assigned)
            ->where('trec_by', $request->user()->user_id)->get();

        $lastIdRecnum = StoreReceived::orderByDesc('srec_id')->first();

        if ($lastIdRecnum) {
            $lastIdRecnum = $lastIdRecnum->srec_id + 1;
        } else {
            $lastIdRecnum = 1;
        }

        $lnumber = LedgerCheck::orderByDesc('cledger_id')->first()->cledger_no;

        $sledger_no = LedgerStore::where('sledger_store', $request->user()->store_assigned)->orderByDesc('sledger_no')->first();

        if ($sledger_no) {
            $sledger_no = $sledger_no->sledger_no;
        } else {
            $sledger_no = 1;
        }

        $storeName = Store::where('store_id', $request->user()->store_assigned)->first()->store_name;

        $data = (object) [
            'cledger_no' => str_pad((int) $lnumber + 1, strlen($lnumber), '0', STR_PAD_LEFT),
            'sledger_no' => str_pad((int) $sledger_no + 1, strlen($sledger_no), '0', STR_PAD_LEFT),
            'storename' => $storeName,
            'recnumid' => $lastIdRecnum,
            'gcs' => $gcs,
        ];

        $transaction = DB::transaction(function () use ($request, $data) {

            $this->dbservices
                ->storeIntoLedgerCheck($request, $data)
                ->storeIntoStoreReceived($request, $data->cledger_no)
                ->storeIntoStoreReceivedGc($request, $data)
                ->storeIntoLedgerStore($request, $data)
                ->removeTempStore($request)
                ->updateApprovedGcRequest($request);

            return true;
        });

        if ($transaction) {
            return back()->with([
                'msg' => 'Successfully Save Entry',
                'title' => 'Success',
                'status' => 'success',
            ]);
        } else {
            return back()->with([
                'msg' => 'Something Went Wrong!',
                'title' => 'Error',
                'status' => 'error',
            ]);
        }
    }

    public function submitVerify($request)
    {
        $found = false;
        $isRevalidateGC = false;
        $verifyGc = false;

        if (
            Gc::where('barcode_no', $request->barcode)->where('status', null)->exists() ||
            Gc::where('barcode_no', $request->barcode)->where('status', '')->exists()
        ) {
            if (InstitutTransactionsItem::where('instituttritems_barcode', $request->barcode)->exists()) {
                $found = true;
                $gctype = 1;
            }
            if (
                StoreReceivedGc::where('strec_barcode', $request->barcode)
                    ->where('strec_sold', '*')->where('strec_return', '')->exists()
            ) {
                $found = true;
                $gctype = 1;
            }

            if (PromogcReleased::where('prgcrel_barcode', $request->barcode)->exists()) {
                $found = true;
                $gctype = 4;
            }


            $bandgo = StoreReceivedGc::where('strec_barcode', $request->barcode)->where('strec_sold', '')
                ->where('strec_return', '')
                ->where('strec_bng_tag', '*')
                ->get();

            if (count($bandgo) > 0) {
                $found = true;
                $gctype = 6;
                $bngGC = true;
            }


            if ($found) {
                $tfilext = '.' . self::appSetting('txtfile_extension_internal');
                $denom = self::denomination($request);
            }
        } elseif (SpecialExternalGcrequestEmpAssign::where('spexgcemp_barcode', $request->barcode)->exists()) {

            $special = self::specialCount($request);

            if ($special > 0) {
                $tfilext = '.' . self::appSetting('txtfile_extension_external');

                $denom = self::specialDenomination($request);

                $found = true;
                $gctype = 3;
            } else {
                return back()->with([
                    'status' => 'error',
                    'title' => 'Opss Error',
                    'msg' => 'Barcode Not found.',
                ]);
            }
        } elseif (DtiBarcodes::where('dti_barcode', $request->barcode)->exists()) {

            $special = self::specialDtiCount($request);
            // dd($special);

            if ($special > 0) {

                $tfilext = '.' . self::appSetting('txtfile_extension_external');

                $denom = self::specialDtiDenomination($request);

                $found = true;
                $gctype = 3;
            } else {
                return back()->with([
                    'status' => 'error',
                    'title' => 'Opss Error',
                    'msg' => 'Barcode Not found.',
                ]);
            }
        }
        ;

        if (!$found) {

            if (self::blocked($request->barcode) === 'blocked') {

                return back()->with([
                    'status' => 'warning',
                    'msg' => 'GC is blocked and not allowed to used.',
                    'title' => '400 Invalid'
                ]);
            }

            return back()->with([
                'status' => 'error',
                'msg' => 'Barcode #' . $request->barcode . ' not found',
                'title' => 'Error!'
            ]);
        }

        $bngGC = false;

        if ($bngGC && $request->user()->store_assigned != $bandgo[0]->strec_storeid) {
            return back()->with([
                'status' => 'error',
                'msg' => 'Invalid Store ' . '<br/>' . ' Store Purchased: ' . $bandgo[0]->store_name,
                'title' => '400 Invalid'
            ]);
        }

        if ($bngGC && $request->payment != 'STORE DEPARTMENT') {
            return back()->with([
                'status' => 'warning',
                'msg' => 'BEAM AND GO GC are only allowed to redeemed at Store Department',
                'title' => '400 Invalid'
            ]);
        }

        $data = [
            'tfilext' => $tfilext,
            'gctype' => $gctype,
            'denom' => $denom,
            'customer' => Customer::where('cus_id', $request->customer)->first(),
        ];

        if (empty(self::verifyQueryData($request))) {
            $verified = false;
        } else {
            $verified = true;
        }

        if (!$request->reprint) {

            if ($verified) {

                $verQuery = self::verifyQueryData($request);

                if ($verQuery->vs_date <= today() && $verQuery->vs_tf_used == '*') {
                    return back()->with([
                        'msg' => 'GC Barcode # ' . $request->barcode . ' is already verified and used.',
                        'title' => 'Already Verified and Used',
                        'status' => 'warning',
                    ]);
                } else {

                    $revalidated = $this->quickCheckRevalidation($request);

                    if (empty($revalidated)) {
                        // dd();
                        return back()->with([
                            'msg' => 'GC Barcode # ' . $request->barcode . ' is already verified.',
                            'title' => 'Already Verified',
                            'status' => 'warning',
                        ]);
                    }

                    if ($revalidated->reval_revalidated != '0') {
                        // dd();
                        return back()->with([
                            'msg' => 'GC Barcode # ' . $request->barcode . ' is already reverified.',
                            'title' => 'Already Reverified',
                            'status' => 'warning',
                        ]);
                    }

                    if ($revalidated->trans_store != $request->user()->store_assigned) {
                        return back()->with([
                            'msg' => 'GC Revalidated at ' . $revalidated->store_name,
                            'title' => 'Already Reverified',
                            'status' => 'warning',
                            'date' => 'Date Revalidated: ' . Date::parse($revalidated->trans_datetime)->toFormattedDateString()
                        ]);
                    }

                    if (self::equalCustomer($request) === $request->customer) {
                        if (Date::parse($revalidated->trans_datetime)->format('Y-m-d') === today()->format('Y-m-d')) {
                            $isRevalidateGC = true;
                            $verifyGC = true;
                        } else {
                            return back()->with([
                                'msg' => 'GC Barcode # ' . $request->barcode . ' already verified at ' . $revalidated->store_name,
                                'title' => 'Already Verified',
                                'status' => 'error',
                            ]);
                        }
                    } else {
                        return back()->with([
                            'msg' => 'Invalid Customer Information',
                            'title' => 'Its seems like customer didnt match, Customer: ' . $this->customerName(self::equalCustomer($request)),
                            'status' => 'error',
                            'error' => 'invalidcustomer',
                            'data' => $revalidated,
                        ]);
                    }
                }
            } else {
                $verifyGC = true;
            }


            if ($gctype === 4) {
                $dtrelease = PromogcReleased::where('prgcrel_barcode', $request->barcode)->value('prgcrel_at');

                $days = AppSetting::where('app_tablename', 'promotional_gc_claim_expiration')->value('app_settingvalue');

                $enddt = Date::parse($dtrelease)->addDays($days)->format('Y-m-d');


                if ($enddt > today()) {

                    return back()->with([
                        'status' => 'error',
                        'msg' => 'Sorry the Promo Gift Check is Expired!.',
                        'title' => 'Expired'
                    ]);
                }
                $lost = self::lostGcQuery($request);

                if (!empty($lost) && empty($lost->lostgcb_status)) {
                    return back()->with([
                        'msg' => 'GC Barcode # ' . $request->barcode . ' seems lost.',
                        'title' => 'Lost Gc',
                        'status' => 'warning',
                        'error' => 'lost',
                        'data' => $lost
                    ]);
                }

                if ($verifyGC) {
                    if ($isRevalidateGC) {
                        $this->dbservices->updateRevalidation($request);
                    } else {
                        $lastid = $this->dbservices->storeInStoreVerification($request, $data);
                        $insertLast = $lastid->vs_id;
                    }

                    if ($request->payment != 'WHOLESALE') {
                        $this->dbservices->createtextfile($request, $data);
                    }

                    if ($isRevalidateGC) {

                        $this->dbservices->updateRevalidationTransaction($request);

                        return back()->with([
                            'status' => 'success',
                            'msg' => 'Successfully reverfied!.',
                            'title' => 'Reverified'
                        ]);
                    } else {

                        return back()->with([
                            'status' => 'success',
                            'msg' => 'Successfully Verified!.',
                            'title' => 'Reverified'
                        ]);
                    }
                }
            } else {
                // dd();

                $lost = self::lostGcQuery($request);

                if (!empty($lost) && empty($lost->lostgcb_status)) {
                    return back()->with([
                        'msg' => 'GC Barcode # ' . $request->barcode . ' seems lost.',
                        'title' => 'Lost Gc',
                        'status' => 'warning',
                        'error' => 'lost',
                        'data' => $lost
                    ]);
                }

                if ($verifyGC) {
                    // dd();
                    if ($isRevalidateGC) {
                        $this->dbservices->updateRevalidation($request);
                    } else {
                        $lastid = $this->dbservices->storeInStoreVerification($request, $data);
                        $insertLast = $lastid->vs_id;
                    }

                    if ($request->payment != 'WHOLESALE') {

                        $success = $this->dbservices->createtextfile($request, $data);

                        if (!$success) {
                            $this->dbservices->createtextfileSecondaryPath($request, $data);
                        }
                    }

                    if ($isRevalidateGC) {

                        $this->dbservices->updateRevalidationTransaction($request);

                        return back()->with([
                            'status' => 'success',
                            'msg' => 'Successfully reverified!.',
                            'title' => 'Verification'
                        ]);
                    } else {
                        return back()->with([
                            'status' => 'success',
                            'msg' => 'Successfully Verified!.',
                            'title' => 'Verification'
                        ]);
                    }
                }
            }
        } else {
            return 'reprint';
        }
    }

    private static function specialCount($request)
    {
        return SpecialExternalGcrequestEmpAssign::join('approved_request', 'reqap_trid', '=', 'spexgcemp_trid')
            ->where('spexgcemp_barcode', $request->barcode)
            ->where('reqap_approvedtype', 'special external gc review')
            ->where('spexgc_status', '!=', 'inactive')
            ->count();
    }

    private static function specialDtiCount($request)
    {
        return DtiBarcodes::join('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_barcodes.dti_trid')
            ->where('dti_barcode', $request->barcode)
            ->where('dti_approvedtype', 'special external gc review')
            ->where('dti_status', '!=', 'inactive')
            ->count();
    }

    private static function denomination($request)
    {
        return Gc::select('denomination')->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->where('barcode_no', $request->barcode)->value('denomination');
    }
    private static function lostGcQuery($request)
    {
        return LostGcBarcode::join('lost_gc_details', 'lostgcd_id', '=', 'lostgcb_repid')
            ->where('lostgcb_barcode', $request->barcode)->first();
    }
    private static function specialDenomination($request)
    {
        return SpecialExternalGcrequestEmpAssign::select('spexgcemp_denom')
            ->where('spexgcemp_barcode', $request->barcode)
            ->value('spexgcemp_denom');
    }
    private static function specialDtiDenomination($request)
    {
        return DtiBarcodes::select('dti_denom')
            ->where('dti_barcode', $request->barcode)
            ->value('dti_denom');
    }
    private static function equalCustomer($request)
    {
        return StoreVerification::select('vs_cn')->where('vs_barcode', $request->barcode)->value('vs_cn');
    }
    private static function verifyQueryData($request)
    {
        return StoreVerification::select('vs_date', 'vs_tf_used')
            ->join('stores', 'store_id', '=', 'vs_store')
            ->join('users', 'user_id', '=', 'vs_by')
            ->join('customers', 'cus_id', '=', 'vs_cn')
            ->where('vs_barcode', $request->barcode)
            ->orderByDesc('vs_id')
            ->first();
    }


    public function quickCheckRevalidation($request)
    {
        return TransactionRevalidation::select(
            'reval_id',
            'trans_store',
            'reval_trans_id',
            'trans_datetime',
            'reval_revalidated',
            'store_name'
        )
            ->join('transaction_stores', 'trans_sid', '=', 'reval_trans_id')
            ->join('stores', 'store_id', '=', 'trans_store')
            ->where('reval_barcode', $request->barcode)
            ->whereDate('trans_datetime', today())
            ->first();
    }
    public function customerName($id)
    {
        return Customer::select('cus_fname', 'cus_lname', 'cus_mname', 'cus_namext')->where('cus_id', $id)->first()->full_name;
    }

    public static function blocked($barcode)
    {
        $barcodes = [
            '1310000007883' => 'blocked',
            '1310000007884' => 'blocked',
            '1310000007885' => 'blocked',
            '131000010754' => 'blocked',
            '131000010755' => 'blocked',
            '131000010756' => 'blocked',
        ];

        return $barcodes[$barcode] ?? '';
    }

    public static function appSetting($column)
    {
        return AppSetting::where('app_tablename', $column)->first()->app_settingvalue;
    }

    public function availableGcList(Request $request)
    {
        $denom = Denomination::where('denom_status', 'active')->get();

        $gc = StoreReceivedGc::where('strec_storeid', $request->user()->store_assigned)
            ->when($request->id !== null, function ($query) use ($request) {
                return $query->where('strec_denom', $request->id);
            })
            ->when($request->barcode !== null, function ($query) use ($request) {
                return $query->where('strec_barcode', $request->barcode);
            })
            ->join('denomination', 'denomination.denom_id', '=', 'store_received_gc.strec_denom')
            ->where('denomination.denom_status', 'active')
            ->paginate(10)
            ->withQueryString();


        return Inertia::render('Retail/AvailableGcTable', [
            'denom' => $denom,
            'gc' => $gc
        ]);
    }

    public function getAvailableGC()
    {
        $total = 0;
        $subtotal = 0;
        $counts = [];
        $denoms = Denomination::where('denom_type', 'RSGC')
            ->where('denom_status', 'active')
            ->get();

        foreach ($denoms as $denom) {
            $availableGcCount = StoreReceivedGc::where('strec_storeid', request()->user()->store_assigned)
                ->where('strec_denom', $denom->denom_id)
                ->where('strec_sold', '')
                ->where('strec_transfer_out', '')
                ->where('strec_bng_tag', '')
                ->count();
            $counts[$denom->denom_id] = $availableGcCount;
        }
        foreach ($denoms as $denom) {
            $denom->count = $counts[$denom->denom_id] ?? 0;
        }

        foreach ($denoms as $item) {
            // dump($item->denomination);
            //  dump(  $item->count);
            $subtotal = $item->denomination * $item->count;
            $total += $subtotal;
        }
        $data = [
            'denoms' => $denoms,
            'total' => $total
        ];
        return $data;
    }

    public function getRevolvingFund($request)
    {
        $rfund = Store::where('store_id', $request->user()->store_assigned)->first();
        $getAvailableGc = self::getAvailableGC();

        $storeBudget = $rfund['r_fund'] - $getAvailableGc['total'];

        return $storeBudget;
    }


    public function storeEOD(Request $request)
    {
        $data = StoreVerification::select(
            'store_verification.vs_barcode',
            'store_verification.vs_tf_denomination',
            'users.firstname',
            'users.lastname',
            'store_verification.vs_time',
            'store_verification.vs_date',
            'store_verification.vs_tf_balance',
            'store_verification.vs_reverifydate',
            'store_verification.vs_reverifyby',
            'customers.cus_fname',
            'customers.cus_lname',
            'gc_type.gctype'
        )
            ->join('users', 'users.user_id', '=', 'store_verification.vs_by')
            ->join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->join('gc_type', 'gc_type.gc_type_id', '=', 'store_verification.vs_gctype')
            ->whereAny([
                'vs_barcode'
            ], 'like', '%' . $request->barcode . '%')
            ->where('store_verification.vs_store', $request->user()->store_assigned)
            ->where('store_verification.vs_tf_used', '')
            ->where('store_verification.vs_tf_eod', '')
            ->where(function ($query) {
                $query->whereDate('store_verification.vs_reverifydate', '=', now()->toDateString())
                    ->orWhereDate('store_verification.vs_date', '<=', now()->toDateString());
            })
            ->orderByDesc('store_verification.vs_id')
            ->get();

        $data->transform(function ($item) {
            $item->date = Date::parse($item->vs_date)->format('F d, Y');
            return $item;
        });

        return $data;
    }


    public function verifiedGc(Request $request)
    {


        $data = StoreVerification::join('customers', 'customers.cus_id', '=', 'store_verification.vs_cn')
            ->join('gc_type', 'gc_type.gc_type_id', '=', 'store_verification.vs_gctype')
            ->leftJoin('gc', 'gc.barcode_no', '=', 'store_verification.vs_barcode')
            ->leftJoin('transaction_revalidation', 'transaction_revalidation.reval_barcode', '=', 'store_verification.vs_barcode')
            ->leftJoin('transaction_stores', 'transaction_stores.trans_sid', '=', 'transaction_revalidation.reval_trans_id')
            ->Join('institut_transactions_items', 'institut_transactions_items.instituttritems_barcode', '=', 'store_verification.vs_barcode')
            ->Join('institut_transactions', 'institut_transactions.institutr_id', '=', 'institut_transactions_items.instituttritems_trid')
            ->whereAny([
                'vs_barcode'
            ], 'like', '%' . $request->barcode . '%')
            ->where('store_verification.vs_store', $request->user()->store_assigned)
            ->orderByDesc('vs_barcode')
            ->paginate()
            ->withQueryString();


        return $data;
    }


    public function generate_verified_gc_pdf($request, $data, $d1, $d2)
    {
        $pdf = Pdf::loadView('pdf/verifiedgcreport', [
            'data' => $data,
            'd1' => Date::parse($d1)->format('F d, Y'),
            'd2' => Date::parse($d2)->format('F d, Y'),
            'generatedby' => $request->user()->fullname,
            'dateGenerated' => now()->format('F d, Y')
        ])->setPaper('letter');
        return $pdf;
    }


}
