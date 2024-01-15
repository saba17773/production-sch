<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<style type="text/css">
    table {
	    border-collapse: collapse;
	    width: 120%;
	    font-size:8px;
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
    <thead>
  		<tr>
  			<td colspan="4" rowspan="2" >
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
  			<td colspan="25" rowspan="2">
  				<h2>
  					รายงาน หน้ายางผลิตได้
  				</h2>
  			</td>
  		</tr>
  		<tr>
  			<td colspan="9" align="left">
  				<?php echo $date; ?>
  				<font color="red"><?php echo date('d/m/Y H:i'); ?></font>
  				<br><br>
  				กะ <?php echo $shift; ?>
  			</td>
  		</tr>
      <tr>
        <th rowspan="2" width="2%">No</th>
  			<th rowspan="2" width="3%">Item Id</th>
  			<th rowspan="2" width="7%">Item Name</th>
  			<th rowspan="2" width="4%">Color</th>
  		  <th colspan="5" width="8%">หน้ายาง แผนกออกยางนอก กะกลางวัน</th>
        <th rowspan="2" width="3%">สต็อคหน้ายาง<BR>นับจริง</th>
        <th rowspan="2" width="5%">เปรียบเทียบ<BR>นับจริง&ในระบบ</th> -->
         <th colspan="1" width="5%">จ่ายยางมากกว่า</th>
        <th rowspan="2" width="5%">แผนกสร้างโครง<BR>เบิกยาง</th>
        <th rowspan="2" width="5%">เปรียบเทียบ<BR>เบิก-จ่าย</th>
        <th colspan="3" width="5%">คันที่1</th>
        <th colspan="3" width="5">คันที่2</th>
        <th colspan="3" width="5">คันที่3</th>
        <th colspan="3" width="5%">คันที่4</th>
        <th colspan="3" width="5%">คันที่5</th>
        <th colspan="3" width="5%">คันที่6</th>
        <th colspan="3" width="5%">คันที่7</th>
        <th colspan="3" width="5%">คันที่8</th>


    </tr>
    <tr>
      <th width="3%">Stock</th>
      <th width="3%">ผลิตได้</th>
      <th width="3%">จ่ายออก</th>
      <th width="3%">ยางไม่ได้<BR>Spec/ยางเก็บงาน</th>
      <th width="3%">คงเหลือ</th>
      <th width="3%">กลางวัน</th>
      <th width="2%">เบอร์รถ</th>
      <th width="2%">จำนวน<BR>(เส้น)</th>
      <th width="2%">จ่ายออก</th>
      <th width="2%">เบอร์รถ</th>
      <th width="2%">จำนวน<BR>(เส้น)</th>
      <th width="2%">จ่ายออก</th>
      <th width="2%">เบอร์รถ</th>
      <th width="2%">จำนวน<BR>(เส้น)</th>
      <th width="2%">จ่ายออก</th>
      <th width="2%">เบอร์รถ</th>
      <th width="2%">จำนวน<BR>(เส้น)</th>
      <th width="2%">จ่ายออก</th>
      <th width="2%">เบอร์รถ</th>
      <th width="2%">จำนวน<BR>(เส้น)</th>
      <th width="2%">จ่ายออก</th>
      <th width="2%">เบอร์รถ</th>
      <th width="2%">จำนวน<BR>(เส้น)</th>
      <th width="2%">จ่ายออก</th>
      <th width="2%">เบอร์รถ</th>
      <th width="2%">จำนวน<BR>(เส้น)</th>
      <th width="2%">จ่ายออก</th>
      <th width="2%">เบอร์รถ</th>
      <th width="2%">จำนวน<BR>(เส้น)</th>
      <th width="2%">จ่ายออก</th>


    </tr>

    </thead>
    <?php
       foreach ($data as $k => $v) {
        if($v['Stock2'] == 0 || $v['Stock2'] == NULL){
          $v['Stock2'] = "";
        }
        if($v['TotalProduct'] == 0 || $v['TotalProduct'] == NULL){
          $v['TotalProduct'] = "";
        }
        if($v['TotalPayOfCar'] == 0 || $v['TotalPayOfCar'] == NULL){
          $v['TotalPayOfCar'] = "";
        }
        if($v['TireNotSpec'] == 0 || $v['TireNotSpec'] == NULL){
          $v['TireNotSpec'] = "";
        }
        if($v['Total'] == 0 || $v['Total'] == NULL){
          $v['Total'] = "";
        }
        if($v['StockTire'] == 0 || $v['StockTire'] == NULL){
          $v['StockTire'] = "";
        }
        if($v['CompareNum'] == 0 || $v['CompareNum'] == NULL){
          $v['CompareNum'] = "";
        }
        if($v['TotalPlanCreate'] == 0 || $v['TotalPlanCreate'] == NULL){
          $v['TotalPlanCreate'] = "";
        }

        if($v['CountCar1'] == 0 || $v['CountCar1'] == NULL){
          $v['CountCar1'] = "";
        }
        if($v['CountCar2'] == 0 || $v['CountCar2'] == NULL){
          $v['CountCar2'] = "";
        }
        if($v['CountCar3'] == 0 || $v['CountCar3'] == NULL){
          $v['CountCar3'] = "";
        }
        if($v['CountCar4'] == 0 || $v['CountCar4'] == NULL){
          $v['CountCar4'] = "";
        }
        if($v['CountCar5'] == 0 || $v['CountCar5'] == NULL){
          $v['CountCar5'] = "";
        }
        if($v['CountCar6'] == 0 || $v['CountCar6'] == NULL){
          $v['CountCar6'] = "";
        }
        if($v['CountCar7'] == 0 || $v['CountCar7'] == NULL){
          $v['CountCar7'] = "";
        }
        if($v['CountCar8'] == 0 || $v['CountCar8'] == NULL){
          $v['CountCar8'] = "";
        }
        if($v['NumberCar1'] == 0 || $v['NumberCar1'] == NULL){
          $v['NumberCar1'] = "";
        }
        if($v['NumberCar2'] == 0 || $v['NumberCar2'] == NULL){
          $v['NumberCar2'] = "";
        }
        if($v['NumberCar3'] == 0 || $v['NumberCar3'] == NULL){
          $v['NumberCar3'] = "";
        }
        if($v['NumberCar4'] == 0 || $v['NumberCar4'] == NULL){
          $v['NumberCar4'] = "";
        }
        if($v['NumberCar5'] == 0 || $v['NumberCar5'] == NULL){
          $v['NumberCar5'] = "";
        }
        if($v['NumberCar6'] == 0 || $v['NumberCar6'] == NULL){
          $v['NumberCar6'] = "";
        }
        if($v['NumberCar7'] == 0 || $v['NumberCar7'] == NULL){
          $v['NumberCar7'] = "";
        }
        if($v['NumberCar8'] == 0 || $v['NumberCar8'] == NULL){
          $v['NumberCar8'] = "";
        }
        if($v['PayOfCar'] == 0 || $v['PayOfCar'] == NULL){
          $v['PayOfCar'] = "";
        }
        if($v['PayOfCar2'] == 0 || $v['PayOfCar2'] == NULL){
          $v['PayOfCar2'] = "";
        }
        if($v['PayOfCar3'] == 0 || $v['PayOfCar3'] == NULL){
          $v['PayOfCar3'] = "";
        }
        if($v['PayOfCar4'] == 0 || $v['PayOfCar4'] == NULL){
          $v['PayOfCar4'] = "";
        }
        if($v['PayOfCar5'] == 0 || $v['PayOfCar5'] == NULL){
          $v['PayOfCar5'] = "";
        }
        if($v['PayOfCar6'] == 0 || $v['PayOfCar6'] == NULL){
          $v['PayOfCar6'] = "";
        }
        if($v['PayOfCar7'] == 0 || $v['PayOfCar7'] == NULL){
          $v['PayOfCar7'] = "";
        }
        if($v['PayOfCar8'] == 0 || $v['PayOfCar8'] == NULL){
          $v['PayOfCar8'] = "";
        }
        echo "<tr>";
        echo "<td>".($k+1)."</td>";
        echo "<td>".$v['ItemId']."</td>";
        echo "<td class='td'>".$v['ItemGTName']."</td>";
        echo "<td>".$v['Color']."</td>";
        echo "<td>".$v['Stock2']."</td>";
        echo "<td>".$v['TotalProduct']."</td>";
        echo "<td>".$v['TotalPayOfCar']."</td>";
        echo "<td >".$v['TireNotSpec']."</td>";
        echo "<td>".$v['Total']."</td>";
        echo "<td>".$v['StockTire']."</td>";
        echo "<td>".$v['CompareNum']."</td>";
        echo "<td class='td'>".$v['CheckCountOut']."</td>";
        echo "<td>".$v['TotalPlanCreate']."</td>";
        echo "<td>".$v['CompareBill']."</td>";
        echo "<td>".$v['CountCar1']."</td>";
        echo "<td class='td'>".$v['NumberCar1']."</td>";
        echo "<td>".$v['PayOfCar']."</td>";
        echo "<td>".$v['CountCar2']."</td>";
        echo "<td class='td'>".$v['NumberCar2']."</td>";
        echo "<td>".$v['PayOfCar2']."</td>";
        echo "<td>".$v['CountCar3']."</td>";
        echo "<td class='td'>".$v['NumberCar3']."</td>";
        echo "<td>".$v['PayOfCar3']."</td>";
        echo "<td>".$v['CountCar4']."</td>";
        echo "<td class='td'>".$v['NumberCar4']."</td>";
        echo "<td>".$v['PayOfCar4']."</td>";
        echo "<td>".$v['CountCar5']."</td>";
        echo "<td class='td'>".$v['NumberCar5']."</td>";
        echo "<td>".$v['PayOfCar5']."</td>";
        echo "<td>".$v['CountCar6']."</td>";
        echo "<td class='td'>".$v['NumberCar6']."</td>";
        echo "<td>".$v['PayOfCar6']."</td>";
        echo "<td>".$v['CountCar7']."</td>";
        echo "<td class='td'>".$v['NumberCar7']."</td>";
        echo "<td>".$v['PayOfCar7']."</td>";
        echo "<td>".$v['CountCar8']."</td>";
        echo "<td class='td'>".$v['NumberCar8']."</td>";
        echo "<td>".$v['PayOfCar8']."</td>";
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
    					$sumtarget += $v['Stock2'];
    				}
    				echo $sumtarget;
				?></th>
			<th>	<?php
				$sumtarget1 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget1 += $v['TotalProduct'];
    				}
    				echo $sumtarget1;?></th>
			<th><?php
				$sumtarget2 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget2 += $v['TotalPayOfCar'];
    				}
    				echo $sumtarget2;?></th>
			<th><?php
				$sumtarget3 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget3 += $v['TireNotSpec'];
    				}
    				echo $sumtarget3;?></th>
			<th><?php
				$sumtarget4 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget4 += $v['Total'];
    				}
    				echo $sumtarget4;?></th>
			<th><?php
				$sumtarget5 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget5 += $v['StockTire'];
    				}
    				echo $sumtarget5;?></th>
            <th><?php
              $sumtarget6 = 0;
                  foreach ($data as $k => $v) {
                    $sumtarget6 += $v['CompareNum'];
                  }
                  echo $sumtarget6;?></th>
            <th></th>
		<th><?php
				$sumtarget7 = 0;
    				foreach ($data as $k => $v) {
    					$sumtarget7 += $v['TotalPlanCreate'];
    				}
    				echo $sumtarget7;?></th>
            <th></th>
            <th  colspan=24></th>


		</tr>


<?php

$type ="";
 ?>
</table>

</body>
</html>

<?php

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A3-L', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
