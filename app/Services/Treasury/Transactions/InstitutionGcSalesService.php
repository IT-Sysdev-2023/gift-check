<?php

namespace App\Services\Treasury\Transactions;
use Illuminate\Http\Request;
use App\Models\Gc;
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

        $filtered = $scannedBc->reject(function($item) use ($barcode) {
            return $item['barcode'] == $barcode;
        });

        $request->session()->put($this->sessionName, $filtered);
        return redirect()->back()->with('success', "Barcode {$barcode} successfully deleted!");
    }
}