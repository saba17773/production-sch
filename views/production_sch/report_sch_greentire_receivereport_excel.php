<?php ob_start();
error_reporting(0);
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductiongreentireReport_".Date("Ymd_His").".xls");
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
			<td colspan="9" rowspan="2">
				<h2>
					รายงาน  รับ-เข้า-เบิก  ยางกรีนไทร์
				</h2>
			</td>
		</tr>
		<tr>
			<td colspan="7" align="left">
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
			<!-- <th rowspan="2" width="4%">PR</th> -->
			<!-- <th colspan="3" width="13%">CODE</th> -->
			<th rowspan="2" width="10%">COLOR</th>
			<th colspan="3" width="15%">ยางกรีนไทร์</th>
			<th colspan="11"><?php echo $shift_head; ?></th>
		</tr>
		<tr>
			<!-- <th width="4%">Brand</th>
			<th width="5%">Pattern</th>
			<th width="4%">T/T</th> -->
			<td width="5%"><font size="2.2">Spare หน้าเตา(เส้น)</font></td>
			<td width="5%"><font size="2.2">Stock ในแผนก(เส้น)</font></td>
			<td width="7%"><font size="2.2">คงเหลือ หน้าเตา+<BR>แผนกกรีนไทร์<BR> (เส้น)</font></td>
      <?php if($shiftcheck == 1){?>
        <td width="5%" ><font size="2.2">แผนสร้าง กะกลางวัน (เส้น)</font></td>
        <td width="5%"> <font size="2.2">ผลิตได้ กะกลางวัน (เส้น)</font></td>
      <?php  }else {?>
        <td width="5%"><font size="2.2">แผนสร้าง กะกลางคืน (เส้น)</font></td>
        <td width="5%"<font size="2.2">ผลิตได้ กะกลางคืน (เส้น)</font></td>
        <?php  }  ?>
		   <td width="5%"><font size="2.2">รับเข้า (เส้น)</font></td>
       <td width="5%"><font size="2.2">จ่ายออก (เส้น)</font></td>
			<td width="5%"><font size="2.2">อบยาง เบิก(เส้น)</font></td>
			<td width="6%"><font size="2.2">ยางไม่ได้ Spec(เส้น)</font></td>
			<td width="7%"><font size="2.2">Stock แผนก<BR>กรีนไทร์ (เส้น)</font></td>
      <td width="5%"><font size="2.2">นับจริง</font></td>
			<td width="5%"><font size="2.2">เปรียบเทียบนับจริง & ในระบบ </font></td>
			<td width="5%"><font size="2.2">เปรียบเทียบสร้างโครงผลิต ได้รับเข้า</font></td>
			<td width="5%"><font size="2.2">เปรียบเทียบอบยางเบิกจ่ายออก</font></td>
		</tr>
  </thead>

		<?php
			foreach ($data as $k => $v) {
        if($v['SpareOfcure'] == null || $v['SpareOfcure'] == 0){
          $v['SpareOfcure'] = "";
        }
        if($v['StockInplan'] == null || $v['StockInplan'] == 0){
          $v['StockInplan'] = "";
        }
        if($v['TOTAL'] == null || $v['TOTAL'] == 0){
          $v['TOTAL'] = "";
        }
        if($v['TOTAL'] == null || $v['TOTAL'] == 0){
          $v['TOTAL'] = "";
        }
        if($v['CountShift'] == null || $v['CountShift'] == 0){
          $v['CountShift'] = "";
        }
        if($v['CountPlan'] == null || $v['CountPlan'] == 0){
          $v['CountPlan'] = "";
        }
        if($v['CountIn'] == null || $v['CountIn'] == 0){
          $v['CountIn'] = "";
        }
        if($v['CountCure'] == null || $v['CountCure'] == 0){
          $v['CountCure'] = "";
        }
        if($v['CountNotSpec'] == null || $v['CountNotSpec'] == 0){
          $v['CountNotSpec'] = "";
        }
        if($v['TotalSockGT'] == null || $v['TotalSockGT'] == 0){
          $v['TotalSockGT'] = "";
        }
        if($v['CountReal'] == null || $v['CountReal'] == 0){
          $v['CountReal'] = "";
        }
        if($v['Chekdata'] == null || $v['Chekdata'] == 0){
          $v['Chekdata'] = "";
        }
        if($v['CountOut'] == null || $v['CountOut'] == 0){
          $v['CountOut'] = "";
        }

				echo "<tr>";
				echo "<td>".($k+1)."</td>";
				echo "<td>".$v['ItemId']."</td>";
				echo "<td class='td'>".$v['ItemGTName']."</td>";
				// echo "<td>".$v['PR']."</td>";
				// echo "<td>".$v['Brand']."</td>";
				// echo "<td>".$v['Pattern']."</td>";
				// echo "<td>".$v['TT']."</td>";
				echo "<td>".$v['Color']."</td>";

				echo "<td>".$v['SpareOfcure']."</td>";
				echo "<td>".$v['StockInplan']."</td>";
				echo "<td>".$v['TOTAL']."</td>";
				echo "<td>".$v['CountShift']."</td>";
				echo "<td>".$v['CountPlan']."</td>";
				echo "<td>".$v['CountIn']."</td>";
        echo "<td>".$v['CountOut']."</td>";
				echo "<td>".$v['CountCure']."</td>";
				echo "<td>".$v['CountNotSpec']."</td>";
				echo "<td>".$v['TotalSockGT']."</td>";
        echo "<td>".$v['CountReal']."</td>";
        echo "<td>".$v['Chekdata']."</td>";
        echo "<td>".$v['CheckCountShift']."</td>";
        echo "<td>".$v['CheckCountOut']."</td>";
				echo "</tr>";
			}
		?>

		<tr>
			<th colspan=4>
				Total
			</th>
			<th>	<?php
				$sumtarget = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget += $v['SpareOfcure'];
    				}
    				echo $sumtarget;
				?></th>
			<th>	<?php
				$sumtarget1 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget1 += $v['StockInplan'];
    				}
    				echo $sumtarget1;?></th>
			<th><?php
				$sumtarget2 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget2 += $v['TOTAL'];
    				}
    				echo $sumtarget2;?></th>
			<th><?php
				$sumtarget3 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget3 += $v['CountShift'];
    				}
    				echo $sumtarget3;?></th>
			<th><?php
				$sumtarget4 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget4 += $v['CountPlan'];
    				}
    				echo $sumtarget4;?></th>
			<th><?php
				$sumtarget5 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget5 += $v['CountIn'];
    				}
    				echo $sumtarget5;?></th>
            <th><?php
              $sumtarget14 = 0;
                  foreach ($data as $k => $v) {
                    $sumtarget14 += $v['CountOut'];
                  }
                  echo $sumtarget14;?></th>
		<th><?php
				$sumtarget6 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget6 += $v['CountCure'];
    				}
    				echo $sumtarget6;?></th>
			<th><?php
				$sumtarget7 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget7 += $v['CountNotSpec'];
    				}
    				echo $sumtarget7;?></th>
      <th><?php
				$sumtarget8 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget8 += $v['TotalSockGT'];
    				}
    				echo $sumtarget8;?></th>
			<th><?php
				$sumtarget9 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget9 += $v['CountReal'];
    				}
    				echo $sumtarget9;?></th>
            <th><?php
              $sumtarget10 = 0;
                  foreach ($data as $k => $v) {
                    $sumtarget10 += $v['Chekdata'];
                  }
                  echo $sumtarget10;?></th>
            <th>
			<th></th>
			<!-- <th></th> -->
		</tr>

	</table>

</body>
</html>
