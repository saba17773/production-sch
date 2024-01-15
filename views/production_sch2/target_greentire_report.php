<?php

// header("Content-type: application/vnd.ms-excel; charset=utf-8");
// header("Content-Disposition: attachment; filename=target_greentire_" . Date("Ymd_His") . ".xls");

function isEmpty($data)
{
  if ($data === null || $data === "") {
    return 0;
  } else if ($data === ".00") {
    return "";
  } else {
    return $data;
  }
}

function serializeColor($color)
{
  if ($color !== null || $color !== "") {
    return $color . "/";
  } else {
    return "";
  }
}

function getThaiDate($date)
{
  $d = date("d", strtotime($date));
  $m = date(
    "m",
    strtotime($date)
  );
  $y = date("Y", strtotime($date));
  $month = [
    "มกราคม",
    "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม",
    "กันยายน", "ตุลาคม", "พฤษจิกายน", "ธันวาคม"
  ];
  return "วันที่ " . (int) $d . " " .
    $month[$m - 1] . " พ.ศ. " . (int) ($y + 543);
}

ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Report Target Greentire</title>
  <style>
    body {
      font-family: "Cordia New";
      font-size: 8px;
    }

    table tr td,
    table tr th {
      padding: 3px;
    }
  </style>
</head>

<body>
  <table id="gridTargetGreentire" border="1" cellspacing="0" style="width:100%;">
    <thead>
      <tr>
        <th colspan="3" style="padding: 10px;"><img src="<?php echo root; ?>/assets/images/DSL_2.png" width="100" alt=""></th>
        <th colspan="9">
          <h1>เป้าหมายการผลิตอบยาง DSL</h1>
        </th>
      </tr>
      <tr>
        <th colspan="6" class="text-center" style="padding: 10px;">
          <?php echo getThaiDate($date); ?>
        </th>
        <th colspan="2">BOM <?php echo $shift1; ?></th>
        <th colspan="2">BOM <?php echo $shift2; ?></th>
        <th colspan="2">รวม</th>
      </tr>
      <tr>
        <th rowspan="2" style="width: 25px;">No.</th>
        <th rowspan="2" style="width: 25px;">Item Id</th>
        <th rowspan="2" style="width: 250px;">Size</th>
        <!-- <th rowspan="2" style="width: 25px;">PR</th>
        <th rowspan="2" style="width: 25px;">Code</th>
        <th rowspan="2" style="width: 25px;">Pattern</th> -->
        <th rowspan="2" style="width: 25px;">T/T T/L</th>
        <th rowspan="2" style="width: 80px;">Color</th>
        <th rowspan="2" style="width: 25px;">Weight</th>
        <th colspan="2" style="width: 80px;">รวม BRAND</th>
        <th colspan="2" style="width: 80px;">รวม BRAND</th>
        <th colspan="2">C+D</th>
      </tr>
      <tr>
        <th>เป้าผลิต</th>
        <th>ผลิตได้</th>
        <th>เป้าผลิต</th>
        <th>ผลิตได้</th>
        <th style="width: 70px;">น้ำหนักเป้าหมาย</th>
        <th style="width: 70px;">น้ำหนักผลิต</th>
      </tr>
    </thead>
    <tbody>
      <?php

      $totalBomCPlan = 0;
      $totalBomCActual = 0;
      $totalBomDPlan = 0;
      $totalBomDActual = 0;
      $totalWeightPlan = 0;
      $totalWeightActual = 0;

      foreach ($data as $value) {
        $totalBomCPlan += isEmpty($value["BomCPlan"]);
        $totalBomCActual += isEmpty($value["BomCActual"]);
        $totalBomDPlan += isEmpty($value["BomDPlan"]);
        $totalBomDActual += isEmpty($value["BomDActual"]);
        $totalWeightPlan += isEmpty($value["WeightPlan"]);
        $totalWeightActual += isEmpty($value["WeightActual"]);
      ?>
        <tr>
          <td><?php echo $value["Id"]; ?></td>
          <td><?php echo $value["ItemId"]; ?></td>
          <td><?php echo $value["ItemGTName"]; ?></td>
          <td><?php echo $value["TT"]; ?></td>
          <td><?php echo trim(serializeColor($value["ColorAll"])); ?></td>
          <td><?php echo (int) $value["Weight"] === 0 ? '' : number_format(number_format($value["Weight"], 3, '.', ''), 3); ?></td>
          <td><?php echo isEmpty($value["BomCPlan"]) === 0 ? "" : isEmpty($value["BomCPlan"]); ?></td>
          <td><?php echo isEmpty($value["BomCActual"]) === 0 ? "" : isEmpty($value["BomCActual"]); ?></td>
          <td><?php echo isEmpty($value["BomDPlan"]) === 0 ? "" : isEmpty($value["BomDPlan"]); ?></td>
          <td><?php echo isEmpty($value["BomDActual"]) === 0 ? "" : isEmpty($value["BomDActual"]); ?></td>
          <td><?php echo (int) $value["WeightPlan"] === 0 ? '' : number_format(number_format($value["WeightPlan"], 3, '.', ''), 3); ?></td>
          <td><?php echo (int) $value["WeightActual"] === 0 ? '' : number_format(number_format($value["WeightActual"], 3, '.', ''), 3); ?></td>
        </tr>
      <?php } ?>
      <tr>
        <td colspan="6" rowspan="5" style="text-align: center;">Total</td>
        <td><?php echo number_format($totalBomCPlan); ?></td>
        <td><?php echo number_format($totalBomCActual); ?></td>
        <td><?php echo number_format($totalBomDPlan); ?></td>
        <td><?php echo number_format($totalBomDActual); ?></td>
        <td><?php echo number_format(number_format($totalWeightPlan, 3, '.', ''), 2); ?></td>
        <td><?php echo number_format(number_format($totalWeightActual, 3, '.', ''), 2); ?></td>
      </tr>
      <tr>
        <td colspan="2">เป้าผลิต</td>
        <td colspan="2"><?php echo number_format($totalBomCPlan + $totalBomDPlan); ?></td>
        <td>เส้น</td>
        <td rowspan="2"><?php echo number_format((($totalBomCActual + $totalBomDActual) * 100) / ($totalBomCPlan + $totalBomDPlan), 2, '.', '');
                        ?>%</td>
      </tr>
      <tr>
        <td colspan="2">ผลิตได้</td>
        <td colspan="2"><?php echo number_format($totalBomCActual + $totalBomDActual); ?></td>
        <td>เส้น</td>
      </tr>
      <tr>
        <td colspan="2">น้ำหนักเป้า</td>
        <td colspan="2"><?php echo number_format($totalWeightPlan / 1000, 2, '.', '');
                        ?></td>
        <td>Tone</td>
        <td rowspan="2"><?php echo number_format(($totalWeightActual * 100) / $totalWeightPlan, 2, '.', '');
                        ?>%</td>
      </tr>
      <tr>
        <td colspan="2">น้ำหนักผลิต</td>
        <td colspan="2">
          <?php echo number_format($totalWeightActual / 1000, 2, '.', ''); ?>
        </td>
        <td>Tone</td>
      </tr>
    </tbody>
  </table>
</body>

</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf = new mPDF(
  'th', // mode
  'A4', // format,
  0, // font size,
  '', // default font
  3, // margin left
  3, // margin right
  3, // margin top
  1, // margin bottom
  0, // margin header ?
  0, // margin footer ?
  'P' // orientation
);
$mpdf->WriteHTML($html);
$mpdf->Output();
