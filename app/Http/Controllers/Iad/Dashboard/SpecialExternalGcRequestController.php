<?php

namespace App\Http\Controllers\Iad\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\ApprovedRequest;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Rmunate\Utilities\SpellNumber;

class SpecialExternalGcRequestController extends Controller
{
    public function approvedGc(Request $request)
    {

        $data = SpecialExternalGcrequest::with(
            'user:user_id,firstname,lastname',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'hasManySpecialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty'
        )
            ->select(
                'spexgc_company',
                'spexgc_reqby',
                'spexgc_num',
                'spexgc_dateneed',
                'spexgc_id',
                'spexgc_datereq',
            )
            ->where([['spexgc_status', 'approved'], ['spexgc_reviewed', '']])
            ->orderBy('spexgc_id')
            // ->get();
            ->paginate(10)
            ->withQueryString();

        // dd(SpecialExternalGcRequestResource::collection($data)->toArray($request));
        return inertia('Iad/Dashboard/ApprovedGcTable', [
            'data' => SpecialExternalGcRequestResource::collection($data),
            'columns' => ColumnHelper::$approvedGcForReviewed,
            'filters' => $request->only('search', 'date')
        ]);
    }

    public function viewApprovedGcRecord(Request $request, SpecialExternalGcrequest $id)
    {
        $record = $id->load(
            'user:user_id,firstname,lastname,usertype',
            'user.accessPage:access_no,title',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'approvedRequest:reqap_id,reqap_trid,reqap_preparedby,reqap_date,reqap_remarks,reqap_doc,reqap_checkedby,reqap_approvedby',
            'approvedRequest.user:user_id,firstname,lastname',
            'hasManySpecialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty',
            'document:doc_id,doc_trid,doc_fullpath,doc_type'
        );
        return inertia('Iad/Dashboard/ViewApprovedGcTable', [
            'data' => new SpecialExternalGcRequestResource($record),
            'title' => 'Special External Gc'
        ]);

    }

    public function barcodeSubmission(Request $request, $id)
    {
        $gc = SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_trid',
            'spexgcemp_denom',
            'spexgcemp_fname',
            'spexgcemp_lname',
            'spexgcemp_mname',
            'spexgcemp_extname',
            'spexgcemp_barcode',
            'spexgcemp_review',
            'spexgcemp_id'
        )
            ->where([
                ['spexgcemp_trid', $id],
                ['spexgcemp_review', ''],
                ['spexgcemp_barcode', $request->barcode]
            ])
            ->withWhereHas('specialExternalGcrequest', function ($q) {
                $q->where('spexgc_status', 'approved');
            })->first();

        if ($gc->isEmpty()) {
            return redirect()->back()->with('error', "GC Barcode # {$request->barcode} not Found!");
        }

        if (!empty($gc->spexgcemp_review)) {
            return redirect()->back()->with('error', "GC Barcode # {$request->barcode} already Reviewed!");
        }

        if ($gc->specialExternalGcrequest->spexgc_status != 'approved') {
            return redirect()->back()->with('error', "GC Barcode # {$request->barcode} GC request is still Pending!");
        }

        $sessionName = 'scanReviewGC';
        $toSession = [
            "lastname" => $gc->spexgcemp_lname,
            "firstname" => $gc->spexgcemp_fname,
            "middlename" => $gc->spexgcemp_mname,
            "extname" => $gc->spexgcemp_extname,
            "denom" => $gc->spexgcemp_denom,
            "barcode" => $gc->spexgcemp_barcode,
            "trid" => $id,
            "gcid" => $gc->spexgcemp_id
        ];
        $scanGc = collect($request->session()->get($sessionName, []));

        if ($scanGc->contains('barcode', $request->barcode)) {
            return redirect()->back()->with('error', "GC Barcode # {$request->barcode} already Scanned!");
        }

        $request->session()->push($sessionName, $toSession);
        return redirect()->back()->with('success', "GC Barcode # {$request->barcode} successfully Scanned!");

        //ajax.php search = gcreviewscangc
    }

    public function gcReview(Request $request, $id)
    {
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
        }

        return redirect()->back()->with('error', 'Please scan the Gc first!');
        //ajax.php search = gcreview

    }
}
