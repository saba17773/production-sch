<?php ob_start(); ?>
<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductionSch_ReportBIAS" . Date("Ymd_His") . ".xls");
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
        width: 100%;
        font-size: 9px;
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
                <td colspan="3">


                </td>
                <td colspan="13">
                    <h2>
                        รายงานแผนการผลิตรายวันของแผนกอบยาง BIAS<BR>
                        <?php echo $date; ?>
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="10" align="center">

                    กะ : C (08.00-20.00 น.)
                </td>
                <td colspan="6" align="center">
                    กะ : D (20.00-08.00 น.)
                </td>
            </tr>
            <tr style="background: #FFDEAD;">
                <th rowspan="2" width="5%">ลำดับ</th>
                <th rowspan="2" width="5%">เตา</th>
                <th rowspan="2" width="5%">จำนวนเตา</th>
                <th rowspan="2" width="5%">ลำดับเตา</th>
                <!-- <th rowspan="2" width="15%">ชื่อพนักงาน</th> -->
                <th rowspan="2" width="6%">Item Number</th>
                <th rowspan="2" width="15%">ขนาดพิมพ์</th>
                <!-- <th rowspan="2" width="7%">เวลาอบ</th> -->
                <th colspan="2" width="10%">จำนวนการอบยาง</th>
                <!-- <th rowspan="2" width="7%">น้ำหนัก</th>  -->
                <th rowspan="2" colspan="2">หมายเหตุ</th>
                <th rowspan="2" width="6%">Item Number</th>
                <th rowspan="2" width="15%">ขนาดพิมพ์</th>
                <th colspan="2" width="10%">จำนวนการอบยาง</th>
                <th rowspan="2" colspan="2">หมายเหตุ</th>
            </tr>
            <tr>
                <th style="background: #FFFACD;" width="7%">เป้า</th>
                <th style="background: #FFDEAD;" width="7%">อบได้</th>
                <th style="background: #FFFACD;" width="7%">เป้า</th>
                <th style="background: #FFDEAD;" width="7%">อบได้</th>
            </tr>
        </thead>

        <?php
        $boiler = "";
        $BoilerName = "";
        $rowspantop = "";
        $i = 1;
        $j = 0;
        $k = 0;


        foreach ($data as $key => $value) {
            // echo $value['Boiler'].$value['ItemID'];
            // echo "<br>";

            echo "<tr>";
            if ($BoilerName != $value["BoilerName"]) {
                echo "<td rowspan=" . $value['rowspantop'] . ">" . $i . "</td>";

                //echo "<td ></td>";
                $i++;
            }
            if ($boiler != $value["Boiler"]) {
                if ($BoilerName != $value["BoilerName"]) {
                    $j = 1;
                } else {
                    $j++;
                }

                echo "<td rowspan=" . $value['rowspan'] . ">" . $value["Boiler"] . "\n" . $value['BoilerName'] . "</td>";
            }

            if ($BoilerName != $value["BoilerName"]) {

                echo "<td rowspan=" . $value['rowspantop'] . ">" . $value["TotalBoiler"] . "</td>";
            }
            if ($boiler != $value["Boiler"]) {
                echo "<td rowspan=" . $value['rowspan'] . ">" . $j . "</td>";
            }
            if ($value["ItemID"] == "") {
                echo "<td>&nbsp;</td>";
            } else {
                echo "<td>" . $value["ItemID"] . "</td>";
            }

            echo "<td align='left'>" . $value["ItemName"] . "</td>";
            echo "<td style='background: #FFFACD;'>" . $value["Target"] . "</td>";
            echo "<td>" . $value["Actual"] . "</td>";
            echo "<td align='left' colspan='2'>" . $value["Remark"] . "</td>";

            if ($value["ItemID_D"] == "") {
                echo "<td>&nbsp;</td>";
            } else {
                echo "<td>" . $value["ItemID_D"] . "</td>";
            }
            echo "<td align='left'>" . $value["ItemName_D"] . "</td>";
            echo "<td style='background: #FFFACD;'>" . $value["Target_D"] . "</td>";
            echo "<td>" . $value["Actual_D"] . "</td>";
            echo "<td align='left' colspan='2'>" . $value["Remark_D"] . "</td>";
            echo "</tr>";
            $boiler = $value["Boiler"];
            $rowspantop = $value["rowspantop"];
            $BoilerName = $value["BoilerName"];
        }
        ?>
        <tr>
            <td colspan="6" align="center">
                Total
            </td>
            <td>
                <?php
                $sumtarget = 0;
                foreach ($data as $target) {
                    $sumtarget += $target["Target"];
                }
                echo $sumtarget;
                ?>
            </td>
            <!-- <td>
				<?php
                $sumactual1 = 0;
                foreach ($data as $actual) {
                    $sumactual1 += $actual["Actual1"];
                }
                echo (int)$sumactual1 === 0 ? '' : $sumactual1;
                ?>
			</td>
			<td>
				<?php
                $sumactual2 = 0;
                foreach ($data as $actual) {
                    $sumactual2 += $actual["Actual2"];
                }
                echo (int)$sumactual2 === 0 ? '' : $sumactual2;
                ?>
			</td> -->
            <td>
                <?php
                $sumactual = 0;
                foreach ($data as $actual) {
                    $sumactual += $actual["Actual"];
                }
                echo (int)$sumactual === 0 ? '' : $sumactual;
                ?>
            </td>
            <td colspan="2">

            </td>
            <td></td>
            <td></td>
            <td>
                <?php
                $sumtarget4 = 0;
                foreach ($data as $target_D) {
                    $sumtarget4 += $target_D["Target_D"];
                }
                echo $sumtarget4;
                ?>
            </td>
            <!-- <td>
				<?php
                $sumactual5 = 0;
                foreach ($data as $actual_D) {
                    $sumactual5 += $actual_D["Actual1_D"];
                }
                echo (int)$sumactual5 === 0 ? '' : $sumactual5;
                ?>
			</td>
			<td>
				<?php
                $sumactual6 = 0;
                foreach ($data as $actual_D) {
                    $sumactual6 += $actual_D["Actual2_D"];
                }
                echo (int)$sumactual6 === 0 ? '' : $sumactual6;
                ?>
			</td> -->
            <td>
                <?php
                $sumactual_7 = 0;
                foreach ($data as $actual_D) {
                    $sumactual_7 += $actual_D["Actual_D"];
                }
                echo (int)$sumactual_7 === 0 ? '' : $sumactual_7;
                ?>
            </td>
            <td colspan="2">

            </td>
        </tr>
        <tr>
            <td align="center" text-rotate="90" align="center" colspan="2">
                การผลิต
            </td>
            <td colspan="5">
                <table class="table">
                    <tr class="tr">
                        <td class="td">
                            จำนวนเตาอบยางที่เปิดอบทั้งหมด
                        </td>
                        <td class="td">
                            ...............<?php echo $countBoilerAll; ?>................. เตา
                        </td>
                    </tr>
                    <tr class="tr">
                        <td class="td" width="45%">
                            จำนวนเตาอบยางที่เปิดอบ
                        </td>
                        <td class="td">
                            ...............<?php echo $countBoiler; ?>................. เตา
                        </td>
                    </tr>
                    <tr class="tr">
                        <td class="td">
                            จำนวนพิมพ์ที่อบทั้งหมด
                        </td>
                        <td class="td">
                            ...............<?php echo $countMoldAll; ?>................. พิมพ์
                        </td>
                    </tr>
                    <tr class="tr">
                        <td class="td">
                            จำนวนพิมพ์ที่อบ
                        </td>
                        <td class="td">
                            ...............<?php echo $countMold; ?>................. พิมพ์
                        </td>
                    </tr>


                </table>
            </td>
            <td colspan="9">
                <table class="table">
                    <tr class="tr">
                        <td class="td" width="48%">
                            เป้าหมายการผลิต
                        </td>
                        <td class="td">
                            ...............<?php echo ($sumtarget + $sumtarget4); ?>................. เส้น
                        </td>
                    </tr>
                    <tr class="tr">
                        <td class="td">
                            ผลิตได้
                        </td>
                        <td class="td">
                            ......<?php echo ($sumactual + $sumactual_7); ?>.....เส้น......<?php $sumall = (($sumactual + $sumactual_7) / ($sumtarget + $sumtarget4)) * 100;
                                                                                            echo number_format((float)$sumall, 2, '.', '');
                                                                                            ?>..... %
                        </td>
                    </tr>

                </table>
            </td>
        </tr>









    </table>
</body>

</html>