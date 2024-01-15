<?php ob_start();  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Building Report By Machine</title>
</head>
</html>
<?php 

$pdf=new mPDF('th','A4', 0, '', 3, 3, 48.5, 5);
$pdf->AliasNbPages();

function txtHeader($date,$shift){
    $txtheader = "<table border='1' cellspacing='0' cellpadding='10' width='100%'>
        <tr>
            <th colspan='4' align='center'>
                <img src=./assets/images/STR.jpg style='height:50px; width:auto;' />
            </th>
            <th colspan='4' valign='middle'>
                <h2>Building Report By Machine</h2>
            </th>
        </tr>
        <tr>
            <td colspan='8' align='left' style='font-size:15px;'>
                <b>Date : </b> ".$date." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>Shift : </b>".$shift."
            </td>
        </tr>
        <tr>
            <td width='7%' align='center' style='font-size:12px;'>
                No
            </td>
            <td width='7%' align='center' style='font-size:12px;'>
                Mc
            </td>
            <td width='10%' align='center' style='font-size:12px;'>
                GT.Code
            </td>
            <td width='13%' align='center' style='font-size:12px;'> 
                Barcode No
            </td>
            <td width='22%' align='center' style='font-size:12px;'>
                Barcode No
            </td>
            <td width='19%' align='center' style='font-size:12px;'>
                BuildingDate
            </td>
            <td width='15%' align='center' style='font-size:12px;'>
                Disposition
            </td>
            <td width='7%' align='center' style='font-size:12px;'>
                Shift
            </td>
        </tr></table>";
    return $txtheader;
}

$dataheader = txtHeader($date,$shift);
$pdf->SetHTMLHeader($dataheader);
$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $x = 1;
    foreach ($data as $value) {

        $m = $value->BuildingNo;  
            if ($m!=$j) {
                $pdf->AddPage();
                $x=1;
            }     
        $newdate = date("d-m-Y H:i:s", strtotime($value->CreateDate));
        $pdf->WriteHTML("<table style='border: 1px solid #000000;border-collapse: collapse;font-size:12px;' cellpadding='12' width='100%'>
                <tr style='border: 1px solid #000000;'>
                    <td width='7%'  style='border: 1px solid #000000;' align='center' >".$x."</td>
                    <td width='7%' style='border: 1px solid #000000;' align='center' >".$value->BuildingNo."</td>
                    <td width='10%' style='border: 1px solid #000000;' align='center' >".$value->GT_Code."</td>
                    <td width='13%' style='border: 1px solid #000000;' align='center' >".$value->Barcode."</td>
                    <td width='22%' style='border: 1px solid #000000;' align='center' >
                      <img width='140' height='20' src='data:image/png;base64,". base64_encode($generator->getBarcode($value->Barcode, $generator::TYPE_CODE_128)) . "'>
                    </td>
                    <td width='19%' style='border: 1px solid #000000;' align='center' >".$newdate."</td>
                    <td width='15%' style='border: 1px solid #000000;' align='center' >".$value->DisposalDesc."</td>
                    <td width='7%'  style='border: 1px solid #000000;' align='center' >".$value->Description."</td>
                </tr></table>
        ");

        $j = $value->BuildingNo;  

    $x++;
    }


$pdf->Output();

