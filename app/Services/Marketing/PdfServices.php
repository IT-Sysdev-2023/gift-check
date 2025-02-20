<?php

namespace App\Services\Marketing;

class PdfServices
{
    public function getHtml($checkby, $approveByFirstname ,$approveByLastname
    ,$dateRequest, $supplier,  $productionReqItems, $requestNo,  $dateNeed)
    {
        $html =
        ' <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GC E-Requisition</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                width: 100%;
            }

            .container {
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                padding: 20px;
            }

            header {
                text-align: center;
                margin-bottom: 20px;
            }

            h1 {
                font-size: 28px;
                margin-bottom: 10px;
            }

            h2 {
                font-size: 22px;
                margin-bottom: 10px;
                color: #555555;
            }

            h3 {
                font-size: 20px;
                margin-bottom: 30px;
                color: #333333;
            }

            .request-info, .supplier-info {
                margin-bottom: 30px;
            }

            .request-info p, .supplier-info p {
                font-size: 16px;
                line-height: 1.6;
            }

            .breakdown table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }

            .breakdown th, .breakdown td {
                border: 1px solid #dddddd;
                padding: 8px;
                text-align: left;
                font-size: 16px;
            }

            .breakdown th {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            .signatures {
                width: 100%;

            }

            .signature {
                float: left;
                width: 40%;
                margin-right: 3.33%;
                padding: 20px;
                box-sizing: border-box;
                text-align: center;
            }

            .signature p {
                margin-bottom: 5px;
            }

            .signature-line {
                border-top: 1px solid #000;
                padding-top: 5px;
                margin-top: 15px;
                text-align: center;
                font-size: 14px;
                color: #555555;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Header Section -->
            <header>
                <p>Marketing Department</p>
                <p>ALTURAS GROUP OF COMPANIES</p>
                <p>GC E-Requisition</p>
            </header>

            <!-- Request Information Section -->
            <section class="request-info">
                <p><strong>E-Req. No:</strong> ' . htmlspecialchars($requestNo) . '</p>
                <p><strong>Date Requested:</strong> ' . htmlspecialchars($dateRequest) . '</p>
                <p><strong>Date Needed:</strong> ' . htmlspecialchars($dateNeed) . '</p>
                <p><strong>Request:</strong> Request for gift cheque printing as per breakdown provided below.</p>
            </section>

            <!-- Table for Breakdown -->
            <section class="breakdown">
                <table>
                    <thead>
                        <tr>
                            <th>Denomination</th>
                            <th>Qty</th>
                            <th>Barcode No. Start</th>
                            <th>Barcode No. End</th>
                        </tr>
                    </thead>
                    <tbody>';


    foreach ($productionReqItems as $item) {
        $html .= '
                        <tr>
                            <td>' . htmlspecialchars($item->denomination) . '</td>
                            <td>' . htmlspecialchars($item->pe_items_quantity) . '</td>
                            <td>' . htmlspecialchars($item->barcodeStart) . '</td>
                            <td>' . htmlspecialchars($item->barcodeEnd) . '</td>
                        </tr>';
    }

    $html .= '
                    </tbody>
                </table>
            </section>

            <!-- Supplier Information Section -->
            <section class="supplier-info">
                <h4>Supplier Information</h4>
                <p><strong>Company Name:</strong>' . $supplier[0]->gcs_companyname . '</p>
                <p><strong>Contact Person:</strong>' . $supplier[0]->gcs_contactperson . '</p>
                <p><strong>Contact #:</strong> ' . $supplier[0]->gcs_contactnumber . '</p>
                <p><strong>Address:</strong>' . $supplier[0]->gcs_address . '</p>
            </section>

            <!-- Signatures Section -->
            <section class="signatures">
                <div class="signature">
                    <p><strong>Checked by:</strong></p>
                    <p>' . $checkby . '</p>
                    <div class="signature-line">Signature over Printed Name</div>
                </div>
                <div class="signature">
                    <p><strong>Prepared by:</strong></p>
                    <p>' . ucfirst($approveByFirstname) . ' ' . ucfirst($approveByLastname) . '</p>
                    <div class="signature-line">Signature over Printed Name</div>
                </div>
            </section>
        </div>
    </body>
    </html>';

    return $html;
    }
}
