<?php ob_start();
error_reporting(0);
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductionSch_OrderReport_" . Date("Ymd_His") . ".xls");
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
            font-size: 5px;
        }
    </style>
</head>

<body>
    <BR>
    <table border="1">
        <thead>
            <tr>
                <td colspan="4">

                </td>
                <td colspan="10">
                    <h3>
                        <center> Order การผลิต </center>
                    </h3>
                </td>
                <td colspan="5">
                    ประจำวันที่ <?php echo $date . " " . $shift; ?>
                </td>
            </tr>

            <tr>
                <th rowspan="2" width="4%">No</th>
                <th rowspan="2" width="6%">ItemID</th>
                <th rowspan="2" width="15%">Item Name</th>
                <th rowspan="2" width="8%">Color</th>
                <th colspan="2" width="12%">Order การผลิต</th>
                <th rowspan="2" width="4%">อบยางผลิตได้(เส้น)</th>
                <th colspan="12" width="50%">กรีนไทร์</th>


            </tr>
            <tr>
                <th width="6%">BL</th>
                <th width="6%">สร้างโครง<BR>ผลิตได้</th>
                <th width="4%">WIP<BR>หน้าเตา</th>
                <th width="4%">WIP<BR>กรีนไทร์</th>
                <th width="4%">รวม<BR>รับเข้า</th>
                <th width="4%">เพิ่ม<BR>รับเข้า</th>
                <th width="6%">รวมกรีนไทร์<BR>ทั้งหมด</th>
                <th width="4%">รวมจ่ายออก</th>
                <th width="4%">เพิ่มจ่ายออก</th>
                <th width="4%">คงเหลือ<BR>ในแผนก</th>
                <th width="4%">อบยางเบิก</th>
                <th width="4%">คำนวณหน้าเตา</th>
                <th width="4%">หน้าเตา</th>
                <th width="5%">คงเหลือใน<BR>แผนก+หน้าเตา</th>


            </tr>
        </thead>
        <?php
        foreach ($data as $k => $v) {
            echo "<tr>";
            echo "<td>" . ($k + 1) . "</td>";
            echo "<td>" . $v['ItemId'] . "</td>";
            echo "<td align='left'>" . $v['ItemGTName'] . "</td>";
            echo "<td>" . $v['ColorAll'] . "</td>";
            echo "<td></td>";


            if ($v['Actual'] > 0) {
                $Actual = $v['Actual'];
                echo "<td>" . $Actual . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['Bom'] > 0) {
                $Bom = $v['Bom'];
                echo "<td>" . $Bom . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['SpareOfcure'] > 0) {
                $SpareOfcure = $v['SpareOfcure'];
                echo "<td>" . $SpareOfcure . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['StockInplan'] > 0) {
                $StockInplan = $v['StockInplan'];
                echo "<td>" . $StockInplan . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['CountIn'] > 0) {
                $CountIn = $v['CountIn'];
                echo "<td>" . $CountIn . "</td>";
            } else {
                echo "<td></td>";
            }

            echo "<td></td>";

            if ($v['TotalGreentire'] > 0) {
                $TotalGreentire = $v['TotalGreentire'];
                echo "<td>" . $TotalGreentire . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['CountOut'] > 0) {
                $CountOut = $v['CountOut'];
                echo "<td>" . $CountOut . "</td>";
            } else {
                echo "<td></td>";
            }
            echo "<td></td>";

            if ($v['AmountInplan'] > 0) {
                $AmountInplan = $v['AmountInplan'];
                echo "<td>" . $AmountInplan . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['CountCure'] > 0) {
                $CountCure = $v['CountCure'];
                echo "<td>" . $CountCure . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['CalCure'] > 0) {
                $CalCure = $v['CalCure'];
                echo "<td>" . $CalCure . "</td>";
            } else {
                echo "<td></td>";
            }

            echo "<td></td>";
            if ($v['SummaryCure'] > 0) {
                $SummaryCure = $v['SummaryCure'];
                echo "<td>" . $SummaryCure . "</td>";
            } else {
                echo "<td></td>";
            }


            echo "</tr>";
        }


        ?>
        <tr>
            <td colspan="4">
                <b>TOTAL</b>
            </td>
            <td></td>
            <td>
                <?php
                $sumActual = 0;
                foreach ($data as $v) {
                    $sumActual += $v['Actual'];
                }
                // echo $sumtarget_a;
                echo (int) $sumActual === 0 ? '' : number_format($sumActual);
                ?>
            </td>

            <td>
                <?php
                $sumBom = 0;
                foreach ($data as $v) {
                    $sumBom += $v['Bom'];
                }
                // echo $sumtarget_a;
                echo (int) $sumBom === 0 ? '' : number_format($sumBom);
                ?>
            </td>
            <td>
                <?php
                $sumSpareOfcure = 0;
                foreach ($data as $v) {
                    $sumSpareOfcure += $v['SpareOfcure'];
                }
                // echo $sumtarget_a;
                echo (int) $sumSpareOfcure === 0 ? '' : number_format($sumSpareOfcure);
                ?>
            </td>
            <td>
                <?php
                $sumStockInplan = 0;
                foreach ($data as $v) {
                    $sumStockInplan += $v['StockInplan'];
                }
                // echo $sumtarget_a;
                echo (int) $sumStockInplan === 0 ? '' : number_format($sumStockInplan);
                ?>
            </td>

            <td>
                <?php
                $sumCountIn = 0;
                foreach ($data as $v) {
                    $sumCountIn += $v['CountIn'];
                }
                // echo $sumtarget_a;
                echo (int) $sumCountIn === 0 ? '' : number_format($sumCountIn);
                ?>
            </td>

            <td></td>
            <td>
                <?php
                $sumTotalGreentire = 0;
                foreach ($data as $v) {
                    $sumTotalGreentire += $v['TotalGreentire'];
                }
                // echo $sumtarget_a;
                echo (int) $sumTotalGreentire === 0 ? '' : number_format($sumTotalGreentire);
                ?>
            </td>

            <td>
                <?php
                $sumCountOut = 0;
                foreach ($data as $v) {
                    $sumCountOut += $v['CountOut'];
                }
                // echo $sumtarget_a;
                echo (int) $sumCountOut === 0 ? '' : number_format($sumCountOut);
                ?>
            </td>

            <td></td>
            <td>
                <?php
                $sumAmountInplan = 0;
                foreach ($data as $v) {
                    $sumAmountInplan += $v['AmountInplan'];
                }
                // echo $sumtarget_a;
                echo (int) $sumAmountInplan === 0 ? '' : number_format($sumAmountInplan);
                ?>
            </td>

            <td>
                <?php
                $sumCountCure = 0;
                foreach ($data as $v) {
                    $sumCountCure += $v['CountCure'];
                }
                // echo $sumtarget_a;
                echo (int) $sumCountCure === 0 ? '' : number_format($sumCountCure);
                ?>
            </td>

            <td>
                <?php
                $sumCalCure = 0;
                foreach ($data as $v) {
                    $sumCalCure += $v['CalCure'];
                }
                // echo $sumtarget_a;
                echo (int) $sumCalCure === 0 ? '' : number_format($sumCalCure);
                ?>
            </td>

            <td></td>

            <td>
                <?php
                $sumSummaryCure = 0;
                foreach ($data as $v) {
                    $sumSummaryCure += $v['SummaryCure'];
                }
                // echo $sumtarget_a;
                echo (int) $sumSummaryCure === 0 ? '' : number_format($sumSummaryCure);
                ?>
            </td>







        </tr>




    </table>
</body>

</html>