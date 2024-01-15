<?php ob_start(); ?>
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
	    width: 100%;
	    font-size: 9px;
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
		<thead>
		<tr>
			<td colspan="2">
				<?php if ($_SESSION['user_company']=='DSI'){ ?>
					<a class="navbar-brand"><img  src="./assets/images/DSI.png" style="padding-left:10px;height:45px; width:auto;" /></a> 
				<?php } ?>
				<?php if ($_SESSION['user_company']=='SVO'){ ?>
					<a class="navbar-brand"><img  src="./assets/images/SVO.png" style="padding-left:10px;height:40px; width:120px;" /></a> 
				<?php } ?>
				<?php if ($_SESSION['user_company']=='DRB'){ ?>
					<a class="navbar-brand"><img  src="./assets/images/DRB.png" style="padding-left:10px;height:40px; width:auto;" /></a> 
				<?php } ?>
				<?php if ($_SESSION['user_company']=='DSL'){ ?>
					<a class="navbar-brand"><img  src="./assets/images/DSL.png" style="padding-left:10px;height:35px; width:auto;" /></a> 
				<?php } ?>
				<?php if ($_SESSION['user_company']=='STR'){ ?>
					<a class="navbar-brand"><img  src="./assets/images/STR.png" style="padding-left:10px;height:40px; width:auto;" /></a> 
				<?php } ?>
				
			</td>
			<td colspan="7">
				<h2>
					ใบเบิกยางกรีนไทร์
				</h2>
			</td>
		</tr>
		<tr>
			<td colspan="5" align="left">
				<?php echo $date; ?>
				กะ : <?php echo $shift; ?>
			</td>
			<td colspan="4" align="right">
				<?php echo $date_pay; ?>
				กะ : <?php echo $shift_pay; ?>
			</td>
		</tr>
		<tr style="background: #FFDEAD;"> 
			<th width="5%">เตา</th> 
			<th width="10%">Item Number</th> 
			<th width="20%">ขนาดยาง (Size)</th> 
			<th width="7%">เวลาอบ (นาที)</th> 
			<th width="10%">เป้าอบยาง (เส้น)</th> 
			<th width="10%">ยอดคงเหลือ (ยางหน้าเตา) เส้น</th>
			<th width="10%">ยอดการเบิก (ยางกรีนไทร์) เส้น</th> 
			<th width="10%">ยอดการจ่าย (ยางกรีนไทร์) เส้น</th>
			<th>หมายเหตุ</th> 
		</tr> 
		</thead>
		
			<?php 
				$boiler = "";
				foreach ($data as $key => $value) {
					echo "<tr>";
					if ($boiler != $value["Boiler"]) {
						echo "<td rowspan=".$value['rowspan'].">".$value["Boiler"]."\n".$value['BoilerName']."</td>";
					}
					
					echo "<td>".$value["ItemID"]."</td>";

					echo "<td class='td'>".$value["ItemName"]."</td>";

					echo "<td>".$value["Time"]."</td>";

					echo "<td style='background: #FFFACD;'>".$value["Target"]."</td>";

					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					// echo "<td align='left'>".$value["Remark"]."</td>";
					echo "<td></td>";
					
					echo "</tr>";
					$boiler = $value["Boiler"];
				}
			?>
		<tr>
			<td colspan="4" align="center">
				รวมยางที่เบิก
			</td>
			<td>
				<?php 	
				$sumtarget = 0;
    				foreach ($data as $target) {
    					$sumtarget += $target["Target"];
    				}
    				echo $sumtarget;
				?>
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="4" align="center">
				คิดเป็น %
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="9">
				<table class="table"> 
					<tr class="tr">
						<td class="td" align="center">...............................</td>
						<td class="td" align="center">...............................</td>
					</tr>
					<tr class="tr">
						<td class="td" align="center">ผู้อนุมัติเบิก ( หน.แผนก )</td>
						<td class="td" align="center">ผู้อนุมัติจ่าย ( หน.แผนก )</td>
					</tr>
					<tr class="tr">
						<td class="td" align="center">วันที่ .........../.........../...........</td>
						<td class="td" align="center">วันที่ .........../.........../...........</td>
					</tr>
				</table>
			</td>
		</tr>

	</table>
</body>
</html>

<?php

function txtPageFooter()
{
    $txtpage =
        '<table style="border-collapse: collapse; width: 100%;">
			<tr style="border: 0px solid #000000; font-size: 8px;">
			    <td align="left" style="border: 0px solid #000000; font-size: 8px; width: 50%;">
			        Ref.WI-PP-3.10
			    </td>
			    <td align="right" style="border: 0px solid #000000; font-size: 8px; width: 50%;">
			        FM.PP.3.10.8,Issued #5
			    </td>
			</tr>
		</table>';
    return $txtpage;
}

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4', 0, '', 3, 3, 3, 10);  
$pdf->SetDisplayMode('fullpage');
$datafooter = txtPageFooter();
$pdf->SetHTMLFooter($datafooter);
$pdf->WriteHTML($html);
$pdf->Output();