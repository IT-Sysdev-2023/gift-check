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
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;


class EodServices extends FileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'eod';
    }
    public function getVerifiedFromStore($request)
    {

        $eod = StoreVerification::selectFilter()->leftJoin('users', 'user_id', '=', 'vs_by')
            ->leftJoin('customers', 'cus_id', '=', 'vs_cn')
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
    private function storeEodTransactionAlMo($item, $id)
    {
        StoreEodTextfileTransaction::create([
            'seodtt_eod_id' => $id,
            'seodtt_barcode' => $item['barcode'],
            'seodtt_line' => '008',
            'seodtt_creditlimit' => $item['denom'],
            'seodtt_credpuramt' => $item['denom'],
            'seodtt_addonamt' => 0,
            'seodtt_balance' => $item['balance'],
            'seodtt_transno' => $item['transno'],
            'seodtt_timetrnx' => $item['time'],
            'seodtt_bu' => $item['bu'],
            'seodtt_terminalno' => $item['terminal'],
            'seodtt_ackslipno' => $item['slipno'],
            'seodtt_crditpurchaseamt' => $item['purchase'],
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
    private function updateStoreVerFile($item)
    {
        StoreVerification::where('vs_barcode', $item['barcode'])->update([
            'vs_tf_used' => '*',
            'vs_tf_balance' => $item['balance'],
            'vs_tf_purchasecredit' => $item['denom'],
            'vs_tf_addon_amt' => 0,
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

    private function storeEodItemFile($item, $id)
    {
        StoreEodItem::create([
            'st_eod_barcode' => $item['barcode'],
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

    public function getEodList($request)
    {
        // dd($request->user()->it_type);
        if ($request->user()->it_type === '1') {
            $data = $this->getEodListCorporate($request);
            // dd($data->toArray());
        } else {
            if ($request->user()->store_assigned == 2) {
                $dbconnection = DB::connection('mysqltalibon');
            } else if ($request->user()->store_assigned == 5) {
                $dbconnection = DB::connection('mysqltubigon');
            } else if ($request->user()->store_assigned == 8) {
                $dbconnection = DB::connection('mysqlaltacitta');
            } else if ($request->user()->store_assigned == 7) {
                $dbconnection = DB::connection('mysqlmandaue');
            } else if ($request->user()->store_assigned == 6) {
                $dbconnection = DB::connection('mysqlcolon');
            }

            $collect = $this->getEodListCorporate($request);

            $collectPerStore = $dbconnection->table('store_eod')
                ->select('steod_id', 'steod_by', 'steod_storeid', 'steod_datetime', 'firstname', 'lastname', 'store_name')
                ->join('users', 'users.user_id', '=', 'store_eod.steod_by')
                ->join('stores', 'store_id', '=', 'steod_storeid')
                ->where('steod_storeid', $request->user()->store_assigned)
                ->orderByDesc('steod_datetime')
                ->get();

            $dataCollect = collect($collect->items())->merge(collect($collectPerStore));

            // **Manual Pagination**
            $page = request()->input('page', 1); // Get current page from request
            $perPage = 10; // Items per page
            $offset = ($page - 1) * $perPage; // Calculate offset

            $data = new LengthAwarePaginator(
                $dataCollect->slice($offset, $perPage)->values(), // Slice data for pagination
                $dataCollect->count(), // Total items
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        $data->transform(function ($item) {
            $item->storename = $item->store_name ?? null;
            $item->fullname = $item->firstname;
            $item->date = Date::parse($item->steod_datetime)->toFormattedDateString();
            $item->time = Date::parse($item->steod_datetime)->format('h:i A');
            return $item;
        });

        return $data;
    }

    public function getEodListCorporate($request)
    {
        return StoreEod::select('steod_id', 'steod_by', 'steod_storeid', 'steod_datetime', 'firstname', 'lastname', 'store_name')
            ->join('users', 'user_id', '=', 'steod_by')
            ->leftJoin('stores', 'store_id', '=', 'steod_storeid')
            ->where('steod_storeid', $request->user()->store_assigned)
            ->orderByDesc('steod_datetime')
            ->paginate(10)->withQueryString();
    }

    public function generatePdf(int $id)
    {
        return $this->retrieveFile("{$this->folderName}/treasuryEod", "eod{$id}.pdf");
    }

    public function getEodListIfCorporate($request, $id)
    {

        return StoreEod::where([
            ['steod_storeid', $request->user()->store_assigned],
            ['steod_id', $id],
        ])->exists();
    }
    public function getEodListDetails($request, $id)
    {
        // dd($request->user()->store_assigned);
        $data =  $this->getEodListIfCorporate($request, $id);

        if (!$data) {
            $dbconnection = false;
            if ($request->user()->store_assigned == 5) {
                $dbconnection = DB::connection('mysqltubigon');
            }
            if ($request->user()->store_assigned == 2) {
                $dbconnection = DB::connection('mysqltalibon');
            }
            if ($request->user()->store_assigned == 8) {
                $dbconnection = DB::connection('mysqlaltacitta');
            }
            if ($request->user()->store_assigned == 7) {
                $dbconnection = DB::connection('mysqlmandaue');
            }
            if ($request->user()->store_assigned == 6) {
                $dbconnection = DB::connection('mysqlcolon');
            }
            if ($dbconnection) {
                $query = $dbconnection->table('store_eod_items')
                    ->join('store_verification', 'store_eod_items.st_eod_barcode', '=', 'store_verification.vs_barcode')
                    ->leftJoin('customers', 'store_verification.vs_cn', '=', 'customers.cus_id')
                    ->leftJoin('users', 'store_verification.vs_by', '=', 'users.user_id')
                    ->leftJoin('gc_type', 'store_verification.vs_gctype', '=', 'gc_type.gc_type_id')
                    ->leftJoin('stores', 'store_verification.vs_store', '=', 'stores.store_id')
                    ->where('store_eod_items.st_eod_trid', $id)
                    ->orderByDesc('store_eod_items.st_eod_barcode')
                    ->select(
                        'store_eod_items.*',
                        'store_verification.vs_barcode',
                        'store_verification.vs_cn',
                        'store_verification.vs_store',
                        'store_verification.vs_by',
                        'store_verification.vs_date',
                        'store_verification.vs_reverifydate',
                        'store_verification.vs_gctype',
                        'store_verification.vs_tf_denomination',
                        'store_verification.vs_tf_balance',
                        'store_verification.vs_time',
                        'customers.cus_id',
                        'customers.cus_fname',
                        'customers.cus_lname',
                        'customers.cus_mname',
                        'customers.cus_namext',
                        'users.user_id',
                        'users.firstname',
                        'users.lastname',
                        'gc_type.gc_type_id',
                        'gc_type.gctype',
                        'stores.store_id',
                        'stores.store_name'
                    )
                    ->paginate(10);
            }
        } else {
            $query = StoreEodItem::join('store_verification', 'store_eod_items.st_eod_barcode', '=', 'store_verification.vs_barcode')
                ->leftJoin('customers', 'store_verification.vs_cn', '=', 'customers.cus_id')
                ->leftJoin('users', 'store_verification.vs_by', '=', 'users.user_id')
                ->leftJoin('gc_type', 'store_verification.vs_gctype', '=', 'gc_type.gc_type_id')
                ->leftJoin('stores', 'store_verification.vs_store', '=', 'stores.store_id')
                ->where('store_eod_items.st_eod_trid', $id)
                ->orderByDesc('store_eod_items.st_eod_barcode')
                ->select(
                    'store_eod_items.*',
                    'store_verification.vs_barcode',
                    'store_verification.vs_cn',
                    'store_verification.vs_store',
                    'store_verification.vs_by',
                    'store_verification.vs_date',
                    'store_verification.vs_reverifydate',
                    'store_verification.vs_gctype',
                    'store_verification.vs_tf_denomination',
                    'store_verification.vs_tf_balance',
                    'store_verification.vs_time',
                    'customers.cus_id',
                    'customers.cus_fname',
                    'customers.cus_lname',
                    'customers.cus_mname',
                    'customers.cus_namext',
                    'users.user_id',
                    'users.firstname',
                    'users.lastname',
                    'gc_type.gc_type_id',
                    'gc_type.gctype',
                    'stores.store_id',
                    'stores.store_name'
                )
                ->paginate(10);
        }
        // dd($query->toArray());

        return EodListDetailResources::collection($query);
    }
    public function storeVer($barcode)
    {
        return StoreVerification::where('vs_barcode', $barcode)->exists();
    }

    public function getEodListDetailsTxt($request, $barcode)
    {
        if ($this->storeVer($barcode)) {
            $data = StoreEodTextfileTransaction::select(
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
        } else {
            if ($request->user()->store_assigned == 5) {
                $dbconnection = DB::connection('mysqltubigon');
            }
            if ($request->user()->store_assigned == 2) {
                $dbconnection = DB::connection('mysqltalibon');
            }
            if ($request->user()->store_assigned == 8) {
                $dbconnection = DB::connection('mysqlaltacitta');
            }
            if ($request->user()->store_assigned == 7) {
                $dbconnection = DB::connection('mysqlmandaue');
            }
            if ($request->user()->store_assigned == 6) {
                $dbconnection = DB::connection('mysqlcolon');
            }

            if ($dbconnection) {
                $data =  $dbconnection->table('store_eod_textfile_transactions')->select(
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
        return $data;
    }

    function formatExcelTime($excelDecimalTime): string
    {
        $secondsInDay = 86400;
        $totalSeconds = (int) round($excelDecimalTime * $secondsInDay);

        return \Carbon\Carbon::createFromTime(0, 0, 0)
            ->addSeconds($totalSeconds)
            ->format('h:i:s A'); // or 'H:i:s' for 24-hr
    }
    public function processFileEod($request, $id)
    {
        $uploadedFiles = collect($request->file);
        $storeVerData = collect($request->data['data']);
        $allBarcodes = collect();

        $allBarcodes = collect();

        foreach ($uploadedFiles as $uploadedItem) {
            $uploadedFile = $uploadedItem['originFileObj'] ?? null;

            if (!$uploadedFile) {
                continue;
            }

            $excelData = Excel::toArray([], $uploadedFile);

            if (empty($excelData)) {
                continue;
            }

            foreach ($excelData as $sheet) {
                // Skip the first row (header)
                $rowsWithoutHeader = array_slice($sheet, 1);

                foreach ($rowsWithoutHeader as $row) {
                    $allBarcodes->push($row);
                }
            }
        }
        $dataGathered = [];

        $allBarcodes->each(function ($item) use (&$storeVerData, &$dataGathered) {

            $isMatch = collect($storeVerData)->contains(function ($val) use ($item) {
                return $val['vs_barcode'] === $item[3];
            });

            if ($isMatch) {

                $denom = StoreVerification::where('vs_barcode', $item[3])->value('vs_tf_denomination');

                $dataGathered[] = [
                    'barcode' => $item[3],
                    'credit' => $item[13],
                    'date' => Carbon::createFromDate(1900, 1, 1)->addDays($item[9] - 2),
                    'time' => $this->formatExcelTime($item[10]),
                    'bu' => $item[6],
                    'addon' => 0.0,
                    'transno' => $item[0],
                    'purchase' => $item[13],
                    'balance' => $item[14],
                    'terminal' => $item[7],
                    'denom' => $denom,
                    'slipno' => 0,
                ];
            }
        });

        $query = collect($dataGathered)->each(function ($item) use ($id) {
            DB::transaction(function () use (&$item, $id) {
                $this->updateStoreVerFile($item);
                $this->storeEodTransactionAlMo($item, $id);
                $this->storeEodItemFile($item, $id);
            });
        });

        if ($query) {
            return redirect()->back()->with([
                'status' => 'success',
                'title' => 'Success',
                'msg' => 'Process Eod Successfully',
            ]);
        } else {
            return redirect()->back()->with([
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'Opps Something Went Wrong!',
            ]);
        }
    }
}
