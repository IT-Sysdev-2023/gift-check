<?php

namespace App\Services\Eod;

use App\Events\EodProcessEvent;
use App\Helpers\NumberHelper;
use App\Http\Resources\EodListDetailResources;
use App\Models\Store;
use App\Models\StoreEod;
use App\Models\StoreEodItem;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreVerification;
use Illuminate\Support\Facades\Storage;
use App\Services\Documents\FileHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


class EodServices extends FileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'eod';
    }
    public function getVerifiedFromStore($request)
    {
        $eod = StoreVerification::selectFilter()->join('users', 'user_id', '=', 'vs_by')
            ->join('customers', 'cus_id', '=', 'vs_cn')
            ->join('stores', 'store_id', '=', 'vs_store')
            ->join('gc_type', 'gc_type_id', '=', 'vs_gctype')
            ->where('vs_tf_used', '')
            ->where('vs_tf_eod', '')
            ->when($request->user()->it_type === '1', function ($q) use ($request) {
                $q->whereIn('vs_store', [1, 3, 4]);
            })
            ->when($request->user()->it_type === '2', function ($q) use ($request) {
                $q->where('vs_store', $request->user()->store_assigned);
            })
            ->where(function ($q) {
                $q->whereDate('vs_reverifydate', today())
                    ->orWhereDate('vs_date', '<=', today());
            })
            ->orderByDesc('vs_id')
            ->paginate(10)
            ->withQueryString();

        $eod->transform(function ($item) {
            $item->dateFmatted = Date::parse($item->vs_date)->toFormattedDateString();
            $item->fullname = $item->firstname . ' ' . $item->lastname;
            $item->status = is_null($item->vs_reverifydate) ? 'Verified' : 'Reverified';
            $item->date = Date::parse($item->vs_date)->toFormattedDateString();
            $item->formattedType = Str::title($item->gctype);
            $item->formattedDenom = NumberHelper::currency($item->vs_tf_denomination);
            return $item;
        });


        return $eod;
    }

    public function commandExecute($request)
    {

        if ($request->user()->it_type === '1') {
            $st = Store::where('store_id', 1)->first();
        } else {
            $st = Store::where('store_id', $request->user()->store_assigned)->first();
        }

        $driveLetter = 'Z:';
        $networkPath = rtrim($st->store_textfile_ip, '\\');

        // Unmap the drive first
        exec("C:\\Windows\\System32\\net.exe use $driveLetter /delete /y 2>&1", $unmap_output, $unmap_return_var);

        $command = "C:\\Windows\\System32\\net.exe use $driveLetter \"{$networkPath}\" /user:\"$st->username\" \"$st->new_password\" /persistent:yes 2>&1";
        exec($command, $output, $return_var);

        return $return_var;
    }


    public function processEod($request, $id)
    {

        $wholesaletime = now()->format('H:i');

        $store = StoreVerification::select(
            'username',
            'vs_tf',
            'vs_barcode',
            'vs_tf_denomination',
            'vs_tf_balance',
            'vs_tf_used',
            'vs_tf_eod',
            'vs_tf_eod2',
            'vs_store',
            'vs_payto'
        )->join('users', 'user_id', '=', 'vs_by')->where('vs_tf_used', '')
            ->where('vs_tf_eod', '')
            ->when($request->user()->it_type === '1', function ($q) use ($request) {
                $q->whereIn('vs_store', [1, 3, 4]);
            })
            ->when($request->user()->it_type === '2', function ($q) use ($request) {
                $q->where('vs_store', $request->user()->store_assigned);
            })
            ->where(function ($q) {
                $q->whereDate('vs_reverifydate', today())
                    ->orWhereDate('vs_date', '<=', today());
            })
            ->orderByDesc('vs_id')
            ->get();

        $rss = [];

        if (!$store) {

            return back()->with([
                'status' => 'error',
                'msg' => 'Opps Something went wrong',
            ]);
        } else {

            if ($store->count() === 0) {

                return back()->with([
                    'status' => 'error',
                    'msg' => 'No TextFile Exists'
                ]);
            }

            $txtfiles_temp = collect();

            $notFoundGC = [];

            $error = false;
            $sfiles = 1;
            $allf = $store->count();

            $r = $this->commandExecute($request);

            if ($r == 0) {
                $store->each(function ($item) use (&$txtfiles_temp, &$notFoundGC, &$error, &$sfiles, $allf) {

                    $ip = $this->getStoreIp($item->vs_store);

                    $quickCheck = collect(File::files($ip));

                    $res = $quickCheck->contains(function ($value, int $key) use ($item) {
                        return $value->getFilename() == $item->vs_tf;
                    });

                    if ($res) {
                        $txtfiles_temp[] = [
                            'ver_barcode' => $item->vs_barcode,
                            'ver_textfilename' => $item->vs_tf,
                            'ver_denom' => $item->vs_tf_denomination,
                            'ver_balance' => $item->vs_tf_balance,
                            'ver_used' => $item->vs_tf_used,
                            'ver_eod1' => $item->vs_tf_eod,
                            'txtfile_ip' => $ip,
                            'payto' => $item->vs_payto
                        ];
                    } else {
                        if ($item->vs_payto === 'WHOLESALE') {

                            $txtfiles_temp[] = [
                                'ver_barcode' => $item->vs_barcode,
                                'ver_textfilename' => $item->vs_tf,
                                'ver_denom' => $item->vs_tf_denomination,
                                'ver_balance' => $item->vs_tf_balance,
                                'ver_used' => $item->vs_tf_used,
                                'ver_eod1' => $item->vs_tf_eod,
                                'txtfile_ip' => $ip,
                                'payto' => $item->vs_payto
                            ];
                        } else {
                            $notFoundGC[] = $item->vs_tf;
                            $error = true;
                        }
                    }

                    EodProcessEvent::dispatch("Scanning Barcodes Please Wait...", $sfiles++, $allf, Auth::user());
                });

            } else {
                return back()->with([
                    'title' => 'Error',
                    'msg' => 'Opss Something went wrong in the server TextFile',
                    'status' => 'error',
                ]);
            }



            if ($error) {
                return back()->with([
                    'title' => 'Error',
                    'msg' => 'Opss there are some barcodes not found',
                    'status' => 'error',
                    'data' => $notFoundGC,
                ]);
            }

            $cFiles = 1;

            $allFiles = $txtfiles_temp->count();

            $txtfiles_temp->each(function ($item) use ($id, $wholesaletime, &$rss, &$cFiles, $allFiles) {

                if ($item['payto'] === 'WHOLESALE') {

                    DB::transaction(function () use ($item, $id, $wholesaletime) {

                        $this->updateStoreVerWholeSale($item);

                        $this->storeVerificationTextFile($item, $id, $wholesaletime);

                        $this->storeEodItem($item, $id);
                    });
                } else {

                    $file = $item['txtfile_ip'] . '\\' . $item['ver_textfilename'];

                    $text = File::get($file);

                    if (!File::exists($file)) {
                        $rss[] = [
                            'error' => 'GC Barcode # ' . $item['ver_textfilename'] . ' missing.',
                            'status' => 'error'
                        ];
                    }

                    $temp = explode(".", $file);

                    $extension = end($temp);

                    $allowedExts = collect([
                        'igc',
                        'egc',
                    ]);

                    if (!$allowedExts->contains($extension)) {

                        $rss[] = [
                            'msg' => 'GC Barcode # ' . $item['ver_textfilename'] . ' file extension is not allowed.',
                            'status' => 'error'
                        ];
                    }

                    $text = File::get($file);

                    $exp = explode("\n", $text);
                    // dd($exp);

                    $pc = '';
                    $am = '';
                    $amount = 0;

                    $exprn = [];
                    // dd($exp);

                    foreach ($exp as $key => $line) {

                        $exprn[] = explode(",", $line);

                        if ($key == 2) {
                            $pc = $exprn[$key][1];
                        }

                        if ($key == 3) {
                            $am = $exprn[$key][1];
                        }

                        if ($key == 4) {
                            $amount = $exprn[$key][1];

                            if ($amount < $item['ver_denom']) {

                                $success = $this->updateStoreVerification($item, $pc, $am, $amount);

                                if ($success) {
                                    $this->updateStoreVerificationEod($item);
                                }
                            }
                        }
                        if ($key > 7) {
                            if ($line !== '') {
                                $if8key = $exprn[$key];
                                $this->storeEodTransaction($item, $if8key, $id);
                            }
                        }
                    }


                    $success = $this->updateStoreVerificationEod($item);

                    $this->storeEodItem($item, $id);
                    if (!$success) {
                        return '';
                    }


                    $fileArchive = storage_path('app/public/archives');


                    if (File::exists($file . '.bak')) {
                        $copy = File::copy($file, $fileArchive . '\\' . $item['ver_textfilename'] . '.bak');

                        if ($copy) {

                            $deleted = File::delete($file . '.bak');

                            if (!$deleted) {

                                $rss[] = [
                                    'msg' => 'Error On Deleting' . $item['ver_textfilename'] . 'textfile',
                                    'status' => 'error'
                                ];
                            }
                        } else {

                            $rss[] = [
                                'msg' => 'Error On Copying' . $item['ver_textfilename'] . 'textfile',
                                'status' => 'error'
                            ];
                        }
                    }

                    if (File::copy($file, $fileArchive . '\\' . $item['ver_textfilename'])) {
                        $deleted = File::delete($file);

                        if (!$deleted) {
                            $rss[] = [
                                'msg' => 'Error On Deleting' . $item['ver_textfilename'] . 'textfile',
                                'status' => 'error'
                            ];
                        }
                    } else {
                        $rss[] = [
                            'msg' => 'Error On Copying' . $item['ver_textfilename'] . 'textfile',
                            'status' => 'error'
                        ];
                    }
                }

                EodProcessEvent::dispatch("End of date is now in progress please wait ... ", $cFiles++, $allFiles, Auth::user());
            });
        }
        if ($rss) {
            return back()->with([
                'status' => 'error',
                'title' => 'Error',
                'data' => $rss,
            ]);
        } else {
            return back()->with([
                'status' => 'success',
                'title' => 'Success',
                'msg' => 'Success Process Eod'
            ]);
        }
    }
    public function getStoreIp($store)
    {
        return Store::where('store_id', $store)->value('store_textfile_ip');
    }
    private function storeEodTransaction($item, $exprn, $id)
    {
        StoreEodTextfileTransaction::create([
            'seodtt_eod_id' => $id,
            'seodtt_barcode' => $item['ver_barcode'],
            'seodtt_line' => $exprn[0],
            'seodtt_creditlimit' => $exprn[1],
            'seodtt_credpuramt' => $exprn[2],
            'seodtt_addonamt' => $exprn[3],
            'seodtt_balance' => $exprn[4],
            'seodtt_transno' => $exprn[5],
            'seodtt_timetrnx' => $exprn[6],
            'seodtt_bu' => $exprn[7],
            'seodtt_terminalno' => $exprn[8],
            'seodtt_ackslipno' => $exprn[9],
            'seodtt_crditpurchaseamt' => $exprn[10],
        ]);
    }
    private function updateStoreVerification($item, $pc, $am, $amount)
    {
        StoreVerification::where('vs_barcode', $item['ver_barcode'])->update([
            'vs_tf_used' => '*',
            'vs_tf_balance' => $amount,
            'vs_tf_purchasecredit' => $pc,
            'vs_tf_addon_amt' => $am,
        ]);
        return true;
    }

    private function updateStoreVerWholeSale($item)
    {
        StoreVerification::where('vs_barcode', $item['ver_barcode'])->update([
            'vs_tf_used' => '*',
            'vs_tf_balance' => '0',
            'vs_tf_purchasecredit' => $item['ver_denom'],
            'vs_tf_eod' => '1',
        ]);
    }
    private function storeVerificationTextFile($item, $id, $wholesaletime)
    {
        StoreEodTextfileTransaction::create([
            'seodtt_eod_id' => $id,
            'seodtt_barcode' => $item['ver_barcode'],
            'seodtt_line' => '000',
            'seodtt_creditlimit' => $item['ver_denom'],
            'seodtt_credpuramt' => $item['ver_denom'],
            'seodtt_addonamt' => '0',
            'seodtt_balance' => '0',
            'seodtt_transno' => '0',
            'seodtt_timetrnx' => $wholesaletime,
            'seodtt_bu' => '',
            'seodtt_terminalno' => 'WHOLESALE',
            'seodtt_ackslipno' => '0',
            'seodtt_crditpurchaseamt' => $item['ver_denom'],
        ]);
    }

    private function storeEodItem($item, $id)
    {
        StoreEodItem::create([
            'st_eod_barcode' => $item['ver_barcode'],
            'st_eod_trid' => $id,
        ]);
    }

    private function updateStoreVerificationEod($item)
    {
        StoreVerification::where('vs_barcode', $item['ver_barcode'])->update([
            'vs_tf_eod' => '1',
        ]);

        return true;
    }

    public function getEodList()
    {

        $data = StoreEod::select('steod_id', 'steod_by', 'steod_storeid', 'steod_datetime')
            ->with('user:user_id,firstname,lastname', 'store:store_id,store_name')
            ->orderByDesc('steod_datetime')->paginate(10)->withQueryString();

        $data->transform(function ($item) {
            $item->storename = $item->store->store_name ?? null;
            $item->fullname = $item->user->fullname;
            $item->date = Date::parse($item->steod_datetime)->toFormattedDateString();
            $item->time = Date::parse($item->steod_datetime)->format('h:i A');
            return $item;
        });

        return $data;
    }

    public function generatePdf(int $id)
    {
        return $this->retrieveFile("{$this->folderName}/treasuryEod", "eod{$id}.pdf");
    }
    public function getEodListDetails($id)
    {

        $query = StoreEodItem::with(
            'storeverification:vs_barcode,vs_cn,vs_store,vs_by,vs_date,vs_reverifydate,vs_gctype,vs_tf_denomination,vs_tf_balance,vs_time',
            'storeverification.customer:cus_id,cus_fname,cus_lname,cus_mname,cus_namext',
            'storeverification.user:user_id,firstname,lastname',
            'storeverification.type:gc_type_id,gctype',
            'storeverification.store:store_id,store_name'
        )
            ->where('st_eod_trid', $id)
            ->orderByDesc('st_eod_barcode')
            ->paginate(10);

        return EodListDetailResources::collection($query);
    }

    public function getEodListDetailsTxt($barcode)
    {
        return StoreEodTextfileTransaction::select(
            'seodtt_line',
            'seodtt_creditlimit',
            'seodtt_credpuramt',
            'seodtt_addonamt',
            'seodtt_balance',
            'seodtt_transno',
            'seodtt_timetrnx',
            'seodtt_bu',
            'seodtt_terminalno',
            'seodtt_ackslipno',
            'seodtt_crditpurchaseamt'
        )->where('seodtt_barcode', $barcode)
            ->orderBy('seodtt_id')
            ->get();
    }
}
