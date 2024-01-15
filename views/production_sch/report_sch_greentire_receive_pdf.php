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
			<td colspan="2" rowspan="2" >
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
			<td colspan="11" rowspan="2">
				<h2>
					รายงาน  รับเข้า-เบิก-จ่าย  ยางกรีนไทร์
				</h2>
			</td>
		</tr>
		<tr>
			<td colspan="4" align="left">
				<?php echo $date; ?>
				<font color="red"><?php echo date('d/m/Y H:i'); ?></font>
				<br><br>
				กะ <?php echo $shift; ?>
			</td>
		</tr>

		<tr>
			<th rowspan="2" width="5%">No</th>
			<th rowspan="2" width="7%">ITEM</th>
			<th rowspan="2" width="15%">SIZE</th>
			<th rowspan="2" width="4%">PR</th>
			<th colspan="3" width="13%">CODE</th>
			<th rowspan="2" width="10%">COLOR</th>
			<th colspan="3" width="15%">ยางกรีนไทร์</th>
			<th colspan="6"><?php echo $shift_head; ?></th>
		</tr>
		<tr>
			<th width="4%">Brand</th>
			<th width="5%">Pattern</th>
			<th width="4%">T/T</th>
			<td width="5%"><font size="2.2">Spare หน้าเตา(เส้น)</font></td>
			<td width="5%"><font size="2.2">Stock ในแผนก(เส้น)</font></td>
			<td width="5%"><font size="2.2">คงเหลือ หน้าเตา+แผนกกรีนไทร์ (เส้น)</font></td>
			<td><font size="2.2">แผนสร้าง กะกลางวัน (เส้น)</font></td>
			<td><font size="2.2">ผลิตได้ กะกลางวัน (เส้น)</font></td>
			<td><font size="2.2">รับเข้า (เส้น)</font></td>
			<td><font size="2.2">อบยาง เบิก(เส้น)</font></td>
			<td><font size="2.2">ยางไม่ได้ Spec(เส้น)</font></td>
			<td><font size="2.2">Stock แผนกกรีนไทร์ (เส้น)</font></td>
		</tr>

		<?php
			foreach ($data as $k => $v) {
				echo "<tr>";
				echo "<td>".($k+1)."</td>";
				echo "<td>".$v['ItemID']."</td>";
				echo "<td class='td'>".$v['ItemName']."</td>";
				echo "<td>".$v['PR']."</td>";
				echo "<td>".$v['Brand']."</td>";
				echo "<td>".$v['Pattern']."</td>";
				echo "<td>".$v['TT']."</td>";
				echo "<td>".$v['Color1'].$v['Color2'].$v['Color3'].$v['Color4'].$v['Color5']."</td>";

				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "</tr>";
			}
		?>

		<tr>
			<th colspan="8">
				Total
			</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>

	</table>

</body>
</html>

<?php

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4-L', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
