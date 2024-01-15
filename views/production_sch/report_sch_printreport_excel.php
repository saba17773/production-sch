<?php ob_start();
error_reporting(0);
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductiongreentireMoldReport_".Date("Ymd_His").".xls");
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

  <table border="1">
    <thead>
		<tr>
			<td colspan="2" rowspan="2" >

			</td>
			<td colspan="5" rowspan="2">
				<h2>
					รายงาน จำนวนพิมพ์
				</h2>
			</td>
		</tr>
		<tr>
			<td colspan="23" align="left">
				<?php echo $date; ?>
				<font color="red"><?php echo date('d/m/Y H:i'); ?></font>
				<br><br>
				กะ <?php echo $shift; ?>
			</td>
		</tr>

		<tr>
			<th rowspan="2" width="50px">No</th>
			<th rowspan="2" width="60px">ITEM</th>
			<th rowspan="2" width="420px">ITEM NAME</th>
			<th rowspan="2" width="100px">COLOR</th>
			<th rowspan="2" width="80px">จำนวนพิมพ์ทั้งหมด</th>
			<th rowspan="2" width="80px">จำนวนพิมพ์คงเหลือ</th>
			<th rowspan="2" width="80px">เวลาอบ (นาที)</th>
			<th rowspan="2" width="80px">จำนวนพิมพ์เปิดอบ</th>
      <th rowspan="2" width="80x">Rate (เส้น) กะ/พิมพ์</th>
      <th colspan="3" width="80x">พิมพ์อบร่วม</th>
      <th colspan="2" width="80px">กรีนไทร์</th>
      <th rowspan="2" width="80px">อบยางเบิก (เส้น)</th>
      <th rowspan="2" width="80px">Spare หน้าเตา (เส้น)</th>
      <th colspan="2" width="80px">Stock</th>
      <th colspan="2" width="80px">เหลือกรีนไทร์</th>
      <th rowspan="2" width="80px">กรีนไทร์ ขาดในกะ (เส้น)</th>
      <th rowspan="2" width="80px"><?php echo $plan1 ; ?></th>
      <th rowspan="2" width="80px">ลำดับกรีนไทร์ขาดอบ</th>
      <th rowspan="2" width="80px">ยางขาดสร้างในกะ</th>
      <th colspan="2" width="80px">แยกตามชนิด Type</th>
      <th rowspan="2" width="80px">Balance เส้น</th>
      <th rowspan="2" width="80px"><?php echo $plan2 ; ?></th>
      <th rowspan="2" width="80px">ยางขาด ต่อ กะ</th>
      <th rowspan="2" width="80px">ยางขาด ต่อ วัน</th>
		</tr>
    <tr>
			<th width="50px">เวลาอบ(นาที)</th>
			<th width="50px">จำนวนพิมพ์เปิดอบ</th>
			<th width="50px">Rate (เส้น) กะ/พิมพ์</th>
			<th width="50px"><font size="2.2">เส้น/กะ</font></th>
			<th width="50px"><font size="2.2">เส้น/วัน</font></th>
			<th width="50px"><font size="2.2">แผนกกรีนไทร์ (เส้น)</font></th>
      <th width="50px"><font size="2.2">หน้าเตา+แผนกกรีนไทร์</font></th>
			<th width="50px"><font size="2.2">ชม</font></th>
			<th width="50px"><font size="2.2">%</font></th>
			<td width="50px"><font size="2.2">จำนวนพิมพ์</font></td>
      <td width="50px"><font size="2.2">อบได้/วัน</font></td>

		</tr>
  </thead>

		<?php
			foreach ($data as $k => $v) {
				echo "<tr>";
				echo "<td height ='30px'>".($k+1)."</td>";
				echo "<td>".$v['ItemId']."</td>";
				echo "<td class='td'>".$v['ItemGTName']."</td>";
		    echo "<td>".$v['Color']."</td>";
        echo "<td>".$v['SumPrint']."</td>";
				echo "<td>".$v['TotalPrint']."</td>";
				echo "<td>".$v['Time']."</td>";
        echo "<td>".$v['Countprintcure']."</td>";
        echo "<td>".$v['Rateprint']."</td>";
				echo "<td>".$v['TimeCureFG']."</td>";
				echo "<td>".$v['CountPrintcurFG']."</td>";
				echo "<td>".$v['RatePrintFG']."</td>";
				echo "<td>".$v['GreentireShift']."</td>";
        echo "<td>".$v['GreentireDay']."</td>";
        echo "<td>".$v['CountCure']."</td>";
        echo "<td>".$v['SpareOfcure']."</td>";
        echo "<td>".$v['StockInplan']."</td>";
        echo "<td>".$v['Total']."</td>";
        echo "<td>".$v['TotalHours']."</td>";
				echo "<td>".$v['PersenGreentire']."</td>";
				echo "<td>".$v['LackShift']."</td>";
				echo "<td>".$v['TargetTemp']."</td>";
				echo "<td>".$v['OrderLackshift']."</td>";
        echo "<td>".$v['LackShift2']."</td>";
        echo "<td>".$v['CountPrint']."</td>";
        echo "<td>".$v['CureDay']."</td>";
        echo "<td>".$v['BL']."</td>";
        echo "<td>".$v['Actual']."</td>";
        echo "<td>".$v['TireLackShift']."</td>";
        echo "<td>".$v['TireLackDay']."</td>";
				echo "</tr>";
			}
		?>
    <tr>
			<th colspan=4>
				Total
			</th>
			<th>
        <?php
				$sumtarget = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget += $v['TotalShareMold'];
    				}
    				echo $sumtarget;
				?>
      </th>
      <th>
        <?php
				$sumtarget2 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget2 += $v['TotalPrint'];
    				}
    				echo $sumtarget2;
				?>
      </th>
      <th>
        <?php
				$sumtarget3 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget3 += $v['Time'];
    				}
    				echo $sumtarget3;
				?>
      </th>
      <th>
        <?php
				$sumtarget4 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget4 += $v['Countprintcure'];
    				}
    				echo $sumtarget4;
				?>
      </th>
      <th>
        <?php
				$sumtarget5 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget5 += $v['Rateprint'];
    				}
    				echo $sumtarget5;
				?>
      </th>
      <th>
        <?php
				$sumtarget6 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget6 += $v['TimeCureFG'];
    				}
    				echo $sumtarget7;
				?>
      </th>
      <th>
        <?php
				$sumtarget7 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget7 += $v['CountPrintcurFG'];
    				}
    				echo $sumtarget7;
				?>
      </th>
      <th>
        <?php
				$sumtarget8 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget8 += $v['RatePrintFG'];
    				}
    				echo $sumtarget8;
				?>
      </th>
      <th>
        <?php
        $sumtarget9 = 0;
            foreach ($data as $k => $v) {
              $sumtarget9 += $v['GreentireShift'];
            }
            echo $sumtarget9;
        ?>
      </th>
      <th>
        <?php
        $sumtarget10 = 0;
            foreach ($data as $k => $v) {
              $sumtarget10 += $v['GreentireDay'];
            }
            echo $sumtarget10;
        ?>
      </th>
      <th>
        <?php
        $sumtarget11 = 0;
            foreach ($data as $k => $v) {
              $sumtarget11 += $v['CountCure'];
            }
            echo $sumtarget11;
        ?>
      </th>
      <th>
        <?php
        $sumtarget12 = 0;
            foreach ($data as $k => $v) {
              $sumtarget12 += $v['SpareOfcure'];
            }
            echo $sumtarget12;
        ?>
      </th>
      <th>
        <?php
        $sumtarget13 = 0;
            foreach ($data as $k => $v) {
              $sumtarget13 += $v['StockInplan'];
            }
            echo $sumtarget13;
        ?>
      </th>
      <th>
        <?php
        $sumtarget14 = 0;
            foreach ($data as $k => $v) {
              $sumtarget14 += $v['Total'];
            }
            echo $sumtarget14;
        ?>
      </th>
      <th>

      </th>
      <th>


      </th>
      <th>
        <?php
        $sumtarget17 = 0;
            foreach ($data as $k => $v) {
              $sumtarget17 += $v['LackShift'];
            }
            echo $sumtarget17;
        ?>
      </th>
      <th>
        <?php
        $sumtarget18 = 0;
            foreach ($data as $k => $v) {
              $sumtarget18 += $v['TargetTemp'];
            }
            echo $sumtarget18;
        ?>
      </th>
      <th>
        <?php
        $sumtarget19 = 0;
            foreach ($data as $k => $v) {
              $sumtarget19 += $v['OrderLackshift'];
            }
            echo $sumtarget19;
        ?>
      </th>
      <th>
        <?php
        $sumtarget20 = 0;
            foreach ($data as $k => $v) {
              $sumtarget20 += $v['LackShift2'];
            }
            echo $sumtarget20;
        ?>
      </th>
      <th>
        <?php
        $sumtarget21 = 0;
            foreach ($data as $k => $v) {
              $sumtarget21 += $v['CountPrint'];
            }
            echo $sumtarget21;
        ?>
      </th>
      <th>
        <?php
        $sumtarget22 = 0;
            foreach ($data as $k => $v) {
              $sumtarget22 += $v['CureDay'];
            }
            echo $sumtarget22;
        ?>
      </th>
      <th>
        <?php
        $sumtarget23 = 0;
            foreach ($data as $k => $v) {
              $sumtarget23 += $v['BL'];
            }
            echo $sumtarget23;
        ?>
      </th>
      <th>
        <?php
        $sumtarget24 = 0;
            foreach ($data as $k => $v) {
              $sumtarget24 += $v['Actual'];
            }
            echo $sumtarget24;
        ?>
      </th>
      <th>
        <?php
        $sumtarget25 = 0;
            foreach ($data as $k => $v) {
              $sumtarget25 += $v['TireLackShift'];
            }
            echo $sumtarget25;
        ?>
      </th>
      <th>
        <?php
        $sumtarget26 = 0;
            foreach ($data as $k => $v) {
              $sumtarget26 += $v['TireLackDay'];
            }
            echo $sumtarget26;
        ?>
      </th>

		</tr>



	</table>

</body>
</html>
