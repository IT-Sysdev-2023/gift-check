<?php
namespace App\Services\Treasury\Transactions;

use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionProductionRequest extends UploadFileHandler
{
	public function __construct()
	{
		parent::__construct();
		$this->folderName = "productionRequestFile/";
	}
	public function storeGc(Request $request)
	{
		$request->validate([
			'remarks' => 'required',
			'dateNeeded' => 'required|date',
			'file' => 'nullable|image|mimes:jpeg,png,jpg|max:5048'
		]);
		$dept = $request->user()->usertype;

		if ($dept == 2) {
			$type = 1;
		} elseif ($dept == 6) {
			$type = 2;
		}

		$filename = $this->createFileName($request);

		DB::transaction(function () use ($request, $type, $filename) {

			$pr = ProductionRequest::create([
				'pe_num' => $request->prNo,
				'pe_requested_by' => $request->user()->user_id,
				'pe_date_request' => now(),
				'pe_date_needed' => $request->dateNeeded,
				'pe_file_docno' => $filename,
				'pe_remarks' => $request->remarks,
				'pe_type' => $type,
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
			$this->saveFile($request, $filename);
		});
	}
}