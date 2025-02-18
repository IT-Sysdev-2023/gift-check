<?php

namespace App\Services\Treasury;

use App\Http\Resources\AllocationAdjustmentResource;
use App\Models\AllocationAdjustment;
use App\Models\AllocationAdjustmentItem;
use App\Services\Documents\FileHandler;
use Illuminate\Http\Request;
use App\Models\LedgerBudget;
use Illuminate\Support\Facades\DB;

class AdjustmentService extends FileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'Adjustment/Budget';
    }
    public static function budgetAdjustment()
    {

        $adjNo = DB::table('budgetadjustment')->select('adj_no')->orderByDesc('adj_no')->get();

        return [
            'adj_no' => $adjNo->isNotEmpty() ? $adjNo : '0001',
            'date_requested' => today()->toDateString(),
            'current_budget' => LedgerBudget::currentBudget(),
            'prepared_by' => request()->user()->firstname
        ];
    }

    public static function allocationAdjustment()
    {

        $record = AllocationAdjustment::with('store:store_id,store_name', 'gcType:gc_type_id,gctype', 'user:user_id,firstname,lastname')
            ->select('aadj_id', 'aadj_datetime', 'aadj_type', 'aadj_remark', 'aadj_loc', 'aadj_gctype', 'aadj_by')
            ->paginate()->withQueryString();
        return inertia('Treasury/Dashboard/Adjustment/AllocationAdjustment', [
            'title' => 'Allocation Adjustment',
            'records' => AllocationAdjustmentResource::collection($record),
            'columns' => ColumnHelper::$allocationAdjustment,
        ]);
    }

    public static function viewAllocationAdjustment(Request $request, string $id)
    {
        $record = AllocationAdjustmentItem::with([
            'gc' => function ($q) use ($request) {

                $q->with('denomination:denom_id,denomination')->select('barcode_no', 'denom_id');
            }
        ])
            ->filterDenomination($request)
            ->where('aadji_aadj_id', $id)
            ->orderBy('aadji_barcode')
            ->paginate(10)
            ->withQueryString();

        $denominations = AllocationAdjustmentItem::where('aadji_aadj_id', $id)
            ->whereHas('gc.denomination')
            ->with('gc.denomination:denom_id,denomination')
            ->get()
            ->pluck('gc.denomination')
            ->flatten()
            ->unique('denom_id')
            ->values();

        return response()->json(['record' => $record, 'denoms' => $denominations]);
    }

    public function storeBudgetAdjustment(Request $request)
    {
        $request->validate([
            "adjustmentNo" => "required",
            "budget" => "required|not_in:0",
            "remarks" => "required",
            "adjustmentType" => 'required'
        ]);

        $isExist = DB::table('budgetadjustment')
            ->join('users', 'users.user_id', '=', 'budgetadjustment.adj_requested_by')
            ->where('users.usertype', $request->user()->usertype)
            ->where('budgetadjustment.adj_request_status', 0)
            ->exists();

        if ($isExist) {
            return back()->with('error', 'You have pending budget adjustment request.');
        }

        try {
            DB::transaction(function () use ($request) {
                $filename = $this->createFileName($request);

                DB::table('budgetadjustment')->insert([
                    'adj_request' => $request->budget,
                    'adj_no' => $request->adjustmentNo,
                    'adj_requested_by' => $request->user()->user_id,
                    'adj_requested_at' => now(),
                    'adj_file_docno' => $filename,
                    'adjust_type' => $request->adjustmentType,
                    'adj_remarks' => $request->remarks,
                    'adj_request_status' => '0',
                    'adj_type' => userDepartment($request->user())
                ]);

                $this->saveFile($request, $filename);

            });
            return redirect()->back()->with('success', 'Successfully Submitted');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Something went wrong {$e}");

        }
    }

    public function budgetUpdateSubmission(Request $request)
    {
        $data = DB::table('budgetadjustment')->first();
        $imageToDestroy = $data->adj_file_docno;
        $image = '';
        if ($request->hasFile('file')) { //if new file is set
            $image = $this->createFileName($request);
        } else if (isset($request->file['url'])) { //if the stock file is removed
            $image = '';
        } else { //if the file is untouch keep the filename in database
            $image = $data->adj_file_docno;
        }
        $isSuccess = DB::table('budgetadjustment')->where('adj_id', $data->adj_id)
            ->update([
                'adj_request' => $request->budget,
                'adj_no' => $data->adj_no,
                'adj_requested_by' => $request->user()->user_id,
                'adj_requested_at' => $data->adj_requested_at,
                'adj_file_docno' => $image,
                'adjust_type' => $request->adjustmentType,
                'adj_remarks' => $request->remarks,
                'adj_request_status' => '0',
                'adj_type' => userDepartment($request->user())
            ]);

        if ($isSuccess) {
            $this->saveFile($request, $image);
            $this->destroyFile($request, $imageToDestroy);

            return redirect()->back()->with('success', 'Successfully Update');
        }
        return redirect()->back()->with('error', 'SOmething Went wrong with the server!');

    }
}