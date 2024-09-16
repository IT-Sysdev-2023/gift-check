<?php
namespace App\Services\Treasury\Transactions;

use App\Helpers\NumberHelper;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Rules\DenomQty;
use App\Services\Documents\UploadFileHandler;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class TransactionProductionRequest extends UploadFileHandler
{
	public function __construct()
	{
		parent::__construct();
		$this->folderName = "productionRequestFile";
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
		
		// dd($request->all());
		return $this->generatePdf($request);
		// Boundary

		if ($this->isAbleToRequest($request)) {
			return redirect()->back()->with('error', 'You have pending production request');
		}

		$request->validate([
			'remarks' => 'required',
			'dateNeeded' => 'required|date',
			'file' => 'required|image|mimes:jpeg,png,jpg|max:5048',
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

				$denom = collect($request->denom)->filter(fn($val) => isset ($val['qty']) && $val['qty'] > 0);

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

			return $this->generatePdf($request);

		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Something went wrong!');
		}
	}

	public function generatePdf(Request $request){
		$denom = collect($request->denom)->filter(fn($val) => isset ($val['qty']) && $val['qty'] > 0);

		$denomination = $denom->map(fn ($item) => ([ ...$item, 'denomination' => NumberHelper::format($item['denomination'])]));

		$data = [
			'pr' => $request->prNo,
			'budget' => NumberHelper::format(LedgerBudget::budget()),
			'dateRequested' => today()->toFormattedDateString(),
			'dateNeeded' => Date::parse($request->dateNeeded)->toFormattedDateString(),
			'remarks' => $request->remarks,
			'barcode' => $denomination,
			'preparedBy' => $request->user()->full_name
		];
		$pdf = Pdf::loadView('pdf.giftcheck', ['data' => $data]);
		// $pdf = Pdf::loadView(view: 'pdf.giftcheck', ['data' => '']);

        $pdf->setPaper('A3');

        $stream = base64_encode($pdf->output());

        return redirect()->back()->with(['stream' => $stream, 'success' => 'SuccessFully Requested']);
	}
}