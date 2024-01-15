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
    <thead>
  		<tr>
  			<td colspan="3" rowspan="2" >
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
  			<td colspan="13" rowspan="2">
  				<h2>
  					รายงานเบิกจ่ายหน้ายาง
  				</h2>
  			</td>
  		</tr>
  		<tr>
  			<td colspan="5" align="left">
  				<?php echo $date; ?>
  				<font color="red"><?php echo date('d/m/Y H:i'); ?></font>
  				<br><br>
  				กะ <?php echo $shift; ?>
  			</td>
  		</tr>
      <tr>
        <th rowspan="3" width="5%">No</th>
  			<th rowspan="3" width="7%">Item Id</th>
  			<th rowspan="3" width="15%">Item Name</th>
  			<th rowspan="3" width="10%">Color</th>
  		  <th colspan="16" width="10%">รายงาน STOCK คงเหลือนับจริง</th>
        <th rowspan="3" width="10%">รวม Stock</th>

    </tr>
    <tr>
      <th colspan="2" width="10%">คันที่1</th>
      <th colspan="2" width="10%">คันที่2</th>
      <th colspan="2" width="10%">คันที่3</th>
      <th colspan="2" width="10%">คันที่4</th>
      <th colspan="2" width="10%">คันที่5</th>
      <th colspan="2" width="10%">คันที่6</th>
      <th colspan="2" width="10%">คันที่7</th>
      <th colspan="2" width="10%">คันที่8</th>

    </tr>
    <tr>
      <th width="5%">เบอร์รถ</th>
      <th width="5%">จำนวน<BR>(เส้น)</th>
      <th width="5%">เบอร์รถ</th>
      <th width="5%">จำนวน<BR>(เส้น)</th>
      <th width="5%">เบอร์รถ</th>
      <th width="5%">จำนวน<BR>(เส้น)</th>
      <th width="5%">เบอร์รถ</th>
      <th width="5%">จำนวน<BR>(เส้น)</th>
      <th width="5%">เบอร์รถ</th>
      <th width="5%">จำนวน<BR>(เส้น)</th>
      <th width="5%">เบอร์รถ</th>
      <th width="5%">จำนวน<BR>(เส้น)</th>
      <th width="5%">เบอร์รถ</th>
      <th width="5%">จำนวน<BR>(เส้น)</th>
      <th width="5%">เบอร์รถ</th>
      <th width="5%">จำนวน<BR>(เส้น)</th>

    </tr>
    </thead>
    <?php
      foreach ($data as $k => $v) {
        if($v['Car2_1'] == 0 || $v['Car2_1'] == NULL){
          $v['Car2_1'] = "";
        }
        if($v['Car2_2'] == 0 || $v['Car2_2'] == NULL){
          $v['Car2_2'] = "";
        }
        if($v['Car2_3'] == 0 || $v['Car2_3'] == NULL){
          $v['Car2_3'] = "";
        }
        if($v['Car2_4'] == 0 || $v['Car2_4'] == NULL){
          $v['Car2_4'] = "";
        }
        if($v['Car2_5'] == 0 || $v['Car2_5'] == NULL){
          $v['Car2_5'] = "";
        }
        if($v['Car2_6'] == 0 || $v['Car2_6'] == NULL){
          $v['Car2_6'] = "";
        }
        if($v['Car2_7'] == 0 || $v['Car2_7'] == NULL){
          $v['Car2_7'] = "";
        }
        if($v['Car2_8'] == 0 || $v['Car2_8'] == NULL){
          $v['Car2_8'] = "";
        }

        // if($v['CarNumber2_1'] == 0 || $v['CarNumber2_1'] == NULL){
        //   $v['CarNumber2_1'] = "";
        // }
        // if($v['CarNumber2_2'] == 0 || $v['CarNumber2_2'] == NULL){
        //   $v['CarNumber2_2'] = "";
        // }
        // if($v['CarNumber2_3'] == 0 || $v['CarNumber2_3'] == NULL){
        //   $v['CarNumber2_3'] = "";
        // }
        // if($v['CarNumber2_4'] == 0 || $v['CarNumber2_4'] == NULL){
        //   $v['CarNumber2_4'] = "";
        // }
        // if($v['CarNumber2_5'] == 0 || $v['CarNumber2_5'] == NULL){
        //   $v['CarNumber2_5'] = "";
        // }
        // if($v['CarNumber2_6'] == 0 || $v['CarNumber2_6'] == NULL){
        //   $v['CarNumber2_6'] = "";
        // }
        // if($v['CarNumber2_7'] == 0 || $v['CarNumber2_7'] == NULL){
        //   $v['CarNumber2_7'] = "";
        // }
        // if($v['CarNumber2_8'] == 0 || $v['CarNumber2_8'] == NULL){
        //   $v['CarNumber2_8'] = "";
        // }
        echo "<tr>";
        echo "<td>".($k+1)."</td>";
        echo "<td>".$v['ItemId']."</td>";
        echo "<td class='td'>".$v['ItemGTName']."</td>";
        echo "<td>".$v['Color']."</td>";
        echo "<td>".$v['CarNumber2_1']."</td>";
        echo "<td>".$v['Car2_1']."</td>";
        echo "<td class='td'>".$v['CarNumber2_2']."</td>";
        echo "<td>".$v['Car2_2']."</td>";
        echo "<td>".$v['CarNumber2_3']."</td>";
        echo "<td>".$v['Car2_3']."</td>";
        echo "<td class='td'>".$v['CarNumber2_4']."</td>";
        echo "<td>".$v['Car2_4']."</td>";
        echo "<td>".$v['CarNumber2_5']."</td>";
        echo "<td>".$v['Car2_5']."</td>";
        echo "<td class='td'>".$v['CarNumber2_6']."</td>";
        echo "<td>".$v['Car2_6']."</td>";
        echo "<td>".$v['CarNumber2_7']."</td>";
        echo "<td>".$v['Car2_7']."</td>";
        echo "<td class='td'>".$v['CarNumber2_8']."</td>";
        echo "<td>".$v['Car2_8']."</td>";
        echo "<td>".($v['Car2_1']+$v['Car2_2']+$v['Car2_3']+$v['Car2_4']
        +$v['Car2_5']+$v['Car2_6']+$v['Car2_7']+$v['Car2_8'])."</td>";

        echo "</tr>";
      }
    ?>
    <tr>
      <th colspan=4>
        Total
      </th>
            <th></th>
            <th><?php
                $sumtarget11 = 0;
                    foreach ($data as $k => $v) {
                      $sumtarget11 += $v['Car2_1'];
                    }
                    echo $sumtarget11;?></th>
                    <th></th>
            <th><?php
                $sumtarget12 = 0;
                    foreach ($data as $k => $v) {
                      $sumtarget12 += $v['Car2_2'];
                    }
                    echo $sumtarget12;?></th>
              <th></th>
              <th><?php
                  $sumtarget13 = 0;
                      foreach ($data as $k => $v) {
                        $sumtarget13 += $v['Car2_3'];
                      }
                      echo $sumtarget13;?></th>
                      <th></th>
              <th><?php
                  $sumtarget14 = 0;
                      foreach ($data as $k => $v) {
                        $sumtarget14 += $v['Car2_4'];
                      }
                      echo $sumtarget14;?></th>
              <th></th>
              <th><?php
                  $sumtarget15 = 0;
                      foreach ($data as $k => $v) {
                        $sumtarget15 += $v['Car2_5'];
                      }
                      echo $sumtarget15;?></th>
                      <th></th>
              <th><?php
                  $sumtarget16 = 0;
                      foreach ($data as $k => $v) {
                        $sumtarget16 += $v['Car2_6'];
                      }
                      echo $sumtarget16;?></th>
                <th></th>
                <th><?php
                    $sumtarget17 = 0;
                        foreach ($data as $k => $v) {
                          $sumtarget17 += $v['Car2_7'];
                        }
                        echo $sumtarget17;?></th>
                  <th></th>
                <th><?php
                    $sumtarget18 = 0;
                        foreach ($data as $k => $v) {
                          $sumtarget18 += $v['Car2_8'];
                        }
                        echo $sumtarget18;?></th>
                <th><?php
                    $sumtarget19 = 0;
                        foreach ($data as $k => $v) {
                          $sumtarget19 += $v['Car2_1'] + $v['Car2_2']+$v['Car2_3']
                          +$v['Car2_4'] + $v['Car2_5']+ $v['Car2_6'] +$v['Car2_7']
                          + $v['Car2_8'];
                        }
                        echo $sumtarget19;?></th>

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
$pdf = new mPDF('th','A4-L', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
