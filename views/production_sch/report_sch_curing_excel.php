<?php ob_start(); ?>
<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductionSch_CuringReport".Date("Ymd_His").".xls");
?>
<!DOCTYPE html>
<html>
<style type="text/css">
    table {
	    border-collapse: collapse;
	    width: 100%;
	    font-size:10px;
    }

    td, tr, th {
        border: 1px solid #000000;
        text-align: center;
        padding: 5px;
        font-family:"Angsana New";
    }

    .table {
	    border-collapse: collapse;
	    width: 40%;
	    font-size: 11px;
    }

    .td, .tr, .th {
        border: 0px solid #000000;
        text-align: left;
        padding: 5px;
        font-family:"Angsana New";
    }
</style>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Production Report</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body {
			font-size: 10px;
		}
	</style>
</head>
<body>

	<table>
		<tr>
			<td colspan="7">
				<h2>
					ใบรายงานจำนวนเตาทั้งหมดในการผลิต
				</h2>
			</td>
		</tr>
		<tr>
			<td colspan="7" align="left">
				เดือน : <?php echo monththai($monthnum); ?>
				กะ : <?php echo $shift; ?>
			</td>
		</tr>
		<tr style="background: #FFDEAD;"> 
			<td colspan="7">
				<h3>จำนวนเตาทั้งหมด <?php echo $monthname." - ".$countboiler; ?></h3>
			</td>
		</tr> 
		<tr style="background: #B2DDFF;">
			<td width="5%">No.</td> 
			<td width="15%">Size</td> 
			<td width="15%">Total (Presses)</td> 
			<td width="15%">Total (Molds)</td> 
			<td width="15%">Opening (Presses)</td> 
			<td width="15%" style="color:red;">Stop (Presses)</td> 
			<td>Remark</td> 
		</tr>

		<?php foreach ($data as $key => $value) { ?>
			<tr>
				<td>
					<?php echo $key+1; ?>
				</td>
				<td>
					<?php echo $value["ItemName"]; ?>
				</td>
				<td>
					<?php echo $value["Time"]; ?>
				</td>
				<td>
					<?php echo $value["Target"]; ?>
				</td>
				<td>
					<?php echo $value["Actual"]; ?>
				</td>
				<td>
					<?php echo $value["Scrap"]; ?>
				</td>
				<td>
					<?php echo $value["Remark"]; ?>
				</td>
			</tr>
		<?php } ?>
			
			<tr>
				<td colspan="2">
					<b><u>Grand Total</u></b>
				</td>	
				<td>
					<?php 	
						$sumtotalpresses = 0;
	    				foreach ($data as $totalpresses) {
	    					$sumtotalpresses += $totalpresses["Time"];
	    				}
	    				echo $sumtotalpresses;
					?>
				</td>
				<td>
					<?php 	
						$sumtotaltarget = 0;
	    				foreach ($data as $totaltarget) {
	    					$sumtotaltarget += $totaltarget["Target"];
	    				}
	    				echo $sumtotaltarget;
					?>
				</td>
				<td>
					<?php 	
						$sumtotalactual = 0;
	    				foreach ($data as $totalactual) {
	    					$sumtotalactual += $totalactual["Actual"];
	    				}
	    				echo $sumtotalactual;
					?>
				</td>
				<td>
					<?php 	
						$sumtotalscrap = 0;
	    				foreach ($data as $totalscrap) {
	    					$sumtotalscrap += $totalscrap["Scrap"];
	    				}
	    				echo $sumtotalscrap;
					?>
				</td>
				<td>
					
				</td>
			</tr>

	</table>
	<br>
	<table style="width: 300px;">
		<tr>
			<td width="90px">
				<b>Opening</b>
			</td>
			<td>
				
			</td>
			<td>
				<?php echo $sumtotalactual; ?>
			</td>
			<td width="60px">
				Molds
			</td>
		</tr>
		<tr>
			<td>
				<b>Waiting</b>
			</td>
			<td>
				
			</td>
			<td>
				<?php echo $sumtotalscrap; ?>
			</td>
			<td>
				Molds
			</td>
		</tr>
		<tr>
			<td style="background: #CCCCCC;">
				<b>Total</b>
			</td>
			<td>
				
			</td>
			<td>
				<?php echo $sumtotalactual+$sumtotalscrap; ?>
			</td>
			<td>
				Molds
			</td>
		</tr>	
	</table>
	<br>
	<table class="table"> 
		<tr class="tr">
			<td class="td" width="25%">
				Issue By <br>
				Date
			</td>
			<td class="td">
				................................. <br>
				........./.........../...........
			</td>
		</tr>
		<tr class="tr">
			<td class="td">
				Review By <br>
				Date
			</td>
			<td class="td">
				................................. <br>
				........./.........../...........
			</td>
		</tr>
		<tr class="tr">
			<td class="td">
				Approved By <br>
				Date
			</td>
			<td class="td">
				................................. <br>
				........./.........../...........
			</td>
		</tr>
	</table>
</body>
</html>

<?php

function monththai($param){
	$month_arr=array(
	    "01"=>"มกราคม",
	    "02"=>"กุมภาพันธ์",
	    "03"=>"มีนาคม",
	    "04"=>"เมษายน",
	    "05"=>"พฤษภาคม",
	    "06"=>"มิถุนายน", 
	    "07"=>"กรกฎาคม",
	    "08"=>"สิงหาคม",
	    "09"=>"กันยายน",
	    "10"=>"ตุลาคม",
	    "11"=>"พฤศจิกายน",
	    "12"=>"ธันวาคม"                 
	);	

	return $month_arr[$param];
}

// $html = ob_get_contents();
// ob_end_clean();
// $pdf = new mPDF('th','A4', 0, '', 3, 3, 3, 3);  
// $pdf->SetDisplayMode('fullpage');
// $pdf->WriteHTML($html);
// $pdf->Output();