<?php
namespace App\Services\Iad;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\ApprovedRequest;
use App\Models\DtiApprovedRequest;
use App\Models\DtiBarcodes;
use App\Models\DtiGcRequest;
use App\Models\DtiGcRequestItem;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Services\Documents\FileHandler;
use App\Services\Iad\IadDashboardService;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Rmunate\Utilities\SpellNumber;

use function PHPUnit\Framework\isEmpty;

class SpecialExternalGcService extends FileHandler
{

    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'reports/externalReport';
    }

    public function viewDtiGcData(Request $request)
    {
        $data = DtiGcRequest::join('special_external_customer', 'special_external_customer.spcus_id', '=', 'dti_gc_requests.id')
            ->join('users', 'users.user_id', '=', 'dti_gc_requests.dti_reqby')
            ->join('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->where('dti_gc_requests.dti_status', 'approved')
            ->where('dti_gc_requests.dti_addemp', 'done')
            ->where('dti_approved_requests.dti_approvedtype', '!=', 'special external gc review')
            ->where('dti_reviewed', null)
            ->select(
                [
                    'dti_gc_requests.dti_company',
                    DB::raw('DATE_FORMAT(dti_gc_requests.dti_datereq, "%a, %b %d, %Y %l:%i %p") as dti_datereq'),
                    DB::raw('DATE_FORMAT(dti_gc_requests.dti_dateneed, "%b %d, %Y") as dti_dateneed'),
                    'dti_gc_requests.dti_num',
                    'dti_gc_requests.id',
                    'dti_gc_requests.dti_customer',
                    DB::raw('CONCAT(users.firstname," ",users.lastname) as reqby_name'),
                ]
            )->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('dti_gc_requests.dti_company', 'like', "%$search%")
                        ->orWhere('dti_gc_requests.dti_reqby', 'like', "%$search%")
                        ->orWhere('dti_gc_requests.dti_num', 'like', "%$search%")
                        ->orWhere('dti_gc_requests.dti_dateneed', 'like', "%$search%")
                        ->orWhere('dti_gc_requests.dti_datereq', 'like', "%$search%")
                        ->orWhere('dti_gc_requests.dti_customer', 'like', "%$search%")
                        ->orWhereRaw("CONCAT(users.firstname, ' ', users.lastname) like ?", ["%$search%"]);
                });
            })
            ->orderByDesc('dti_gc_requests.dti_num')
            ->paginate(10);

        $data->transform(function ($item) {
            $item->sub_total = DtiGcRequestItem::where('dti_trid', $item->dti_num)
                ->select(DB::raw('SUM(dti_denoms * dti_qty) as sub_total'))
                ->value('sub_total');
            return $item;
        });
        return $data;
    }

    public function approvedDtiGc(Request $request)
    {
        $query = DtiGcRequest::join('special_external_customer', 'special_external_customer.spcus_id', '=', 'dti_gc_requests.id')
            ->join('dti_gc_request_items', 'dti_gc_request_items.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->join('users', 'users.user_id', '=', 'dti_gc_requests.dti_reqby')
            ->join('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->join('dti_documents', 'dti_documents.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->where('dti_gc_requests.dti_status', 'approved')
            ->where('dti_gc_requests.id', $request->id)
            ->select(
                'dti_gc_requests.dti_remarks',
                'dti_gc_requests.dti_num',
                'dti_gc_Requests.id',
                'dti_gc_requests.dti_dateneed',
                'dti_approved_requests.dti_remarks as dti_approved_remarks',
                'dti_approved_requests.dti_doc',
                'dti_gc_requests.dti_datereq',
                'dti_gc_requests.dti_company',
                'dti_gc_requests.dti_customer',
                'dti_gc_requests.dti_reqby',
                'dti_gc_requests.dti_approvedby',
                'dti_gc_requests.dti_paymenttype',
                'dti_approved_requests.dti_checkby',
                'dti_gc_requests.dti_approveddate',
                'dti_documents.dti_fullpath',
                DB::raw("CONCAT(users.firstname, ' ', users.lastname) as reqby"),
                DB::raw("dti_gc_request_items.dti_qty * dti_gc_request_items.dti_denoms as total")
            )
            ->get();
        $query->transform(function ($item) {
            $dtiBarcodeQuery = DtiBarcodes::where('dti_trid', $item->dti_num)
                ->select(
                    'dti_barcodes.dti_barcode',
                    'dti_barcodes.voucher',
                    'dti_barcodes.address',
                    'dti_barcodes.dti_denom',
                    DB::raw("CONCAT(fname, ' ', COALESCE(mname, ''), ' ', lname, ' ', COALESCE(extname, '')) as completename")
                )
                ->get();

            $item->dti_barcodes = $dtiBarcodeQuery->isNotEmpty()
                ? $dtiBarcodeQuery->map(function ($data) {
                    return [
                        'dti_barcode' => $data->dti_barcode ?? '',
                        'voucher' => $data->voucher ?? '',
                        'address' => $data->address ?? '',
                        'dti_denom' => $data->dti_denom ?? '',
                        'completename' => $data->completename ?? ''
                    ];
                })
                : [
                    [
                        'dti_barcode' => '',
                        'voucher' => '',
                        'address' => '',
                        'dti_denom' => '',
                        'completename' => ''
                    ]
                ];

            return $item;


        });
        return $query;
    }
    public function approvedGc(Request $request)
    {
        // dd();
        $search = $request->search;
        return SpecialExternalGcrequest::with(
            'user:user_id,firstname,lastname',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'specialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty'
        )
            ->select(
                'spexgc_company',
                'spexgc_reqby',
                'spexgc_num',
                'spexgc_dateneed',
                'spexgc_id',
                'spexgc_datereq',
            )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('spexgc_company', 'like', '%' . $search . '%')
                        ->orWhere('spexgc_reqby', 'like', '%' . $search . '%')
                        ->orWhere('spexgc_num', 'like', '%' . $search . '%')
                        ->orWhere('spexgc_dateneed', 'like', '%' . $search . '%')
                        ->orWhere('spexgc_id', 'like', '%' . $search . '%')
                        ->orWhere('spexgc_datereq', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(firstname, ' ', lastname) like ?", ['%' . $search . '%']);
                        })
                        ->orWhereHas('specialExternalCustomer', function ($query) use ($search) {
                            $query->where('spcus_companyname', 'like', '%' . $search . '%')
                                ->orWhere('spcus_acctname', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('specialExternalGcrequestItems', function ($query) use ($search) {
                            $query->where('specit_denoms', 'like', '%' . $search . '%')
                                ->orWhere('specit_qty', 'like', '%' . $search . '%');
                        });
                });
            })
            ->where([['spexgc_status', 'approved'], ['spexgc_reviewed', '']])
            ->orderByDesc('spexgc_id')
            ->paginate(10)
            ->withQueryString();
    }
    public function viewApprovedGcRecord(Request $request, SpecialExternalGcrequest $id)
    {
        $retrievedSession = collect($request->session()->get('scanReviewGC', []))->filter(fn($item) => $item['trid'] == $id->spexgc_id);

        session(['countSession' => $retrievedSession->count()]);
        session(['denominationSession' => $retrievedSession->sum('denom')]);
        session(['scanGc' => $retrievedSession]);

        return $id->load(
            'user:user_id,firstname,lastname,usertype',
            'user.accessPage:access_no,title',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'approvedRequest:reqap_id,reqap_trid,reqap_preparedby,reqap_date,reqap_remarks,reqap_doc,reqap_checkedby,reqap_approvedby',
            'approvedRequest.user:user_id,firstname,lastname',
            'specialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty',
            'document:doc_id,doc_trid,doc_fullpath,doc_type'
        );
    }

    public function dtiScanBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|integer'
        ]);

        $barcode = DtiBarcodes::where([
            ['dti_trid', $request->id],
            ['dti_review', ''],
            ['dti_barcode', $request->barcode]
        ])
            ->withWhereHas('dtiGcRequest', function ($query) {
                $query->where('dti_status', 'approved');
            })->first([
                    'dti_trid',
                    'dti_denom',
                    'fname',
                    'lname',
                    'mname',
                    'extname',
                    'dti_barcode',
                    'dti_review',
                    'id'
                ]);
        if (!$barcode) {
            return response()->json([
                'error' => true,
                'message' => 'Opps',
                'description' => 'DTI Barcode # ' . $request->barcode . ' not found, please try other barcode.'
            ]);
        }
        $sessionName = 'scanReviewDTI';
        $scanDti = collect($request->session()->get($sessionName, []));
        // dd($scanDti);

        if ($scanDti->contains('barcode', $request->barcode)) {
            return response()->json([
                'error' => true,
                'message' => 'Opps',
                'description' => 'DTI Barcode # ' . $request->barcode . ' already scanned'
            ]);
        }
        $currentSession = $request->session()->get($sessionName, []);
        $currentSession[] = [
            "lastname" => $barcode->lname,
            "firstname" => $barcode->fname,
            "middlename" => $barcode->mname,
            "extname" => $barcode->extname,
            "denom" => $barcode->dti_denom,
            "barcode" => $barcode->dti_barcode,
            "id_trid" => $barcode->dti_trid,
            "gcid" => $barcode->id
        ];
        $request->session()->put($sessionName, $currentSession);

        $retrievedSession = collect($request->session()->get($sessionName, []))->filter(fn($item) => $item['id_trid'] == $barcode->dti_trid);
        // dd($retrievedSession->count());
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'description' => 'DTI Barcode # ' . $request->barcode . ' scanned successfully.',
            'countSession' => $retrievedSession->count(),
            'denominationSession' => $retrievedSession->sum('denom'),
            'scanDti' => $retrievedSession
        ]);
    }
    // this is the scan barcode function
    public function barcodeScan(Request $request, $id)
    {
        $request->validate([
            'barcode' => 'required|not_in:0'
        ]);
        $gc = SpecialExternalGcrequestEmpAssign::where([
            ['spexgcemp_trid', $id],
            ['spexgcemp_review', ''],
            ['spexgcemp_barcode', $request->barcode]
        ])
            ->withWhereHas('specialExternalGcrequest', function ($q) {
                $q->where('spexgc_status', 'approved');
            })->first([
                    'spexgcemp_trid',
                    'spexgcemp_denom',
                    'spexgcemp_fname',
                    'spexgcemp_lname',
                    'spexgcemp_mname',
                    'spexgcemp_extname',
                    'spexgcemp_barcode',
                    'spexgcemp_review',
                    'spexgcemp_id'
                ]);

        if ($error = $this->checkBarcodeError($request, $gc)) {
            return $error;
        }

        $sessionName = 'scanReviewGC';
        $scanGc = collect($request->session()->get($sessionName, []));

        if ($scanGc->contains('barcode', $request->barcode)) {
            return redirect()->back()->with('error', "GC Barcode # {$request->barcode} already Scanned!");
        }

        $request->session()->push($sessionName, [
            "lastname" => $gc->spexgcemp_lname,
            "firstname" => $gc->spexgcemp_fname,
            "middlename" => $gc->spexgcemp_mname,
            "extname" => $gc->spexgcemp_extname,
            "denom" => $gc->spexgcemp_denom,
            "barcode" => $gc->spexgcemp_barcode,
            "trid" => $id,
            "gcid" => $gc->spexgcemp_id
        ]);

        $retrievedSession = collect($request->session()->get($sessionName, []))->filter(fn($item) => $item['trid'] == $id);
        // dd($retrievedSession->count());

        return redirect()->back()->with([
            'success' => "GC Barcode # {$request->barcode} successfully Scanned!",
            'countSession' => $retrievedSession->count(),
            'denominationSession' => $retrievedSession->sum('denom'),
            'scanGc' => $retrievedSession,
        ]);
    }
    // dti submit function
    public function dti_review(Request $request)
    {
        // dd($request->all());
        // dd($request->id);
        $request->validate([
            'remarks' => 'required',
        ]);

        $isExist = DtiApprovedRequest::where([['dti_trid', $request->id], ['dti_approvedtype', 'special external gc review']])->exists();
        if ($isExist) {
            return response()->json([
                'error' => true,
                'message' => 'Error',
                'description' => 'DTI Request already reviewed.'
            ]);
        }
        // dd($request->session()->has('scanReviewDTI'));

        // dd($request->id);
        if ($request->session()->has('scanReviewDTI')) {
            DB::transaction(function () use ($request) {
                $update = DtiGcRequest::where('dti_num', $request->id)
                    ->update([
                        'dti_reviewed' => 'reviewed'
                    ]);
                // dd($update);

                if ($update) {
                    DtiApprovedRequest::create([
                        'dti_trid' => $request->id,
                        'dti_remarks' => $request->remarks,
                        'dti_approvedtype' => 'special external gc review',
                        'dti_date' => now(),
                        'dti_checkby' => $request->user()->user_id,
                        'dti_preparedby' => $request->user()->user_id
                    ]);

                    $scanDti = collect($request->session()->get('scanReviewDTI'));
                    $scanDti->each(function ($item) use ($request) {
                        if ($item['id_trid'] === $request->id) {
                            DtiBarcodes::where([
                                ['dti_trid', $item['id_trid']],
                                ['id', $item['gcid']]
                            ])->update(['dti_review' => '*']);
                        }
                    });
                    return response()->json([
                        'success' => true,
                        'message' => 'Success',
                        'description' => 'Dti request reviewed successfully'
                    ]);

                } else {
                    return response()->json([
                        'error' => true,
                        'message' => 'Oops',
                        'description' => 'Dti request already reviewed'
                    ]);
                }
            });
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Oops',
                'description' => 'Please scan gc first'
            ]);
        }
    }
    //this is the submit button function
    public function review(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'remarks' => 'required'
        ]);
        // dd($id);
        $isExist = ApprovedRequest::where([['reqap_trid', $id], ['reqap_approvedtype', 'special external gc review']])->exists();
        if ($isExist) {
            return redirect()->back()->with('error', 'GC Request already reviewed.');
        }
        if ($request->session()->has('scanReviewGC')) {

            DB::transaction(function () use ($id, $request) {
                $update = SpecialExternalGcrequest::where('spexgc_id', $id)->update([
                    'spexgc_reviewed' => 'reviewed'
                ]);

                if ($update) {
                    ApprovedRequest::create([
                        'reqap_trid' => $id,
                        'reqap_remarks' => $request->remarks,
                        'reqap_approvedtype' => 'special external gc review',
                        'reqap_date' => now(),
                        'reqap_preparedby' => $request->user()->user_id
                    ]);

                    $scanGc = collect($request->session()->get('scanReviewGC'));

                    $scanGc->each(function ($item) use ($id) {
                        if ($item['trid'] === $id) {
                            SpecialExternalGcrequestEmpAssign::where([
                                ['spexgcemp_trid', $item['trid']],
                                ['spexgcemp_id', $item['gcid']]
                            ])->update(['spexgcemp_review' => '*']);
                        };
                    });

                    return redirect()->back()->with('success', 'Request successfully reviewed.');

                } else {
                    return redirect()->back()->with('error', 'Request already reviewed');
                }
            });
        } else {
            return redirect()->back()->with('error', 'Please scan the Gc first!');
        }

    }

    public function reprint($id)
    {
        return $this->retrieveFile($this->folderName, "gcrspecial{$id}.pdf");
    }

    private function checkBarcodeError(Request $request, $gc)
    {
        if (is_null($gc) || empty($gc)) {
            if (is_null($gc) || empty($gc)) {
                return redirect()->back()->with('error', "GC Barcode # {$request->barcode} not Found!");
            }

            if (!empty($gc->spexgcemp_review)) {
                return redirect()->back()->with('error', "GC Barcode # {$request->barcode} already Reviewed!");
            }

            if ($gc->specialExternalGcrequest->spexgc_status != 'approved') {
                return redirect()->back()->with('error', "GC Barcode # {$request->barcode} GC request is still Pending!");
            }
            return null;
        }
    }
}
