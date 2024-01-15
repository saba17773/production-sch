<?php

header("Content-type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=target_greentire_Cure" . Date("Ymd_His") . ".xls");

function isEmpty($data)
{
    if ($data === null || $data === "") {
        return 0;
    } else {
        return $data;
    }
}

function serializeColor($colorAll)
{
    if ($colorAll !== null || $colorAll !== "") {
        return $colorAll . "/";
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Report Target Greentire</title>
</head>

<body>
    <table border="1">
        <thead>
            <tr>

                <th colspan="12">
                    <h1>ใบรายงาน การเบิกใช้ เบิกให้ หน้าเตา (Target Greentire)</h1>
                </th>
            </tr>
            <tr>
                <th colspan="6" class="text-center" style="padding: 10px;">
                    <?php echo getThaiDate($date); ?>
                </th>
                <th colspan="3" style="width: 110px;">BOM <?php echo $shift1; ?></th>
                <th colspan="3" style="width: 110px;">BOM <?php echo $shift2; ?></th>
                <!-- <th colspan="2">รวม</th> -->
            </tr>
            <tr>
                <th rowspan="2" style="width: 40px;">No.</th>
                <th rowspan="2" style="width: 80px;">Item Id</th>
                <th rowspan="2" style="width: 250px;">Size</th>

                <th rowspan="2" style="width: 25px;">T/T<BR> T/L</th>
                <th rowspan="2" style="width: 100px;">Color</th>
                <th rowspan="2" style="width: 80px;">Weight</th>
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