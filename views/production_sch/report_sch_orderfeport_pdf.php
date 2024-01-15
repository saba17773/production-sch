<?php ob_start();
error_reporting(0);
?>
<!DOCTYPE html>
<html>

<style type="text/css">
    table {
        border-collapse: collapse;
        width: 120%;
        font-size: 11px;
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
        font-size: 12px;
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
            font-size: 12px;
        }
    </style>
</head>

<body>

    <table>
        <thead>
            <tr>
                <td colspan="4">
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
                <td colspan="11">
                    <h3>
                        รายงาน การผลิตประจำวัน
                    </h3>
                </td>
                <td colspan="5">
                    ประจำวันที่ <?php echo $date . " " . $shift; ?>
                </td>
            </tr>

            <tr>
                <th rowspan="2" width="4%">No</th>
                <th rowspan="2" width="6%">ItemID</th>
                <th rowspan="2" width="25%">Item Name</th>
                <th rowspan="2" width="8%">Color</th>
                <th colspan="2" width="12%">รายงาน การผลิตประจำวัน</th>
                <th colspan="10" width="35%">กรีนไทร์</th>
                <th colspan="2" width="11%">เปรียบเทียบ</th>
                <th colspan="2" width="11%">หน้าเตา</th>
            </tr>
            <tr>
                <th width="6%">สร้างโครง<BR>ผลิตได้(เส้น)</th>
                <th width="6%">อบยาง<BR>ผลิตได้(เส้น)</th>
                <th width="4%">Spare หน้าเตา</th>
                <th width="5%">Stock ในแผนก</th>
                <th width="4%">รับเข้า</th>
                <th width="5%">มีกรีนไทร์<BR>ในแผนก</th>
                <th width="4%">จ่ายออก</th>
                <th width="5%">คงเหลือ<BR>ในแผนก</th>
                <th width="4%">อบยางเบิก</th>
                <th width="4%">คำนวณหน้าเตา</th>
                <th width="4%">Spare หน้าเตา</th>
                <th width="7%">คงเหลือใน<BR>แผนก+หน้าเตา</th>
                <th width="6%">สร้าง/รับเข้า</th>
                <th width="6%">เบิก/จ่าย</th>
                <th width="4%">นับจริง</th>
                <th width="4%">เปรียบเทียบ</th>


            </tr>
        </thead>

        <?php
        foreach ($data as $k => $v) {
            echo "<tr>";
            echo "<td>" . ($k + 1) . "</td>";
            echo "<td>" . $v['ItemId'] . "</td>";
            echo "<td align='left'>" . $v['ItemGTName'] . "</td>";
            echo "<td>" . $v['ColorAll'] . "</td>";
            // echo "<td>".$v['Actual1'][0]."</td>";

            if ($v['Actual'] > 0) {
                $Actual = $v['Actual'];
                echo "<td>" . $Actual . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['BomCheck'] > 0) {
                $BomCheck = $v['BomCheck'];
                echo "<td>" . $BomCheck . "</td>";
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

            if ($v['GreentireInDept'] > 0) {
                $GreentireInDept = $v['GreentireInDept'];
                echo "<td>" . $GreentireInDept . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['CountOut'] > 0) {
                $CountOut = $v['CountOut'];
                echo "<td>" . $CountOut . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['SummaryInDept'] > 0) {
                $SummaryInDept = $v['SummaryInDept'];
                echo "<td>" . $SummaryInDept . "</td>";
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

            if ($v['SpareOfcure2'] > 0) {
                $SpareOfcure2 = $v['SpareOfcure2'];
                echo "<td>" . $SpareOfcure2 . "</td>";
            } else {
                echo "<td></td>";
            }

            if ($v['SummaryCure'] > 0) {
                $SummaryCure = $v['SummaryCure'];
                echo "<td>" . $SummaryCure . "</td>";
            } else {
                echo "<td></td>";
            }

            echo "<td>" . $v['CompareCreateRecve'] . "</td>";

            echo "<td>" . $v['CompareBillBuy'] . "</td>";

            if ($v['CountInOrder'] > 0) {
                $CountInOrder = $v['CountInOrder'];
                echo "<td>" . $CountInOrder . "</td>";
            } else {
                echo "<td></td>";
            }

            echo "<td>" . $v['CompareReal'] . "</td>";
        }

        ?>
        <tr>
            <td colspan="4">
                <b>TOTAL</b>
            </td>
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
                $sumBomCheck = 0;
                foreach ($data as $v) {
                    $sumBomCheck += $v['BomCheck'];
                }
                // echo $sumtarget_a;
                echo (int) $sumBomCheck === 0 ? '' : number_format($sumBomCheck);
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

            <td>
                <?php
                $sumGreentireInDept = 0;
                foreach ($data as $v) {
                    $sumGreentireInDept += $v['GreentireInDept'];
                }
                // echo $sumtarget_a;
                echo (int) $sumGreentireInDept === 0 ? '' : number_format($sumGreentireInDept);
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

            <td>
                <?php
                $sumSummaryInDept = 0;
                foreach ($data as $v) {
                    $sumSummaryInDept += $v['SummaryInDept'];
                }
                // echo $sumtarget_a;
                echo (int) $sumSummaryInDept === 0 ? '' : number_format($sumSummaryInDept);
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

            <td>
                <?php
                $sumSpareOfcure2 = 0;
                foreach ($data as $v) {
                    $sumSpareOfcure2 += $v['SpareOfcure2'];
                }
                // echo $sumtarget_a;
                echo (int) $sumSpareOfcure2 === 0 ? '' : number_format($sumSpareOfcure2);
                ?>
            </td>

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
            <td></td>
            <td></td>
            <td>
                <?php
                $sumCountInOrder = 0;
                foreach ($data as $v) {
                    $sumCountInOrder += $v['CountInOrder'];
                }
                // echo $sumtarget_a;
                echo (int) $sumCountInOrder === 0 ? '' : number_format($sumCountInOrder);
                ?>
            </td>
            <td></td>>






        </tr>




    </table>

</body>

</html>

<?php

// function txtPageFooter()
// {
//     $txtpage =
//         '<table style="border-collapse: collapse; width: 100%;">
// 			<tr style="border: 0px solid #000000; font-size: 8px;">
// 			    <td align="left" style="border: 0px solid #000000; font-size: 8px; width: 50%;">
// 			        Ref.WI-PP-3.10
// 			    </td>
// 			    <td align="right" style="border: 0px solid #000000; font-size: 8px; width: 50%;">
// 			        FM.PP.3.10.5,Issued#3
// 			    </td>
// 			</tr>
// 		</table>';
//     return $txtpage;
// }

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4-L', 0, '', 3, 3, 3, 13);
$pdf->SetDisplayMode('fullpage');

//$datafooter = txtPageFooter();
$pdf->SetHTMLFooter($datafooter);

$pdf->WriteHTML($html);

$pdf->Output();
