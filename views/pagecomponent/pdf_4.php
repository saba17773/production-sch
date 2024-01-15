<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>BEAD ASSEMBLY Report</title>
	<!-- <link rel="stylesheet" href="<?php echo root; ?>/assets/css/theme.min.css" /> -->
<style type="text/css">
	table {
    border-collapse: collapse;
    width: 100%;
    font-size: 11px;
    text-align: center;
}

td, tr {
    border: 1px solid #000000;
    text-align: left;
    padding: 6px;
    text-align: center;
}
.f12{
	font-size:12px;
    font-family:"Angsana New";
}

</style>
</head>
<body>

<div class="container">

<table>
    <thead>
    <tr>
        <td  colspan="3">
            <a class="navbar-brand"><img  src="./assets/images/STR.jpg" 
            style="height:30px; width:auto;" /></a> 
        </td>
        <td align="center" colspan="15" class="f12">
            <h2><b>BEAD ASSEMBLY Report</b></h2>
        </td>
    </tr>
    <tr>
        <td colspan="18" class="f12" align="left">
            วันที่ : <?php echo $date; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
             
            <?php echo "เวลา  : ".$shiftA." น. ถึง ".$shiftB." น."; ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            เครื่อง : -
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            
            <?php if ($mode=='a') { ?>
                กะ   : เช้า 
            <?php }else{ ?>
                กะ   : กลางคืน
            <?php } ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            แผนก :  BEAD STIFFENER
            
        </td>
    </tr>
    <tr>
        <td rowspan="2">No.</td>
        <td rowspan="2" width="6%">Code</td>
        <td colspan="2">Bead</td>
        <td rowspan="2">Weight <br> (Kg:Pcs) </td>
        <td colspan="2">Stiffener</td>
        <td rowspan="2" text-rotate="90">SCH(Pcs.)</td>
        <td colspan="3">ACT(Pcs.)</td>
        <td rowspan="2" text-rotate="90">Total Weight</td>
        <td colspan="3">เวลาที่ใช้</td>
        <td colspan="2">Waste(Kg.)</td>
        <td rowspan="2">Problem</td>
    </tr>
    <tr>
        <td>Code</td>
        <td>Date</td>
        <td>Code</td>
        <td>Date</td>
        <td>No.รถ</td>
        <td>No.Tag</td>
        <td>จำนวน</td>
        <td>Start</td>
        <td>End</td>
        <td>Total</td>
        <td>Bead</td>
        <td>Stiffener</td>
    </tr>
    </thead>
        <?php $i=1;
        foreach ($data as $value) {
            echo "<tr>";
            echo "<td>".$i."</td>";
            echo "<td>".$value->PastCodeID."</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>".$value->SCH."</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>".$value->GoodQty."</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>".$value->ErrorQty."</td>";
            echo "<td>".$value->GroupName."</td>";
            echo "</tr>";
        $i++; } 
        ?>
    <tr>
        <td colspan="7">
           <b>Total</b> 
        </td>
        <td>
            <?php 
                $sum_sch = 0;
                foreach ($data as $value) {
                    $sum_sch += $value->SCH;
                }
                if ($sum_sch==0) {
                    echo "";
                }else{
                    echo $sum_sch;
                }
            ?> 
        </td>
        <td></td>
        <td></td>
        <td>
            <?php 
                $sum_good = 0;
                foreach ($data as $value) {
                    $sum_good += $value->GoodQty;
                }
                if ($sum_good==0) {
                    echo "";
                }else{
                    echo $sum_good;
                }
            ?>    
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            <?php 
                $sum_error = 0;
                foreach ($data as $value) {
                    $sum_error += $value->ErrorQty;
                }
                if ($sum_error==0) {
                    echo "";
                }else{
                    echo $sum_error;
                }
            ?>    
        </td>
        <td></td>
    </tr>
</table>

</div>

</body>
</html>
<?php

$stylesheet = " table{
                      width: 100%;
                    }
                    tr{
                      border: 0px;
                    }
                    td{
                      border: 0px;
                      text-align: center; 
                      padding: 5px;
                    }";
$footer = "<table>
            <tr>
                <td align='left'>
                    ผู้รายงาน_________________________________________
                </td> 
                <td align='right'>
                    หัวหน้ากลุ่ม/แผนก_________________________________________
                </td> 
            </tr>
            <tr>
                <td align='left'>
                    Ref.WI-PP-2.13
                </td>
                <td align='right'>
                    FM-PP-2.13.4 Issued #2   
                </td>
            </tr>
        </table>";

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-L', 0, '', 5, 5, 5, 5);  
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->WriteHTML($stylesheet, 1);
$pdf->SetHTMLFooter($footer);
$pdf->Output(); 
?>