<?php

namespace App\Services\Treasury\Transactions;
use App\Models\InstitutCustomer;
use App\Models\InstitutPayment;
use App\Models\InstitutTransaction;
use App\Models\InstitutTransactionsItem;
use App\Models\LedgerBudget;
use Illuminate\Http\Request;
use App\Models\Gc;
use Illuminate\Support\Facades\DB;
class InstitutionGcSalesService
{
    private string $sessionName;
    public function __construct()
    {
        $this->sessionName = 'scanForReleasedCustomerGC';
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
        // dd($request->all());
        // $request->validate([
        //     // 'file' => 'required',
        //     'remarks' => 'required',
        //     "receivedBy" => 'required',
        //     "checkedBy" => 'required',

        //     'customer' => 'required',
        //     'paymentFund' => 'required',

        //     'paymentType.type' => 'required',

        //     // 'paymentType.amount' => [
        //     //     function ($attribute, $value, $fail) use ($request) {

        //     //         $total = $request->input('totalDenomination');
        //     //         if (($request->input('paymentType.type') == 'cash' || $request->input('paymentType.type') == 'cashcheck' || $request->input('paymentType.type') == 'ar') && (is_null($value) || $value == 0)) {
        //     //             $fail('The ' . $attribute . ' is required and cannot be 0');
        //     //         }
        //     //     },
        //     // ],
        //     'paymentType.amount' => [
        //         'required_if:paymentType.type,cash,cashcheck,ar',
        //         'min:1',
        //         'gt:totalDenomination',
        //         'nullable',
        //     ],

        //     'paymentType.bankName' => 'required_if:paymentType.type,check,cashcheck',
        //     'paymentType.accountNumber' => 'required_if:paymentType.type,check,cashcheck',
        //     'paymentType.checkNumber' => 'required_if:paymentType.type,check,cashcheck',
        //     'paymentType.cash' => 'required_if:paymentType.type,cashcheck',
        //     'paymentType.supDocu' => 'required_if:paymentType.type,gad',

        // ], [
        //     'paymentType.bankName' => 'The Bank name field is required',
        //     'paymentType.accountNumber' => 'The Account Number name field is required',
        //     'paymentType.checkNumber' => 'The Check Number field is required.',
        //     'paymentType.checkAmount' => 'The Amount field is required.',
        // ]);


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
        $relnum = InstitutTransaction::where('institutr_trtype', 'sales')->max('institutr_trnum');
        $relnumber = $relnum ? $relnum + 1 : 1;

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

                });

            } else {
                return redirect()->back()->with('error', 'Customer doesnt exist!');
            }
        } else {
            return redirect()->back()->with('error', 'Please Scan a Barcode First!');
        }


        // dd($relnumber); //releaseTreasuryCustomer ajax
    }
}