<?php

namespace App\Services\Treasury\Transactions;
use App\Exports\InstitutTransactionExport;
use App\Helpers\ArrayHelper;
use App\Helpers\NumberHelper;
use App\Models\Assignatory;
use App\Models\Document;
use App\Models\InstitutCustomer;
use App\Models\InstitutPayment;
use App\Models\InstitutTransaction;
use App\Models\InstitutTransactionsItem;
use App\Models\LedgerBudget;
use App\Models\PaymentFund;
use App\Services\Documents\ExportHandler;
use App\Services\Documents\FileHandler;
use Illuminate\Http\Request;
use App\Models\Gc;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class InstitutionGcSalesService extends FileHandler
{
    private string $sessionName;
    public function __construct()
    {
        parent::__construct();
        $this->sessionName = 'scanForReleasedCustomerGC';
        $this->folderName = 'institutionDocs';
    }

    public function index(Request $request)
    {
        $trNumber = InstitutTransaction::where('institutr_trtype', 'sales')->max('institutr_trnum') + 1;

        $customer = InstitutCustomer::select(
            'ins_name as label',
            'ins_date_created as date',
            'ins_id as value'
        )
            ->where('ins_status', 'active')->orderByDesc('ins_date_created')->get();

        $paymentFund = PaymentFund::select(
            'pay_id as value',
            'pay_desc as label',
            'pay_dateadded as date'
        )->where('pay_status', 'active')->orderByDesc('pay_dateadded')->get();

        $sessionBarcode = $request->session()->get('scanForReleasedCustomerGC');
        $scannedBarcode = ArrayHelper::paginate($sessionBarcode, 5) ?? [];

        return (object) [
            'trNo' => $trNumber,
            'customer' => $customer,
            'paymentFund' => $paymentFund,
            'scannedBarcode' => $scannedBarcode,
            'sessionBarcode' => $sessionBarcode
        ];
    }
    public function barcodeScanning(Request $request)
    {
        $request->validate([
            'barcode' => 'required_if:switchMode,false|nullable|digits:13',
            'startBarcode' => 'required_if:switchMode,true|nullable|digits:13',
            'endBarcode' => 'required_if:switchMode,true|nullable|digits:13',
        ]);

        $response = [];

        $scannedBc = collect($request->session()->get($this->sessionName, []));

        if ($request->switchMode == 'true') {

            foreach (range($request->startBarcode, $request->endBarcode) as $barcode) {
                $response[] = $this->validateBarcode($request, $barcode, $scannedBc);
            }
        } else {
            $response[] = $this->validateBarcode($request, $request->barcode, $scannedBc);
        }
        return redirect()->back()->with('scanGc', $response);
    }

    public function validateBarcode(Request $request, $barcode, $scannedBc)
    {

        $gc = Gc::where('barcode_no', $barcode)->first();

        if ($gc) {

            if ($gc->custodianSrrItems()->exists()) {

                if ($gc->gcLocation()->doesntExist()) {

                    if ($gc->promoGcReleasedToItems()->doesntExist()) {

                        if ($gc->institutTransactionsItem()->doesntExist()) {

                            if ($scannedBc->doesntContain(fn($item) => $item['barcode'] == $barcode)) {

                                $request->session()->push($this->sessionName, [
                                    "barcode" => $barcode,
                                    "denomination" => $gc->denomination->denomination
                                ]);
                                return [
                                    'success' => "Barcode {$barcode} succesfully scanned for Releasing.",
                                    'status' => 200
                                ];
                            } else {
                                return [
                                    'error' => "Barcode {$barcode} already Scanned.",
                                    'status' => 400
                                ];
                            }
                        } else {
                            return [
                                'error' => "Barcode {$barcode} already released to treasury customer.",
                                'status' => 400
                            ];
                        }
                    } else {
                        return [
                            'error' => "Barcode {$barcode} already released for promotional GC.",
                            'status' => 400
                        ];
                    }
                } else {
                    return [
                        'error' => "Barcode {$barcode} already allocated",
                        'status' => 400
                    ];
                }
            } else {
                return [
                    'error' => "Barcode {$barcode} needs validation",
                    'status' => 400
                ];
            }
        } else {
            return [
                'error' => "Barcode {$barcode} not Found!",
                'status' => 400
            ];
        }
    }

    public function destroyBarcode(Request $request, $barcode)
    {
        $scannedBc = collect($request->session()->get($this->sessionName, []));

        $filtered = $scannedBc->reject(function ($item) use ($barcode) {
            return $item['barcode'] == $barcode;
        });

        $request->session()->put($this->sessionName, $filtered);
        return redirect()->back()->with('success', "Barcode {$barcode} successfully deleted!");
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'file' => 'required',
            'remarks' => 'required',
            "receivedBy" => 'required',
            "checkedBy" => 'required',

            'customer' => 'required',
            'paymentFund' => 'required',

            'paymentType.type' => 'required',

            'paymentType.amount' => [
                'required_if:paymentType.type,cash,cashcheck,ar',
                'min:1',
                'gte:totalDenomination',
                'nullable',
            ],

            'paymentType.bankName' => 'required_if:paymentType.type,check,cashcheck',
            'paymentType.accountNumber' => 'required_if:paymentType.type,check,cashcheck',
            'paymentType.checkNumber' => 'required_if:paymentType.type,check,cashcheck',
            'paymentType.cash' => 'required_if:paymentType.type,cashcheck',
            'paymentType.supDocu' => 'required_if:paymentType.type,gad',

        ], [
            'paymentType.bankName' => 'The Bank name field is required',
            'paymentType.accountNumber' => 'The Account Number name field is required',
            'paymentType.checkNumber' => 'The Check Number field is required.',
            'paymentType.checkAmount' => 'The Amount field is required.',
        ]);

        $bankName = '';
        $bankAccountNo = '';
        $checkNum = '';
        $totalamtrec = 0;
        $checkamt = 0;
        $docname = '';
        $cash = 0;

        if ($request->paymentType['type'] === 'cash') {
            $totalamtrec = $request->paymentType['amount'] ?? 0;
            $cash = $totalamtrec;
        }
        if ($request->paymentType['type'] === 'check') {
            $bankName = $request->paymentType['bankName'] ?? '';
            $bankAccountNo = $request->paymentType['accountNumber'] ?? '';
            $checkNum = $request->paymentType['checkNumber'] ?? '';
            $totalamtrec = $request->paymentType['checkAmount'] ?? 0;
            $checkamt = $totalamtrec;
        }

        if ($request->paymentType['type'] === 'cashcheck') {
            $bankName = $request->paymentType['bankName'] ?? '';
            $bankAccountNo = $request->paymentType['accountNumber'] ?? '';
            $checkNum = $request->paymentType['checkNumber'] ?? '';
            $checkamt = $request->paymentType['amount'] ?? 0;
            $cash = $request->paymentType['cash'] ?? 0;

            $totalamtrec = $checkamt + $cash;
        }

        if ($request->paymentType['type'] === 'gad') {
            $docname = $request->paymentType['supDocu'] ?? '';
        }

        if ($request->paymentType['type'] === 'ar') {
            $cash = $request->paymentType['amount'] ?? '';
            $totalamtrec = $cash;
        }
        $customerid = $request->customer;
        $relnumlatest = InstitutTransaction::where('institutr_trtype', 'sales')->max('institutr_trnum');
        $relnum = $relnumlatest ? $relnumlatest + 1 : 1;

        if (collect($request->session()->get($this->sessionName))->isNotEmpty()) {
            if (InstitutCustomer::where('ins_id', $customerid)->exists()) {

                $change = $totalamtrec - $request->totalDenomination;
                DB::transaction(function () use ($change, $relnum, $customerid, $request, $checkamt, $cash, $totalamtrec, $docname, $bankName, $bankAccountNo, $checkNum) {

                    $insertedRecord = InstitutTransaction::create([
                        'institutr_trnum' => $relnum,
                        'institutr_cusid' => $customerid,
                        'institutr_paymenttype' => $request->paymentType['type'],
                        'institutr_trby' => $request->user()->user_id,
                        'institutr_remarks' => $request->remarks,
                        'institutr_date' => now(),
                        'institutr_receivedby' => $request->receivedBy,
                        'institutr_trtype' => 'sales',
                        'institutr_payfundid' => $request->paymentFund,
                        'institutr_checkedby' => $request->checkedBy,
                        'institutr_totamtpayable' => $request->totalDenomination,
                        'institutr_amtchange' => $change,
                        'institutr_checkamt' => $checkamt,
                        'institutr_cashamt' => $cash,
                        'institutr_totamtrec' => $totalamtrec,
                        'institutr_docname' => $docname
                    ]);

                    $paynum = InstitutPayment::paymentNumber();

                    InstitutPayment::create([
                        'insp_trid' => $insertedRecord->institutr_id,
                        'institut_bankname' => $bankName,
                        'institut_bankaccountnum' => $bankAccountNo,
                        'institut_checknumber' => $checkNum,
                        'institut_amountrec' => $request->totalDenomination,
                        'insp_paymentcustomer' => 'institution',
                        'insp_paymentnum' => $paynum
                    ]);

                    $sessionBarcode = collect($request->session()->get($this->sessionName));

                    $sessionBarcode->each(function ($item) use ($insertedRecord) {
                        InstitutTransactionsItem::create([
                            'instituttritems_barcode' => $item['barcode'],
                            'instituttritems_trid' => $insertedRecord->institutr_id
                        ]);

                        Gc::where('barcode_no', $item['barcode'])->update([
                            'gc_treasury_release' => '*'
                        ]);
                    });

                    $q = LedgerBudget::max('bledger_no');
                    $lnum = $q ? $q + 1 : 1;

                    LedgerBudget::create([
                        'bledger_no' => $lnum,
                        'bledger_trid' => $insertedRecord->institutr_id,
                        'bledger_datetime' => now(),
                        'bledger_type' => 'GCRELINS',
                        'bdebit_amt' => $request->totalDenomination
                    ]);

                    $this->saveMultiFiles($request, $insertedRecord->institutr_id, function ($id, $path) {
                        Document::create([
                            'doc_trid' => $id,
                            'doc_type' => 'Institution GC',
                            'doc_fullpath' => $path
                        ]);
                    });
                });

                $data = $this->dataForPdf($request, $change, $totalamtrec);

                $request->session()->forget($this->sessionName);

                $pdf = Pdf::loadView('pdf.institution', ['data' => $data]);
                $output = $pdf->output();

                (new ExportHandler())
                    ->setFolder($this->folderName)
                    ->setFileName($request->user()->user_id, $request->releasingNo)
                    ->exportToExcel( $this->dataForExcel($data))
                    ->exportToPdf($output);

                $stream = base64_encode($output);

                return redirect()->back()->with(['stream' => $stream, 'success' => 'Submission success']);
            } else {
                return redirect()->back()->with('error', 'Customer doesnt exist!');
            }
        } else {
            return redirect()->back()->with('error', 'Please Scan a Barcode First!');
        }

    }
    public function printAr($id)
    {
        $this->folderName = "reports/treasury_ar_report";
        return $this->retrieveFile($this->folderName, "arreport{$id}.pdf");
    }

    public function reprint(Request $request, $id)
    {
        $getFiles = $this->getFilesFromDirectory();

        $file = collect($getFiles)->filter(function ($file) use ($request, $id) {
            return Str::startsWith(basename($file), "{$request->user()->user_id}-{$id}");
        });

        return $this->retrieveFile($this->folderName, basename($file->first()));
    }

    public function transactionDetails(int|string $id)
    {
        $record = InstitutTransaction::select(
            'institutr_id',
            'institutr_cusid',
            'institutr_trby',
            'institutr_trnum',
            'institutr_receivedby',
            'institutr_date',
            'institutr_remarks',
            'institutr_paymenttype'
        )
            ->where('institutr_id', $id)
            ->with([
                'institutCustomer:ins_id,ins_name',
                'institutPayment:insp_trid,institut_bankname,institut_bankaccountnum,institut_checknumber,institut_amountrec',
                'user:user_id,firstname,lastname',
                'institutTransactionItem',
                'document',
            ])
            ->first();

        // Separate query for paginating the relationship
        $institutTransactionItems = $record->institutTransactionItem()->with('gc.denomination');

        return (object) [
            'record' => $record,
            'denomination' => $institutTransactionItems
        ];
    }
    public function excel(Request $request, $id){
        $getFiles = $this->getFilesFromDirectory('excel');

        $file = collect($getFiles)->filter(function ($file) use ($request, $id) {
            return Str::startsWith(basename($file), "{$request->user()->user_id}-{$id}");
        });

        return $this->download(basename($file->first()), 'excel');
    }
    
    private function dataForExcel($data){
        return new InstitutTransactionExport($data);
    }
    private function dataForPdf($request, $change, $cash)
    {
        $barcode = collect($request->session()->get($this->sessionName, []));
        $gr = $barcode->groupBy(fn($item) => $item['denomination'])->sortKeys();

        return [
            //Header
            'company' => [
                'name' => Str::upper('ALTURAS GROUP OF COMPANIES'),
                'department' => Str::title('Head Office - Treasury Department'),
                'report' => 'Institution GC Releasing Report',
            ],

            //SubHeader
            'subheader' => [
                'gc_rel_no' => $request->releasingNo,
                'date_released' => today()->toFormattedDateString(),
                'customer' => InstitutCustomer::find($request->customer)->ins_name,
            ],

            //Barcodes
            'barcode' => $gr,

            //Subfooter
            'summary' => [
                'total_no_of_gc' => $barcode->count(),
                'payment_type' => Str::title($request->paymentType['type']),
                'cash_received' => NumberHelper::currency($cash),
                'total_gc_amount' => NumberHelper::currency($request->totalDenomination),
                'change' => NumberHelper::currency($change),
                'paymentFund' => PaymentFund::find($request->paymentFund)->pay_desc,
            ],

            //Signatures
            'signatures' => [
                'prepared_released_by' => Str::upper($request->user()->full_name),
                'checked_by' => Str::upper(Assignatory::find($request->checkedBy)->assig_name),
                'received_by' => Str::upper($request->receivedBy),
            ],
        ];
    }
  
}