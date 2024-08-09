<?php
namespace App\Services\Treasury\Transactions;

use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Rules\DenomQty;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransactionProductionRequest extends UploadFileHandler
{
	public function __construct()
	{
		parent::__construct();
		$this->folderName = "productionRequestFile/";
	}

	private function isAbleToRequest(Request $request)
	{
		$q = ProductionRequest::whereRelation('user', 'usertype', $request->user()->usertype)->where('pe_status', 0)->count();
		if ($q) {
			return true;
		}
		return false;
	}
	public function storeGc(Request $request)
	{
		if ($this->isAbleToRequest($request)) {
			return redirect()->back()->with('error', 'You have pending production request');
		}

		$request->validate([
			'remarks' => 'required',
			'dateNeeded' => 'required|date',
			'file' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
			'denom' => ['required', 'array', new DenomQty()],
		]);

		$filename = $this->createFileName($request);

		try {
			DB::transaction(function () use ($request, $filename) {

				$pr = ProductionRequest::create([
					'pe_num' => $request->prNo,
					'pe_requested_by' => $request->user()->user_id,
					'pe_date_request' => now(),
					'pe_date_needed' => $request->dateNeeded,
					'pe_file_docno' => $filename,
					'pe_remarks' => $request->remarks,
					'pe_type' => userDepartment($request->user()),
					'pe_group' => 0
				]);

				$denom = collect($request->denom)->filter(fn($val) => isset ($val['qty']));

				$denom->each(function ($value) use ($pr) {
					ProductionRequestItem::create([
						'pe_items_denomination' => $value['id'],
						'pe_items_quantity' => $value['qty'],
						'pe_items_remain' => $value['qty'],
						'pe_items_request_id' => $pr->pe_id
					]);
				});

			});

			$this->saveFile($request, $filename);

			return redirect()->back()->with('success', 'SuccessFully Requested');

		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Something went wrong!');
		}
	}
}