<?php
namespace App\Services\Iad;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\ApprovedRequest;
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

class SpecialExternalGcService extends FileHandler
{

    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'reports/externalReport';
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

        return redirect()->back()->with([
            'success' => "GC Barcode # {$request->barcode} successfully Scanned!",
            'countSession' => $retrievedSession->count(),
            'denominationSession' => $retrievedSession->sum('denom'),
            'scanGc' => $retrievedSession,
        ]);
    }

    public function review(Request $request, $id)
    {
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
        }else{
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
