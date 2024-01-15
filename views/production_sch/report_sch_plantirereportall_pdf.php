<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<style type="text/css">
  table {
    border-collapse: collapse;
    width: 100%;
    font-size: 10px;
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
    font-size: 11px;
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
      font-size: 10px;
    }
  </style>
</head>

<body>

  <table>
    <thead>
      <tr>
        <td colspan="2" rowspan="2">
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
        <td colspan="5" rowspan="2">
          <h2>
            รายงาน Stock แผนสั่งออกหน้ายาง
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
        <th rowspan="2" width="50px">NO.</th>
        <th colspan="1" width="50px">ลำดับ</th>
        <th colspan="1" width="80px">ลำดับออกยาง</th>

        <th rowspan="2" width="80px">Item EXT</th>
        <th rowspan="2" width="200px">Item name</th>
        <th rowspan="2" width="80px">Color</th>
        <th colspan="3" width="200px">Stock หน้ายาง</th>
        <th colspan="2" width="140px">แผนกสร้างโครง <?php echo $date1; ?></th>
        <th colspan="2" width="140x">แผนกสร้างโครง <?php echo $date2; ?></th>
        <th colspan="2" width="140px">แผนกสร้างโครง <?php echo $date3; ?></th>

        <th rowspan="2" width="80px">Compound</th>
        <th rowspan="2" width="80px">BL</th>
        <!-- <th colspan="2" width="80px">Status Stock หน้ายาง</th> -->

      </tr>
      <tr>
        <th width="50px">กรีนไทร์ขาดอบ</th>
        <th width="50px">หน้ายางขาด</th>
        <!-- <th width="50px">ลำดับออกยาง<BR>ช่อง 2</th> -->
        <th width="70px">
          <font size="2.2">แผนก<BR>ออกหน้ายาง</font>
        </th>
        <th width="50px">
          <font size="2.2">แผนก<BR>สร้างโครง</font>
        </th>
        <th width="50px">
          <font size="2.2">Total(เส้น)</font>
        </th>
        <th width="50px">
          <font size="2.2">กะกลางวัน</font>
        </th>
        <th width="50px">
          <font size="2.2">กะกลางคืน</font>
        </th>
        <th width="50px">
          <font size="2.2">กะกลางวัน</font>
        </th>
        <th width="50px">
          <font size="2.2">กะกลางคืน</font>
        </th>
        <th width="50px">
          <font size="2.2">กะกลางวัน</font>
        </th>
        <th width="50px">
          <font size="2.2">กะกลางคืน</font>
        </th>

        <!-- <th width="50px">
          <font size="2.2">17/08/2562</font>
        </th> -->

      </tr>
    </thead>

    <?php
    foreach ($data as $k => $v) {
      echo "<tr>";
      echo "<td height ='30px'>" . ($k + 1) . "</td>";
      echo "<td>" . $v['OrderLackshift'] . "</td>";
      echo "<td>" . $v['checktotal'] . "</td>";

      echo "<td>" . $v['ItemId'] . "</td>";
      echo "<td>" . $v['Name'] . "</td>";
      echo "<td>" . $v['DSG_COLOR'] . "</td>";
      echo "<td>" . $v['GrandTotal'] . "</td>";
      echo "<td>" . $v['TotalSystemPD'] . "</td>";
      echo "<td>" . $v['Total'] . "</td>";
      echo "<td>" . $v['ActualDay1C'] . "</td>";
      echo "<td>" . $v['ActualDay1D'] . "</td>";
      echo "<td>" . $v['ActualDay2C'] . "</td>";
      echo "<td>" . $v['ActualDay2D'] . "</td>";
      echo "<td>" . $v['ActualDay3C'] . "</td>";
      echo "<td>" . $v['ActualDay3D'] . "</td>";
      echo "<td>" . $v['ITEMNAME_LIST'] . "</td>";
      echo "<td>" . $v['BL'] . "</td>";
      // echo "<td>" . $v['StockStatus'] . "</td>";

      echo "</tr>";
    }
    ?>
    <tr>
      <th colspan=6>
        Total
      </th>
      <th> <?php
            $sumtarget = 0;
            foreach ($data as $k => $v) {
              $sumtarget += $v['GrandTotal'];
            }
            echo $sumtarget;
            ?></th>
      <th> <?php
            $sumtarget1 = 0;
            foreach ($data as $k => $v) {
              $sumtarget1 += $v['TotalSystemPD'];
            }
            echo $sumtarget1;
            ?></th>
      <th> <?php
            $sumtarget2 = 0;
            foreach ($data as $k => $v) {
              $sumtarget2 += $v['Total'];
            }
            echo $sumtarget2;
            ?></th>
      <th> <?php
            $sumtarget3 = 0;
            foreach ($data as $k => $v) {
              $sumtarget3 += $v['ActualDay1C'];
            }
            echo $sumtarget3;
            ?></th>
      <th> <?php
            $sumtarget4 = 0;
            foreach ($data as $k => $v) {
              $sumtarget4 += $v['ActualDay1D'];
            }
            echo $sumtarget4;
            ?></th>
      <th> <?php
            $sumtarget5 = 0;
            foreach ($data as $k => $v) {
              $sumtarget5 += $v['ActualDay2C'];
            }
            echo $sumtarget5;
            ?></th>
      <th> <?php
            $sumtarget6 = 0;
            foreach ($data as $k => $v) {
              $sumtarget6 += $v['ActualDay2D'];
            }
            echo $sumtarget6;
            ?></th>
      <th> <?php
            $sumtarget7 = 0;
            foreach ($data as $k => $v) {
              $sumtarget7 += $v['ActualDay3C'];
            }
            echo $sumtarget7;
            ?></th>
      <th> <?php
            $sumtarget8 = 0;
            foreach ($data as $k => $v) {
              $sumtarget8 += $v['ActualDay3D'];
            }
            echo $sumtarget8;
            ?></th>

      <th></th>
      <th> <?php
            $sumtarget15 = 0;
            foreach ($data as $k => $v) {
              $sumtarget15 += $v['BL'];
            }
            echo $sumtarget15;
            ?></th>

    <tr>




  </table>


</body>

</html>

<?php

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
