<?php ob_start(); ?>
<?php
header("Content-type: application/vnd.ms-excel");
// header('Content-type: application/csv'); //*** CSV ***//
header("Content-Disposition: attachment; filename=ProductionSch_Report" . Date("Ymd_His") . ".xls");
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
                <td colspan="2">

                </td>
                <td colspan="4">
                    <h2>
                        ใบรายงานการเบิกใช้ เบิกให้ หน้าเตา
                    </h2>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center">
                    <?php echo $date; ?>
                    กะ : <?php echo $shift; ?>
                </td>
            </tr>
            <tr>
                <th rowspan="1" width="10%">เตา</th>
                <!-- <th rowspan="2" width="15%">ชื่อพนักงาน</th> -->
                <th rowspan="1" width="10%">Item Number</th>
                <th rowspan="1" width="27%">ขนาดพิมพ์</th>
                <th rowspan="1" width="7%">เบิกใช้</th>
                <th rowspan="1" width="14%">เบิกให้</th>
                <!-- <th rowspan="2" width="7%">น้ำหนัก</th>  -->
                <th rowspan="1" width="14%">หน้าเตา</th>
            </tr>

        </thead>
        <?php
        $boiler = "";
        foreach ($data as $key => $value) {
            // echo $value['Boiler'].$value['ItemID'];
            // echo "<br>";

            echo "<tr>";
            // echo "<td>" . $value["Boiler"] . "\n" . $value['BoilerName'] . "</td>";
            if ($boiler != $value["Boiler"]) {
                echo "<td rowspan=" . $value['rowspan'] . ">" . $value["Boiler"] . "\n" . $value['BoilerName'] . "</td>";
                // echo "<td rowspan='6'>" . $value["Boiler"] . "\n" . $value['BoilerName'] . "</td>";
            }
            if ($value["ItemID"] == "") {
                echo "<td>&nbsp;</td>";
            } else {
                echo "<td>" . $value["ItemID"] . "</td>";
            }
            echo "<td align='left'>" . $value["ItemName"] . "</td>";
            echo "<td align='left'>" . $value["BillUse"] . "</td>";
            echo "<td align='left'>" . $value["BillGive"] . "</td>";
            echo "<td align='left'>" . $value["faceBoiler"] . "</td>";



            echo "</tr>";
            $boiler = $value["Boiler"];
        }
        ?>
        <tr>
            <td colspan="3"> TOTAL</td>
            <td>
                <?php
                $sumBillUse = 0;
                foreach ($data as $sumBillUsedata) {
                    $sumBillUse += $sumBillUsedata["BillUse"];
                }
                echo number_format($sumBillUse);
                ?>
            </td>
            <td>
                <?php
                $sumBillGive = 0;
                foreach ($data as $BillGivedata) {
                    $sumBillGive += $BillGivedata["BillGive"];
                }
                echo (int)$sumBillGive === 0 ? '' : number_format($sumBillGive);
                ?>
            </td>
            <td>
                <?php
                $sumfaceBoiler = 0;
                foreach ($data as $faceBoilerdata) {
                    $sumfaceBoiler += $faceBoilerdata["faceBoiler"];
                }
                echo (int)$sumfaceBoiler === 0 ? '' : number_format($sumfaceBoiler);
                ?>
            </td>

        </tr>



    </table>
</body>

</html>