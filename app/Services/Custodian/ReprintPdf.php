<?php

namespace App\Services\Custodian;

use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class ReprintPdf
{
    public function specialGcRequestEmpassign($id)
    {
        return SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_trid',
            'spexgcemp_denom',
            'spexgcemp_fname',
            'spexgcemp_lname',
            'spexgcemp_mname',
            'spexgcemp_extname',
            'voucher',
            'address',
            'department',
            'spexgcemp_barcode'
        )->where('spexgcemp_trid', $id);
    }

    public function specialGcRequest($id)
    {
        return SpecialExternalGcrequest::select(
            'spexgc_datereq',
            'spexgc_dateneed',
            'spexgc_num'
        )->where('spexgc_id', $id)->first();
    }
    public function reprintRequestService($id)
    {
        // dd($id);
        $html = $this->htmlStructure($id);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);


        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $stream = base64_encode($output);

        $filename = 'reprintRequest at ' . today()->toFormattedDateString() . '.pdf';

        $filePathName = storage_path('app/' . $filename);

        if (!file_exists(dirname($filePathName))) {
            mkdir(dirname($filePathName), 0755, true);
        }

        Storage::put($filename, $output);

        $filePath = route('download', ['filename' => $filename]);

        return inertia('Custodian/Result/ReprintRequestResult', [
            'filePath' => $filePath,
            'stream' => $stream,
            'id' => $id
        ]);
    }

    public function  htmlStructure($id)
    {
        $data = $this->specialGcRequestEmpassign($id)->get();
        $sprequest = $this->specialGcRequest($id);

        $html = '<html><body style="font-family: Calibri, Arial, Helvetica, sans-serif;">';
        $html .= '<div style="text-align: center">';
        $html .= '<div style="font-size: 12px; font-weight: 600">ALTURAS GROUP OF COMPANIES</div>';
        $html .= '<div style="font-size: 11px; font-weight: 600">Head Office - Custodian Department</div>';
        $html .= '<div style="font-size: 12px; font-weight: 600">Special External GC Releasing Report</div>';
        $html .= '</div>';
        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 20px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 12px; font-weight: 600; text-align: left;">Request No. ' . $sprequest->spexgc_num . '</td>';
        $html .= '<td style="font-size: 12px; font-weight: 600; text-align: right;">Date Approved: ' .  Date::parse($sprequest->spexgc_datereq)->toFormattedDateString() . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 2px">';
        $html .= '<tr>';
        $html .= '<td style="font-size: 12px; font-weight: 600; text-align: left;">Customer No. ' . '' . '</td>';
        $html .= '<td style="font-size: 12px; font-weight: 600; text-align: right;">Date Needed: ' . Date::parse($sprequest->spexgc_dateneed)->toFormattedDateString() . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '<br><br>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border: 1px solid black;">
        <thead>
            <tr style="text-align: center; font-size: 10px;">
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Name Ext</th>
                <th>Denomination</th>
                <th>Barcode</th>
            </tr>

        </thead>


        <tbody>';

        $data->each(function ($item) use (&$html) {
            $html .= '<tr style="font-size: 10px; text-align:center;">
                    <td>' . $item->spexgcemp_lname . '</td>
                    <td>' . $item->spexgcemp_fname . '</td>
                    <td>' . $item->spexgcemp_mname . '</td>
                    <td>' . $item->spexgcemp_extname . '</td>
                    <td>' . $item->spexgcemp_denom . '</td>
                    <td>' . $item->spexgcemp_barcode . '</td>
                </tr>';
        });
        $html .= '</tbody></table>';

        $html .= '<div style="text-align: start; margin-top: 20px; margin-left: 2px">';
        $html .= '<div style="font-size: 12px; font-weight: 600">Ar#:  ' . '' . '</div>';
        $html .= '<div style="font-size: 12px; font-weight: 600">Total Gc: ' . $data->count() . '</div>';
        $html .= '<div style="font-size: 12px; font-weight: 600">Total Gc Amount: ' . $data->sum('spexgcemp_denom') . '</div>';
        $html .= '
<div style="width: 100%; font-size: 12px; text-align: center; margin-top: 50px;">
    <div style="display: inline-block; text-align: center; width: 40%; margin-right: 10%;">
        Prepared by:<br><br>
        _______________________________<br>
        (Signature Over Printed Name)
    </div>
    <div style="display: inline-block; text-align: center; width: 40%;">
        Received by:<br><br>
        _______________________________<br>
        (Signature Over Printed Name)
    </div>
</div>';

        $html .= '</div>';


        $html .= '</body></html>';

        return $html;
    }
}
