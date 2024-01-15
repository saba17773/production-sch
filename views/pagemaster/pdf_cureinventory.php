<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Cured Inventory Report</title>
<style type="text/css">
	table {
    border-collapse: collapse;
    width: 100%;
    font-size: 10px;
    text-align: center;
}

td, tr {
    border: 1px solid #000000;
    text-align: left;
    padding: 6px;
    text-align: center;
}
.f12{
	font-size:14px;
    font-family:"Angsana New";
}

</style>
</head>
<body>

<div class="container">

<table>
    <thead>
    <tr>
        <td colspan="2">
            <a class="navbar-brand"><img  src="/assets/images/STR.jpg" 
            style="padding-left:10px;height:30px; width:auto;" /></a> 
        </td>
        <td align="center" colspan="4" class="f12">
            <h2><b>Cured Inventory Report</b></h2>
        </td>
    </tr>
    <tr>
        <td colspan="6" class="f12">
            <b>Date : <?php echo $date; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <b>Time : <?php echo $time; ?></b>
        </td>
    </tr>
    <tr>
        <td>
            No.
        </td>
         <td>
            Date/Time
        </td>
         <td>
            CuredCode
        </td>
         <td>
            Press
        </td>
         <td>
            Serail No.
        </td>
         <td>
            Barcode No.
        </td>
    </tr>
    </thead>
    <?php foreach ($datajson as $key => $value) {    
    ?>
    <tr>
        <td>
            <?php echo $value->Row; ?>
        </td>
         <td>
            <?php //echo substr($value->CuringDate, 0,16);
            $_date = new Datetime($value->CuringDate);
            echo $_date->format("d-m-Y H:i:s");
             ?>
        </td>
         <td>
            <?php echo $value->CuringCode; ?>
        </td>
         <td>
            <?php echo $value->PressNo.$value->PressSide; ?>
        </td>
         <td>
            <?php echo $value->TemplateSerialNo; ?>
        </td>
         <td>
            <?php echo $value->Barcode; ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td align="center" colspan="5">
            Total
        </td>
        <td>
            <?php foreach ($datajson as $key => $value) {
                // var_dump();
                    $row = $value->Row;
            } echo $row." เส้น";
            ?>
        </td>
    </tr>
</table>

</div>

</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4', 0, '', 10, 10, 10, 10);  
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output(); 
?>