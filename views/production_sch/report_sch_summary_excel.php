<?php ob_start();
error_reporting(0);
header("Content-type: application/vnd.ms-excel");
// header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductionSch_SummaryReport_" . Date("Ymd_His") . ".xls");
?>
<!DOCTYPE html>
<html>
<style type="text/css">
	table {
		border-collapse: collapse;
		width: 100%;
		font-size: 9px;
	}

	td,
	tr,
	th {
		border: 1px solid #000000;
		text-align: center;
		padding: 4px;
		font-family: "Angsana New";
	}

	.table {
		border-collapse: collapse;
		width: 40%;
		font-size: 10px;
	}

	.td,
	.tr,
	.th {
		border: 0px solid #000000;
		text-align: left;
		padding: 4px;
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
			font-size: 10px;
		}
	</style>
</head>

<body>

	<table border="1">
		<thead>
			<tr>
				<!-- <td colspan="2">

				</td> -->
				<td colspan="2">
					<a class="navbar-brand"><img src="./assets/images/DSL.png" /></a>

				</td>
				<td colspan="11">
					<h3>
						ใบสรุปการผลิตประจำวัน
					</h3>
				</td>
				<td colspan="5">
					ประจำวันที่ <?php echo $date; ?>
				</td>
			</tr>

			<tr>
				<th rowspan="2" width="4%">No</th>
				<th rowspan="2" width="6%">ItemID</th>
				<th rowspan="2" width="17%">SIZE/CODE/BRAND</th>
				<th colspan="4" width="20%"><?php echo $shift1; ?></th>
				<th rowspan="2" width="12%">Remark</th>
				<th colspan="4" width="20%"><?php echo $shift2; ?></th>
				<th rowspan="2" width="12%">Remark</th>
				<th colspan="5" width="24%">TOTAL</th>
			</tr>
			<tr>
				<th width="5%">Target</th>
				<th width="5%">Actual</th>
				<th width="5%">DX</th>
				<th width="5%">Weight</th>
				<th width="5%">Target</th>
				<th width="5%">Actual</th>
				<th width="5%">DX</th>
				<th width="5%">Weight</th>
				<th width="6%">Target(PCS)</th>
				<th width="6%">Actual(PCS)</th>
				<th width="6%">DX(PCS)</th>
				<th width="6%">Target(KG)</th>
				<th width="6%">Actual(KG)</th>
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

			if ($v['Target1'][0] > 0) {
				$target1 = $v['Target1'][0];
				echo "<td>" . $target1 . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['Actual1'][0] > 0) {
				$actual1 = $v['Actual1'][0];
				echo "<td>" . $actual1 . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['Scrap1'][0] > 0) {
				$scrap1 = $v['Scrap1'][0];
				echo "<td>" . $scrap1 . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['Actual1'][0] > 0) {
				$weight1 = $v['Weight1'][0] * $v['Actual1'][0];
				echo "<td>" . number_format(number_format($weight1, 3, '.', ''), 3) . "</td>";
			} else {
				echo "<td></td>";
			}

			echo "<td align='left'>" . $v['Remark1'][0] . "</td>";
			// echo "<td>".$v['Target2'][0]."</td>";
			// echo "<td>".$v['Actual2'][0]."</td>";

			if ($v['Target2'][0] > 0) {
				$target2 = $v['Target2'][0];
				echo "<td>" . $target2 . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['Actual2'][0] > 0) {
				$actual2 = $v['Actual2'][0];
				echo "<td>" . $actual2 . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['Scrap2'][0] > 0) {
				$scrap2 = $v['Scrap2'][0];
				echo "<td>" . $scrap2 . "</td>";
			} else {
				echo "<td></td>";
			}

			if ($v['Actual2'][0] > 0) {
				$weight2 = $v['Weight2'][0] * $v['Actual2'][0];
				echo "<td>" . number_format(number_format($weight2, 3, '.', ''), 3) . "</td>";
			} else {
				echo "<td></td>";
			};

			echo "<td align='left'>" . $v['Remark2'][0] . "</td>";

			$sumtarget = $v['Target1'][0] + $v['Target2'][0];
			echo "<td>";
			echo (int) $sumtarget === 0 ? '' : $sumtarget;
			echo "</td>";

			$sumactual = $v['Actual1'][0] + $v['Actual2'][0];
			echo "<td>";
			echo (int) $sumactual === 0 ? '' : $sumactual;
			echo "</td>";

			$sumscrap = $v['Scrap1'][0] + $v['Scrap2'][0];
			echo "<td>";
			echo (int) $sumscrap === 0 ? '' : $sumscrap;
			echo "</td>";

			$weight_target_ = $target1 + $target2;
			$weight_target = $weight_target_ * $v['WeightDefault'];
			echo "<td>";
			echo (int) $weight_target === 0 ? '' : number_format(number_format($weight_target, 3, '.', ''), 3);
			echo "</td>";

			$weight_actual = $weight1 + $weight2;
			echo "<td>";
			echo (int) $weight_actual === 0 ? '' : number_format(number_format($weight_actual, 3, '.', ''), 3);
			echo "</td>";

			echo "</tr>";

			$sumweight_target += $weight_target;
			$sumweight_actual += $weight_actual;

			$weight1 = 0;
			$weight2 = 0;
			$weight_actual = 0;
			$target1 = 0;
			$target2 = 0;
			$weight_target = 0;
		}

		?>

		<tr>
			<td colspan="3">
				<b>TOTAL</b>
			</td>
			<td>
				<?php
				$sumtarget_a = 0;
				foreach ($data as $v) {
					$sumtarget_a += $v['Target1'][0];
				}
				// echo $sumtarget_a;
				echo (int) $sumtarget_a === 0 ? '' : number_format($sumtarget_a);
				?>
			</td>
			<td>
				<?php
				$sumactual_a = 0;
				foreach ($data as $v) {
					$sumactual_a += $v['Actual1'][0];
				}
				// echo $sumactual_a;
				echo (int) $sumactual_a === 0 ? '' : number_format($sumactual_a);
				?>
			</td>
			<td>
				<?php
				$sumscrap_a = 0;
				foreach ($data as $v) {
					$sumscrap_a += $v['Scrap1'][0];
				}
				// echo $sumscrap_a;
				echo (int) $sumscrap_a === 0 ? '' : number_format($sumscrap_a);
				?>
			</td>
			<td>
				<?php
				$sumweight_a = 0;
				foreach ($data as $v) {
					if ($v['Actual1'] > 0) {
						$sumweight_a += $v['Actual1'][0] * $v['Weight1'][0];
					}
				}
				// echo $sumweight_a;
				// echo (int)$sumweight_a === 0 ? '': number_format((float)$sumweight_a, 3, ',', '');
				echo (int) $sumweight_a === 0 ? '' : number_format(number_format($sumweight_a, 2, '.', ''), 2);
				?>
			</td>
			<td>
				<b>PCS</b>
			</td>
			<td>
				<?php
				$sumtarget_b = 0;
				foreach ($data as $v) {
					$sumtarget_b += $v['Target2'][0];
				}
				// echo $sumtarget_b;
				echo (int) $sumtarget_b === 0 ? '' : number_format($sumtarget_b);
				?>
			</td>
			<td>
				<?php
				$sumactual_b = 0;
				foreach ($data as $v) {
					$sumactual_b += $v['Actual2'][0];
				}
				// echo $sumactual_b;
				echo (int) $sumactual_b === 0 ? '' : number_format($sumactual_b);
				?>
			</td>
			<td>
				<?php
				$sumscrap_b = 0;
				foreach ($data as $v) {
					$sumscrap_b += $v['Scrap2'][0];
				}
				// echo $sumscrap_b;
				echo (int) $sumscrap_b === 0 ? '' : number_format($sumscrap_b);
				?>
			</td>
			<td>
				<?php
				$sumweight_b = 0;
				foreach ($data as $v) {
					if ($v['Actual2'] > 0) {
						$sumweight_b += $v['Actual2'][0] * $v['Weight2'][0];
					}
				}
				// echo $sumweight_b;
				// echo (int)$sumweight_b === 0 ? '': number_format((float)$sumweight_b, 2, '.', '');
				echo (int) $sumweight_b === 0 ? '' : number_format(number_format($sumweight_b, 2, '.', ''), 2);
				?>
			</td>
			<td>
				<b>PCS</b>
			</td>
			<td>
				<?php
				$sumtarget_t = 0;
				foreach ($data as $v) {
					$sumtarget_t += $v['Target1'][0] + $v['Target2'][0];
				}
				// echo $sumtarget_t;
				echo (int) $sumtarget_t === 0 ? '' : number_format($sumtarget_t);
				?>
			</td>
			<td>
				<?php
				$sumactual_t = 0;
				foreach ($data as $v) {
					$sumactual_t += $v['Actual1'][0] + $v['Actual2'][0];
				}
				// echo $sumactual_t;
				echo (int) $sumactual_t === 0 ? '' : number_format($sumactual_t);
				?>
			</td>
			<td>
				<?php
				$sumscrap_t = 0;
				foreach ($data as $v) {
					$sumscrap_t += $v['Scrap1'][0] + $v['Scrap2'][0];
				}
				// echo $sumscrap_t;
				echo (int) $sumscrap_t === 0 ? '' : number_format($sumscrap_t);
				?>
			</td>
			<td>
				<?php
				// echo number_format((float)$sumweight_target, 2, '.', '');
				echo (int) $sumweight_target === 0 ? '' : number_format(number_format($sumweight_target, 2, '.', ''), 2);
				?>
			</td>
			<td>
				<?php
				// echo number_format((float)$sumweight_actual, 2, '.', '');
				echo (int) $sumweight_actual === 0 ? '' : number_format(number_format($sumweight_actual, 2, '.', ''), 2);
				?>
			</td>
		</tr>

		<tr>
			<td class="td" colspan="8" align="center">
				<br>
				ลงชื่อ ............................... (หัวหน้าแผนก กะ C)
				<br><br>
				ลงชื่อ ............................... (หัวหน้าแผนก กะ D)
			</td>
			<td class="td" colspan="3">

			</td>
			<td class="td" colspan="7" align="center">
				<br>
				ลงชื่อ ............................... (หัวหน้าส่วน)
				<br><br>
				ลงชื่อ ............................... (ผจก.ฝ่ายผลิต2)
			</td>
		</tr>
		<tr>
			<!-- <td colspan="2" align="left" style="border: 0px solid #000000; font-size: 8px; width: 50%;"> -->
			<td class="td" colspan="8" align="left">
				Ref.WI-PP-3.10
			</td>
			<!-- <td colspan="14"> -->
			<td class="td" colspan="3">

			</td>
			<!-- <td colspan="2" align="right" style="border: 0px solid #000000; font-size: 8px; width: 50%;"> -->
			<td class="td" colspan="7" align="right">
				FM.PP.3.10.5,Issued#5
			</td>
		</tr>
	</table>
</body>

</html>