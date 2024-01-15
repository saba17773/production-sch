<?php ob_start();
error_reporting(0);
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductionSch_bilbuyReport_" . Date("Ymd_His") . ".xls");
?>
<!DOCTYPE html>
<html>
<style type="text/css">
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 9px;
    }

    td,
    tr,
    th {
        border: 1px solid #000000;
        text-align: center;
        padding: 4px;
        font-family: "Angsana New";
    }

    .table {
        border-collapse: collapse;
        width: 40%;
        font-size: 10px;
    }

    .td,
    .tr,
    .th {
        border: 0px solid #000000;
        text-align: left;
        padding: 4px;
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
                <td colspan="2">


                </td>
                <td colspan="5">
                    <h3>
                        รายงานอบยาง หน้ายาง
                    </h3>
                </td>
                <td colspan="5">
                    ประจำวันที่ <?php echo $date; ?>
                </td>
            </tr>

            <tr>
                <th rowspan="2" width="4%">No</th>
                <th rowspan="2" width="6%">ItemID</th>
                <th rowspan="2" width="30%">SIZE/CODE/BRAND</th>
                <th colspan="3" width="20%"><?php echo $shift1; ?></th>
                <th colspan="3" width="20%"><?php echo $shift2; ?></th>
                <th colspan="3" width="24%">TOTAL</th>
            </tr>
            <tr>
                <th width="5%">เบิกใช้</th>
                <th width="5%">เบิกจ่าย</th>
                <th width="5%">หน้าเตา</th>
                <th width="6%">เบิกใช้</th>
                <th width="5%">เบิกจ่าย</th>
                <th width="5%">หน้าเตา</th>
                <th width="5%">เบิกใช้</th>
                <th width="6%">เบิกจ่าย</th>
                <th width="6%">หน้าเตา</th>

            </tr>
        </thead>

        <?php
        foreach ($data as $k => $v) {
            echo "<tr>";
            echo "<td>" . ($k + 1) . "</td>";
            echo "<td>" . $v['ItemID'] . "</td>";
            echo "<td align='left'>" . $v['ItemName'] . "</td>";
            // echo "<td>".$v['Target1'][0]."</td>";
            // echo "<td>".$v['Actual1'][0]."</td>";

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
            if ($v['BillUse1'][0] > 0 || $v['BillUse2'][0] > 0) {
                $BillUsetotal = $v['BillUse1'][0] + $v['BillUse2'][0];
                echo "<td>" . $BillUsetotal . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['BillGive1'][0] > 0 || $v['BillGive2'][0] > 0) {
                $BillGivetotal = $v['BillGive1'][0] + $v['BillGive2'][0];
                echo "<td>" . $BillGivetotal . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['faceBoiler1'][0] > 0 || $v['faceBoiler2'][0] > 0) {
                $faceBoilertotal = $v['faceBoiler1'][0] + $v['faceBoiler2'][0];
                echo "<td>" . $faceBoilertotal . "</td>";
            } else {
                echo "<td></td>";
            }
        }

        ?>
        <tr>
            <td colspan="3">
                <b>TOTAL</b>
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
            <td>
                <?php
                $sumBillUsetotal = $sumBillUse1 + $sumBillUse2;

                echo (int) $sumBillUsetotal === 0 ? '' : number_format($sumBillUsetotal);
                ?>
            </td>
            <td>
                <?php
                $sumBillGivetotal = $sumBillGive1 + $sumBillGive2;

                echo (int) $sumBillGivetotal === 0 ? '' : number_format($sumBillGivetotal);
                ?>
            </td>
            <td>
                <?php
                $sumfaceBoilertotal = $sumfaceBoiler1 + $sumfaceBoiler2;

                echo (int) $sumfaceBoilertotal === 0 ? '' : number_format($sumfaceBoilertotal);
                ?>
            </td>
        </tr>




    </table>
</body>

</html>