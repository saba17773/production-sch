<?php ob_start(); ?>
<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductionSch_Report".Date("Ymd_His").".xls");
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

	<table border="1">
		<thead>
		<tr>
			<td colspan="2">


			</td>
			<td colspan="7">
				<h2>
					ใบรายงานการผลิต
				</h2>
			</td>
		</tr>
		<tr>
			<td colspan="9" align="center">
				<?php echo $date; ?>
				กะ : <?php echo $shift; ?>
			</td>
		</tr>
		<tr style="background: #FFDEAD;">
			<th rowspan="2" width="5%">เตา</th>
			<th rowspan="2" width="15%">ชื่อพนักงาน</th>
			<th rowspan="2" width="10%">Item Number</th>
			<th rowspan="2" width="27%">ขนาดพิมพ์</th>
			<th rowspan="2" width="7%">เวลาอบ</th>
			<th colspan="2" width="14%">จำนวนการอบยาง</th>
			<!-- <th rowspan="2" width="7%">น้ำหนัก</th>  -->
			<th rowspan="2" colspan="2">หมายเหตุ</th>
		</tr>
		<tr>
			<th style="background: #FFDEAD;" width="7%">เป้า</th>
			<!-- <th style="background: #FFDEAD;" width="7%">รอบ1</th>
			<th style="background: #FFDEAD;" width="7%">รอบ2</th>  -->
			<th style="background: #FFDEAD;" width="7%">อบได้</th>
		</tr>
		</thead>

			<?php
				$boiler = "";
				foreach ($data as $key => $value) {
					// echo $value['Boiler'].$value['ItemID'];
					// echo "<br>";

					echo "<tr>";
					if ($boiler != $value["Boiler"]) {
						echo "<td rowspan=".$value['rowspan'].">".$value["Boiler"]."\n".$value['BoilerName']."</td>";
						echo "<td rowspan=".$value['rowspan']." align='left'>".$value["Employee"]."</td>";
					}
					if ($value["ItemID"] == "") {
						echo "<td>&nbsp;</td>";
					}else{
						echo "<td>".$value["ItemID"]."</td>";
					}

					echo "<td align='left'>".$value["ItemName"]."</td>";

					echo "<td align='center'>".$value["Time"]."</td>";

					echo "<td align='center' >".$value["Target"]."</td>";

					// echo "<td>".$value["Actual1"]."</td>";
					// echo "<td>".$value["Actual2"]."</td>";
					echo "<td align='center'>".$value["Actual"]."</td>";

					// echo "<td>".$value["Weight"]."</td>";

					echo "<td align='left' colspan='2'>".$value["Remark"]."</td>";

					echo "</tr>";
					$boiler = $value["Boiler"];
				}
			?>
		<tr>
			<td colspan="5" align="center">
				Total
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
			<!-- <td>
				<?php
				$sumactual1 = 0;
    				foreach ($data as $actual) {
    					$sumactual1 += $actual["Actual1"];
    				}
    				echo (int)$sumactual1 === 0 ? '': $sumactual1;
				?>
			</td>
			<td>
				<?php
				$sumactual2 = 0;
    				foreach ($data as $actual) {
    					$sumactual2 += $actual["Actual2"];
    				}
    				echo (int)$sumactual2 === 0 ? '': $sumactual2;
				?>
			</td> -->
			<td>
				<?php
				$sumactual = 0;
    				foreach ($data as $actual) {
    					$sumactual += $actual["Actual"];
    				}
    				echo (int)$sumactual === 0 ? '': $sumactual;
				?>
			</td>
			<td colspan="2">

			</td>
		</tr>

		<tr>
			<td align="center" text-rotate="90" align="center">
				จำนวนพนักงาน
			</td>
			<td colspan="1">
				<table class="table">
					<tr class="tr">
						<td class="td" width="50%">
							หัวหน้าแผนก
						</td>
						<td class="td" width="50%">
							...............<?php echo $masterreportsch[0]->Senior; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							หัวหน้าหน่วย
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->SectionHead; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							Auditor/Tranner
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->Auditor; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							พนง.อบยาง
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpCuring; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							พนง.เปลี่ยนแบลดเดอร์
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpBladder; ?>................. คน
						</td>
					</tr>
				</table>
			</td>
			<td colspan="7">
				<table class="table">
					<tr class="tr">
						<td class="td" width="50%">
							พนง.เก็บยางหลังเตา
						</td>
						<td class="td" width="50%">
							...............<?php echo $masterreportsch[0]->EmpCuringBack; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							พนง.ซ่อมยาง
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpMantain; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							พนง.ตัดหนวด/ปาดขอบ
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpCutting; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							พนง.จัดเก็บ/เข้าคลัง
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpWarehoure; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							รวมพนักงานทั้งหมด
						</td>
						<td class="td">
							...............
							<?php echo
							$masterreportsch[0]->Senior+
						  	$masterreportsch[0]->SectionHead+
						  	$masterreportsch[0]->EmpBladder+
						  	$masterreportsch[0]->EmpCuringBack+
						  	$masterreportsch[0]->Auditor+
						  	$masterreportsch[0]->EmpMantain+
						  	$masterreportsch[0]->EmpCuring+
						  	$masterreportsch[0]->EmpCutting+
						  	$masterreportsch[0]->EmpWarehoure
							// echo $masterreportsch[0]->Senior;
							?>
							............ คน
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td class="td">

			</td>
			<td colspan="1" class="td">
				<table class="table">
					<tr class="tr">
						<td class="td" width="45%">
							จำนวนพนักงานที่มาทำงาน
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpWorking; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							จำนวนพนักงานลาพักร้อน
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpSummer; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							จำนวนพนักงานลาป่วย
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpSeak; ?>................. คน
						</td>
					</tr>
				</table>
			</td>
			<td colspan="7">
				<table class="table">
					<tr class="tr">
						<td class="td" width="48%">
							จำนวนพนักงานไม่แจ้ง
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpNoInfo; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							จำนวนพนักงานลากิจ
						</td>
						<td class="td">
							...............<?php echo $masterreportsch[0]->EmpLeave; ?>................. คน
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							จำนวนพนักงานลาทั้งหมด
						</td>
						<td class="td">
							.............
							<?php echo
						  	$masterreportsch[0]->EmpSummer+
						  	$masterreportsch[0]->EmpSeak+
						  	$masterreportsch[0]->EmpLeave+
						  	$masterreportsch[0]->EmpNoInfo
							// echo $masterreportsch[0]->Senior;
							?>
							............... คน
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td align="center" text-rotate="90" align="center">
				การผลิต
			</td>
			<td colspan="1">
				<table class="table">
					<tr class="tr">
						<td class="td" width="45%">
							จำนวนเตาอบยางที่เปิดอบ
						</td>
						<td class="td">
							...............<?php echo $countBoiler; ?>................. เตา
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							จำนวนพิมพ์ที่อบ
						</td>
						<td class="td">
							...............<?php echo $countMold; ?>................. พิมพ์
						</td>
					</tr>
				</table>
			</td>
			<td colspan="7">
				<table class="table">
					<tr class="tr">
						<td class="td" width="48%">
							เป้าหมายการผลิต
						</td>
						<td class="td">
							...............<?php echo $sumtarget; ?>................. เส้น
						</td>
					</tr>
					<tr class="tr">
						<td class="td">
							ผลิตได้
						</td>
						<td class="td">
							......<?php echo $sumactual; ?>.....เส้น......<?php $sumall = ($sumactual/$sumtarget)*100;
							echo number_format((float)$sumall, 2, '.', '');
							?>..... %
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="9">
				<table class="table">
					<tr class="tr">
						<td class="td" width="5%">

						</td>
						<td class="td" colspan="3">
							หมายเหตุ
							<?php
								if (count($masterreportsch[0]->Remark)>0) {
									echo $masterreportsch[0]->Remark;
								}else{
									echo "............................................................................................................................................................................................................................................................................";
								}

							?>
						</td>
					</tr>
					<tr class="tr">
						<td class="td">

						</td>
						<td class="td" align="center">ลงชื่อ ..............................................</td>
						<td class="td" align="center">ลงชื่อ ..............................................</td>
						<td class="td" align="center">ลงชื่อ ..............................................</td>
					</tr>
					<tr class="tr">
						<td class="td">

						</td>
						<td class="td" align="center">(หัวหน้าแผนก)</td>
						<td class="td" align="center">(หัวหน้าส่วน)</td>
						<td class="td" align="center">(ผู้จัดการฝ่ายผลิต2)</td>
					</tr>
					<tr class="tr">
						<td class="td" colspan="4"> 
							Ref .WI-PP-3.10
						</td>
						<td class="td" colspan="6" align="right"> 
							FM-PP-3.10.4.Issued#4
						</td>
					</tr>
				</table>
			</td>
		</tr>

	</table>
</body>
</html>
