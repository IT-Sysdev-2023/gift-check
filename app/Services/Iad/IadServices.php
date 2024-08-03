<?php

namespace App\Services\Iad;

use App\Models\Denomination;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;

class IadServices
{
    public function gcReceivingIndex()
    {
        $directoryPath = base_path('resources/js/Pages/Custodian/Textfiles/New');

        $files = File::files($directoryPath);

        $innerData = [];

        foreach ($files as $key => $item) {
            $contents = file_get_contents($item->getPathname());

            $array = explode("\r\n", $contents);


            foreach ($array as $line) {
                $parts = explode('|', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);

                    if ($key === 'Supplier Name') {
                        $innerData['supname'] = $value;
                    }
                    if ($key === 'GC E-REQUISITION NO') {
                        $innerData['reqno'] = $value;
                    }
                    if ($key === 'Transaction Date') {
                        $innerData['transdate'] = Date::parse($value)->toFormattedDateString();
                    }
                    if ($key === 'Receiving No') {
                        $innerData['recno'] = $value;
                    }
                    if ($key === 'Purchase Order No') {
                        $innerData['po'] = $value;
                    }
                }
            }
            $innerData['name'] = $item->getFilename();
            $result[] = $innerData;
        }
        return $result;
    }

    private static function transactionType(string $type)
    {
        $transaction = [
            'Supplier Name' => 'supname',
            'GC E-REQUISITION NO' => 'reqno',
            'Transaction Date' => 'transdate',
            'Receiving No' => 'recno',
            'Purchase Order No' => 'po',
            'Reference No' => 'refno',
            'Location Code' => 'loccode',
            'Reference PO No' => 'rpn',
            'Payment Terms' => 'payterms',
            'Department Code' => 'depcode',
            'Mode of Payment' => 'mop',
            'Remarks' => 'remarks',
            'Prepared By' => 'preby',
            'Checked By' => 'checkby',
            'SRR Type' => 'ssrType',
            'Purchase Date' => 'purdate',
        ];

        return $transaction[$type] ?? null;
    }
    private static function denomType(string $type)
    {
        $transaction = [
            '00002000' => '00002000',
            '00002001' => '00002001',
            '00002002' => '00002002',
            '00002003' => '00002003',
            '00002004' => '00002004',
            '00002005' => '00002005',
        ];

        return $transaction[$type] ?? null;
    }

    public function setupReceivingtxt($request)
    {
        $directory = base_path('resources/js/Pages/Custodian/Textfiles/New/' . $request->name);

        $files = File::get($directory);

        $array = explode("\r\n", $files);

        foreach ($array as $line) {
            $parts = explode('|', $line, 2);

            if (count($parts) === 2) {

                $key = trim($parts[0]);
                $value = trim($parts[1]);

                $sup = self::transactionType($key);

                $denom = self::denomType($key);


                $result[$sup] = $value;

                $denresult[$denom] = $value;
            }
            $result['name'] = $request->name;
        }
        return (object) [
            'result' => $result,
            'denomres' => $denresult,
        ];
    }

    public function getDenomination($denom)
    {

        $data =  Denomination::select('denomination', 'denom_fad_item_number', 'denom_code')
            ->where('denom_type', 'RSGC')
            ->where('denom_status', 'active')
            ->get();

        $data->transform(function ($item) use ($denom) {
            foreach ($denom as $key => $value) {
                if ($item->denom_fad_item_number == $key) {
                    $item->qty = $value;
                }
            }
            return $item;
        });

        return $data;
    }
}
