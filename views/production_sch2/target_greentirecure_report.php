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
                    <h1>ใบรายงาน การเบิกใช้ เบิกให้ หน้าเตา (Target Greentire)</h1>
                </th>
            </tr>
            <tr>
                <th colspan="6" class="text-center" style="padding: 10px;">
                    <?php echo getThaiDate($date); ?>
                </th>
                <th colspan="3">BOM <?php echo $shift1; ?></th>
                <th colspan="3">BOM <?php echo $shift2; ?></th>
                <!-- <th colspan="2">รวม</th> -->
            </tr>
            <tr>
                <th rowspan="2" style="width: 25px;">No.</th>
                <th rowspan="2" style="width: 25px;">Item Id</th>
                <th rowspan="2" style="width: 250px;">Size</th>

                <th rowspan="2" style="width: 25px;">T/T<BR> T/L</th>
                <th rowspan="2" style="width: 80px;">Color</th>
                <th rowspan="2" style="width: 25px;">Weight</th>
                <th colspan="3" style="width: 80px;">รวม BRAND</th>
                <th colspan="3" style="width: 80px;">รวม BRAND</th>

            </tr>
            <tr>
                <th>เบิกให้</th>
                <th>เบิกใช้</th>
                <th>หน้าเตา</th>
                <th>เบิกให้</th>
                <th>เบิกใช้</th>
                <th>หน้าเตา</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($data as $k => $v) {
                echo "<tr>";
                echo "<td>" . ($k + 1) . "</td>";
                echo "<td>" . $v['ItemID'] . "</td>";
                echo "<td align='left'>" . $v['ItemName'] . "</td>";
                echo "<td>" . $v['TT'] . "</td>";
                echo "<td>" . $v['ColorAll'] . "</td>";
                echo "<td>" . $v['Weight'] . "</td>";

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
                // if ($v['BillUse1'][0] > 0 || $v['BillUse2'][0] > 0) {
                //     $BillUsetotal = $v['BillUse1'][0] + $v['BillUse2'][0];
                //     echo "<td>" . $BillUsetotal . "</td>";
                // } else {
                //     echo "<td></td>";
                // }

                // if ($v['BillGive1'][0] > 0 || $v['BillGive2'][0] > 0) {
                //     $BillGivetotal = $v['BillGive1'][0] + $v['BillGive2'][0];
                //     echo "<td>" . $BillGivetotal . "</td>";
                // } else {
                //     echo "<td></td>";
                // }

                // if ($v['faceBoiler1'][0] > 0 || $v['faceBoiler2'][0] > 0) {
                //     $faceBoilertotal = $v['faceBoiler1'][0] + $v['faceBoiler2'][0];
                //     echo "<td>" . $faceBoilertotal . "</td>";
                // } else {
                //     echo "<td></td>";
                // }
            }

            ?>

            <tr>
                <td colspan="6">
                    <b>
                        <center>TOTAL</center>
                    </b>
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
