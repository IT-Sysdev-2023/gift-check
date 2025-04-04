<?php
namespace App\Services\Treasury\Transactions;

use App\Helpers\NumberHelper;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Rules\DenomQty;
use App\Services\Documents\FileHandler;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionProductionRequest extends FileHandler
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
	public function storeGc(Request $request, $bud)
	{

		if ($this->isAbleToRequest($request)) {
			return redirect()->back()->with('error', 'You have pending production request');
		}

		$request->validate([
			'remarks' => 'required',
			// 'dateNeeded' => 'required|date',
			// 'file' => 'required|image|mimes:jpeg,png,jpg|max:5048',
			'denom' => ['required', 'array', new DenomQty()],
		]);

        if($bud < $request->total){
            return redirect()->back()->with('error', 'Insufficient Budget Unable to proceed');
        }

		$filename = $this->createFileName($request);

		try {
			$denom = collect($request->denom)->filter(fn($val) => isset ($val['qty']) && $val['qty'] > 0);

			DB::transaction(function () use ($request, $filename, $denom) {

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

			return $this->generatePdf($request, $denom);

		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Something went wrong when generating PDF!');
		}
	}

	public function generatePdf(Request $request, Collection $denom)
	{

		$denomination = $denom->map(function ($item) {
			return array_merge($item, ['denomination' => NumberHelper::format($item['denomination'])]);
		});

		$data = [
			'pr' => $request->prNo,
			'budget' => NumberHelper::format(LedgerBudget::budget()),
			'dateRequested' => today()->toFormattedDateString(),
			// 'dateNeeded' => Date::parse($request->dateNeeded)->toFormattedDateString(),
			'remarks' => $request->remarks,

			'subtitle' => 'Production Request Form',
			'barcode' => $denomination,

			//signatures
			'signatures' => [
				'preparedBy' => [
					'name' => $request->user()->full_name,
					'position' => 'Sr Cash Clerk'
				]
			]
		];

		$pdf = Pdf::loadView('pdf.giftcheck', ['data' => $data]);

		//store pdf in storage
		$this->folderName = 'generatedTreasuryPdf/ProductionRequest';
		$this->savePdfFile($request, $request->prNo, $pdf->output());

		$stream = base64_encode($pdf->output());

		return redirect()->back()->with(['stream' => $stream, 'success' => 'SuccessFully Requested']);
	}
}
