<?php ob_start();
error_reporting(0);
header("Content-type: application/vnd.ms-excel");

header("Content-Disposition: attachment; filename=ProductiongreentiredisbursenebtReport_" . Date("Ymd_His") . ".xls");
?>
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

  <table border="1">
    <thead>
      <tr>
        <td colspan="5" rowspan="2">


        </td>
        <td colspan="27" rowspan="2" align="center">
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
        <th rowspan="3" colspan="3" width="15%">Item Name</th>
        <th rowspan="3" width="10%">Color</th>
        <th colspan="3" width="10%">แผนผลิตแผนกสร้างโครงประจำวัน</th>
        <th colspan="5" width="10%">หน้ายาง แผนกสร้างโครง กะกลางวัน</th>
        <th colspan="1" width="10%">กะกลางวัน</th>
        <th rowspan="3" width="10%">BL</th>
        <th rowspan="3" width="10%">นับจริง</th>
        <th rowspan="3" width="10%">เปรียบเทียบ<BR>นับจริง&ในระบบ</th>
        <th rowspan="3" width="10%">แผนกหน้ายาง<BR>จ่ายออก</th>
        <th rowspan="3" width="10%">เปรียบเทียบ<BR>เบิก-จ่าย</th>
        <th colspan="16" width="10%">รายงาน STOCK คงเหลือนับจริง</th>
        <th rowspan="3" width="10%">รวม Stock</th>

      </tr>
      <tr>
        <th rowspan="2" width="5%"><?php echo $topic1; ?></th>
        <th rowspan="2" width="5%"><?php echo $topic2; ?></th>
        <th rowspan="2" width="5%"><?php echo $topic3; ?></th>
        <th rowspan="2" width="5%">Stock<BR>(เส้น)</th>
        <th rowspan="2" width="5%">เบิกจาก<BR>แผนกหน้ายาง</th>
        <th rowspan="2" width="5%">ยางไม่ได้<BR>Spec/ยางเก็บงาน</th>
        <th rowspan="2" width="5%">ผลิตได้</th>
        <th rowspan="2" width="5%">คงเหลือ<BR>ในระบบ</th>
        <th rowspan="2" width="5%">ไม่มีหน้ายาง<BR>แต่มีตัวเลขสร้าง</th>
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
      if ($v['Car2_1'] == 0 || $v['Car2_1'] == NULL) {
        $v['Car2_1'] = "";
      }
      if ($v['Car2_2'] == 0 || $v['Car2_2'] == NULL) {
        $v['Car2_2'] = "";
      }
      if ($v['Car2_3'] == 0 || $v['Car2_3'] == NULL) {
        $v['Car2_3'] = "";
      }
      if ($v['Car2_4'] == 0 || $v['Car2_4'] == NULL) {
        $v['Car2_4'] = "";
      }
      if ($v['Car2_5'] == 0 || $v['Car2_5'] == NULL) {
        $v['Car2_5'] = "";
      }
      if ($v['Car2_6'] == 0 || $v['Car2_6'] == NULL) {
        $v['Car2_6'] = "";
      }
      if ($v['Car2_7'] == 0 || $v['Car2_7'] == NULL) {
        $v['Car2_7'] = "";
      }
      if ($v['Car2_8'] == 0 || $v['Car2_8'] == NULL) {
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
      echo "<td>" . ($k + 1) . "</td>";
      echo "<td>" . $v['ItemId'] . "</td>";
      echo "<td colspan = '3'>" . $v['ItemGTName'] . "</td>";
      echo "<td>" . $v['Color'] . "</td>";
      echo "<td>" . $v['Target'] . "</td>";
      echo "<td>" . $v['Target1'] . "</td>";
      echo "<td>" . $v['Actual'] . "</td>";
      echo "<td>" . $v['Stock'] . "</td>";
      echo "<td>" . $v['Total'] . "</td>";
      echo "<td>" . $v['TireNotSpac'] . "</td>";
      echo "<td>" . $v['Produce'] . "</td>";
      echo "<td>" . $v['TotalSystem'] . "</td>";
      echo "<td>" . $v['CheckCountOut'] . "</td>";
      echo "<td>" . $v['BL'] . "</td>";
      echo "<td>" . $v['CountNum'] . "</td>";
      echo "<td>" . $v['CompareNum'] . "</td>";
      echo "<td>" . $v['TotalPayOfCar'] . "</td>";
      echo "<td>" . $v['CompareBill'] . "</td>";
      echo "<td>" . $v['CarNumber2_1'] . "</td>";
      echo "<td>" . $v['Car2_1'] . "</td>";
      echo "<td class='td'>" . $v['CarNumber2_2'] . "</td>";
      echo "<td>" . $v['Car2_2'] . "</td>";
      echo "<td>" . $v['CarNumber2_3'] . "</td>";
      echo "<td>" . $v['Car2_3'] . "</td>";
      echo "<td class='td'>" . $v['CarNumber2_4'] . "</td>";
      echo "<td>" . $v['Car2_4'] . "</td>";
      echo "<td>" . $v['CarNumber2_5'] . "</td>";
      echo "<td>" . $v['Car2_5'] . "</td>";
      echo "<td class='td'>" . $v['CarNumber2_6'] . "</td>";
      echo "<td>" . $v['Car2_6'] . "</td>";
      echo "<td>" . $v['CarNumber2_7'] . "</td>";
      echo "<td>" . $v['Car2_7'] . "</td>";
      echo "<td class='td'>" . $v['CarNumber2_8'] . "</td>";
      echo "<td>" . $v['Car2_8'] . "</td>";
      echo "<td>" . ($v['Car2_1'] + $v['Car2_2'] + $v['Car2_3'] + $v['Car2_4']
        + $v['Car2_5'] + $v['Car2_6'] + $v['Car2_7'] + $v['Car2_8']) . "</td>";

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
              $sumtarget += $v['Target'];
            }
            echo number_format($sumtarget);
            ?></th>
      <th> <?php
            $sumtarget1 = 0;
            foreach ($data as $k => $v) {
              $sumtarget1 += $v['Target1'];
            }
            echo number_format($sumtarget1); ?></th>
      <th><?php
          $sumtarget2 = 0;
          foreach ($data as $k => $v) {
            $sumtarget2 += $v['Actual'];
          }
          echo number_format($sumtarget2); ?></th>
      <th><?php
          $sumtarget3 = 0;
          foreach ($data as $k => $v) {
            $sumtarget3 += $v['Stock'];
          }
          echo number_format($sumtarget3); ?></th>
      <th><?php
          $sumtarget4 = 0;
          foreach ($data as $k => $v) {
            $sumtarget4 += $v['Total'];
          }
          echo number_format($sumtarget4); ?></th>
      <th><?php
          $sumtarget5 = 0;
          foreach ($data as $k => $v) {
            $sumtarget5 += $v['TireNotSpac'];
          }
          echo number_format($sumtarget5); ?></th>
      <th><?php
          $sumtarget6 = 0;
          foreach ($data as $k => $v) {
            $sumtarget6 += $v['Produce'];
          }
          echo number_format($sumtarget6); ?></th>

      <th><?php
          $sumtarget7 = 0;
          foreach ($data as $k => $v) {
            $sumtarget7 += $v['TotalSystem'];
          }
          echo number_format($sumtarget7); ?></th>
      <th></th>
      <th><?php
          $sumtarget8 = 0;
          foreach ($data as $k => $v) {
            $sumtarget8 += $v['BL'];
          }
          echo number_format($sumtarget8); ?></th>
      <th><?php
          $sumtarget9 = 0;
          foreach ($data as $k => $v) {
            $sumtarget9 += $v['CountNum'];
          }
          echo number_format($sumtarget9); ?></th>
      <th><?php
          $sumtarget10 = 0;
          foreach ($data as $k => $v) {
            $sumtarget10 += $v['CompareNum'];
          }
          echo number_format($sumtarget10); ?></th>
      <th><?php
          $sumtarget24 = 0;
          foreach ($data as $k => $v) {
            $sumtarget24 += $v['TotalPayOfCar'];
          }
          echo number_format($sumtarget24); ?></th>
      <th></th>
      <th></th>
      <th><?php
          $sumtarget11 = 0;
          foreach ($data as $k => $v) {
            $sumtarget11 += $v['Car2_1'];
          }
          echo number_format($sumtarget11); ?></th>
      <th></th>
      <th><?php
          $sumtarget12 = 0;
          foreach ($data as $k => $v) {
            $sumtarget12 += $v['Car2_2'];
          }
          echo number_format($sumtarget12); ?></th>
      <th></th>
      <th><?php
          $sumtarget13 = 0;
          foreach ($data as $k => $v) {
            $sumtarget13 += $v['Car2_3'];
          }
          echo number_format($sumtarget13); ?></th>
      <th></th>
      <th><?php
          $sumtarget14 = 0;
          foreach ($data as $k => $v) {
            $sumtarget14 += $v['Car2_4'];
          }
          echo number_format($sumtarget14); ?></th>
      <th></th>
      <th><?php
          $sumtarget15 = 0;
          foreach ($data as $k => $v) {
            $sumtarget15 += $v['Car2_5'];
          }
          echo number_format($sumtarget15); ?></th>
      <th></th>
      <th><?php
          $sumtarget16 = 0;
          foreach ($data as $k => $v) {
            $sumtarget16 += $v['Car2_6'];
          }
          echo number_format($sumtarget16); ?></th>
      <th></th>
      <th><?php
          $sumtarget17 = 0;
          foreach ($data as $k => $v) {
            $sumtarget17 += $v['Car2_7'];
          }
          echo number_format($sumtarget17); ?></th>
      <th></th>
      <th><?php
          $sumtarget18 = 0;
          foreach ($data as $k => $v) {
            $sumtarget18 += $v['Car2_8'];
          }
          echo number_format($sumtarget18); ?></th>
      <th><?php
          $sumtarget19 = 0;
          foreach ($data as $k => $v) {
            $sumtarget19 += $v['Car2_1'] + $v['Car2_2'] + $v['Car2_3']
              + $v['Car2_4'] + $v['Car2_5'] + $v['Car2_6'] + $v['Car2_7']
              + $v['Car2_8'];
          }
          echo number_format($sumtarget19); ?></th>

    </tr>


    <?php

    $type = "";
    ?>
  </table>

</body>

</html>
<?php
