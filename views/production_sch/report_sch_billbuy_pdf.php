<?php ob_start();
error_reporting(0);
?>
<!DOCTYPE html>
<html>
<style type="text/css">
	table {
		border-collapse: collapse;
		width: 100%;
		font-size: 11px;
	}

	td,
	tr,
	th {
		border: 1px solid #000000;
		text-align: center;
		padding: 5px;
		font-family: "Angsana New";
	}

	.table {
		border-collapse: collapse;
		width: 40%;
		font-size: 12px;
	}

	.td,
	.tr,
	.th {
		border: 0px solid #000000;
		text-align: left;
		padding: 5px;
		font-family: "Angsana New";
	}
</style>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Production Report</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body {
			font-size: 12px;
		}
	</style>
</head>

<body>

	<table>
		<thead>
			<tr>
				<td colspan="2">
					<?php if ($_SESSION['user_company'] == 'DSI') { ?>
						<a class="navbar-brand"><img src="./assets/images/DSI.png" style="padding-left:10px;height:45px; width:auto;" /></a>
					<?php } ?>
					<?php if ($_SESSION['user_company'] == 'SVO') { ?>
						<a class="navbar-brand"><img src="./assets/images/SVO.png" style="padding-left:10px;height:40px; width:120px;" /></a>
					<?php } ?>
					<?php if ($_SESSION['user_company'] == 'DRB') { ?>
						<a class="navbar-brand"><img src="./assets/images/DRB.png" style="padding-left:10px;height:40px; width:auto;" /></a>
					<?php } ?>
					<?php if ($_SESSION['user_company'] == 'DSL') { ?>
						<a class="navbar-brand"><img src="./assets/images/DSL.png" style="padding-left:10px;height:35px; width:auto;" /></a>
					<?php } ?>
					<?php if ($_SESSION['user_company'] == 'STR') { ?>
						<a class="navbar-brand"><img src="./assets/images/STR.png" style="padding-left:10px;height:40px; width:auto;" /></a>
					<?php } ?>

				</td>
				<td colspan="5">
					<h3>
						รายงานอบยาง หน้ายาง
					</h3>
				</td>
				<td colspan="5">
					ประจำวันที่ <?php echo $date; ?>
				</td>
			</tr>

			<tr>
				<th rowspan="2" width="4%">No</th>
				<th rowspan="2" width="6%">ItemID</th>
				<th rowspan="2" width="25%">SIZE/CODE/BRAND</th>
				<th colspan="3" width="20%"><?php echo $shift1; ?></th>
				<th colspan="3" width="20%"><?php echo $shift2; ?></th>
				<th colspan="3" width="24%">TOTAL</th>
			</tr>
			<tr>
				<th width="5%">เบิกใช้</th>
				<th width="5%">เบิกจ่าย</th>
				<th width="5%">หน้าเตา</th>
				<th width="6%">เบิกใช้</th>
				<th width="5%">เบิกจ่าย</th>
				<th width="5%">หน้าเตา</th>
				<th width="5%">เบิกใช้</th>
				<th width="6%">เบิกจ่าย</th>
				<th width="6%">หน้าเตา</th>

			</tr>
		</thead>

		<?php
		foreach ($data as $k => $v) {
			echo "<tr>";
			echo "<td>" . ($k + 1) . "</td>";
			echo "<td>" . $v['ItemID'] . "</td>";
			echo "<td align='left'>" . $v['ItemName'] . "</td>";
			// echo "<td>".$v['Target1'][0]."</td>";
			// echo "<td>".$v['Actual1'][0]."</td>";

			if ($v['BillUse1'][0] > 0) {
				$BillUse1 = $v['BillUse1'][0];
				echo "<td>" . $BillUse1 . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['BillGive1'][0] > 0) {
				$BillGive1 = $v['BillGive1'][0];
				echo "<td>" . $BillGive1 . "</td>";
			} else {
				echo "<td></td>";
			}
			if ($v['faceBoiler1'][0] > 0) {
				$faceBoiler1 = $v['faceBoiler1'][0];
				echo "<td>" . $faceBoiler1 . "</td>";
			} else {
				echo "<td></td>";
			}
			if ($v['BillUse2'][0] > 0) {
				$BillUse2 = $v['BillUse2'][0];
				echo "<td>" . $BillUse2 . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['BillGive2'][0] > 0) {
				$BillGive2 = $v['BillGive2'][0];
				echo "<td>" . $BillGive2 . "</td>";
			} else {
				echo "<td></td>";
			}
			if ($v['faceBoiler2'][0] > 0) {
				$faceBoiler2 = $v['faceBoiler2'][0];
				echo "<td>" . $faceBoiler2 . "</td>";
			} else {
				echo "<td></td>";
			}
			if ($v['BillUse1'][0] > 0 || $v['BillUse2'][0] > 0) {
				$BillUsetotal = $v['BillUse1'][0] + $v['BillUse2'][0];
				echo "<td>" . $BillUsetotal . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['BillGive1'][0] > 0 || $v['BillGive2'][0] > 0) {
				$BillGivetotal = $v['BillGive1'][0] + $v['BillGive2'][0];
				echo "<td>" . $BillGivetotal . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['faceBoiler1'][0] > 0 || $v['faceBoiler2'][0] > 0) {
				$faceBoilertotal = $v['faceBoiler1'][0] + $v['faceBoiler2'][0];
				echo "<td>" . $faceBoilertotal . "</td>";
			} else {
				echo "<td></td>";
			}
		}

		?>
		<tr>
			<td colspan="3">
				<b>TOTAL</b>
			</td>
			<td>
				<?php
				$sumBillUse1 = 0;
				foreach ($data as $v) {
					$sumBillUse1 += $v['BillUse1'][0];
				}
				// echo $sumtarget_a;
				echo (int) $sumBillUse1 === 0 ? '' : number_format($sumBillUse1);
				?>
			</td>

			<td>
				<?php
				$sumBillGive1 = 0;
				foreach ($data as $v) {
					$sumBillGive1 += $v['BillGive1'][0];
				}
				// echo $sumtarget_a;
				echo (int) $sumBillGive1 === 0 ? '' : number_format($sumBillGive1);
				?>
			</td>
			<td>
				<?php
				$sumfaceBoiler1 = 0;
				foreach ($data as $v) {
					$sumfaceBoiler1 += $v['faceBoiler1'][0];
				}
				// echo $sumtarget_a;
				echo (int) $sumfaceBoiler1 === 0 ? '' : number_format($sumfaceBoiler1);
				?>
			</td>
			<td>
				<?php
				$sumBillUse2 = 0;
				foreach ($data as $v) {
					$sumBillUse2 += $v['BillUse2'][0];
				}
				// echo $sumtarget_a;
				echo (int) $sumBillUse2 === 0 ? '' : number_format($sumBillUse2);
				?>
			</td>

			<td>
				<?php
				$sumBillGive2 = 0;
				foreach ($data as $v) {
					$sumBillGive2 += $v['BillGive2'][0];
				}
				// echo $sumtarget_a;
				echo (int) $sumBillGive2 === 0 ? '' : number_format($sumBillGive2);
				?>
			</td>
			<td>
				<?php
				$sumfaceBoiler2 = 0;
				foreach ($data as $v) {
					$sumfaceBoiler2 += $v['faceBoiler2'][0];
				}
				// echo $sumtarget_a;
				echo (int) $sumfaceBoiler2 === 0 ? '' : number_format($sumfaceBoiler2);
				?>
			</td>
			<td>
				<?php
				$sumBillUsetotal = $sumBillUse1 + $sumBillUse2;

				echo (int) $sumBillUsetotal === 0 ? '' : number_format($sumBillUsetotal);
				?>
			</td>
			<td>
				<?php
				$sumBillGivetotal = $sumBillGive1 + $sumBillGive2;

				echo (int) $sumBillGivetotal === 0 ? '' : number_format($sumBillGivetotal);
				?>
			</td>
			<td>
				<?php
				$sumfaceBoilertotal = $sumfaceBoiler1 + $sumfaceBoiler2;

				echo (int) $sumfaceBoilertotal === 0 ? '' : number_format($sumfaceBoilertotal);
				?>
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
			        FM.PP.3.10.5,Issued#3
			    </td>
			</tr>
		</table>';
	return $txtpage;
}

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4-L', 0, '', 3, 3, 3, 13);
$pdf->SetDisplayMode('fullpage');

$datafooter = txtPageFooter();
$pdf->SetHTMLFooter($datafooter);

$pdf->WriteHTML($html);

$pdf->Output();
