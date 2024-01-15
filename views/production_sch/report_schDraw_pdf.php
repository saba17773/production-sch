<?php
ob_start();
error_reporting(0);
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

    <table>
        <thead>
            <tr>
                <td colspan="2">
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
            <tr style="background: #FFDEAD;">
                <th rowspan="1" width="10%">เตา</th>
                <!-- <th rowspan="2" width="15%">ชื่อพนักงาน</th> -->
                <th rowspan="1" width="10%">Item Number</th>
                <th rowspan="1" width="27%">ขนาดพิมพ์</th>
                <th rowspan="1" width="14%">เบิกใช้</th>
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

<?php

function txtPageFooter()
{
    $txtpage =
        '<table style="border-collapse: collapse; width: 100%;">
			<tr style="border: 0px solid #000000; font-size: 8px;">
			    <td align="left" style="border: 0px solid #000000; font-size: 8px;">
			        Ref .WI-PP-3.10
			    </td>
			    <td align="right" style="border: 0px solid #000000; font-size: 8px;">
			        FM-PP-3.10.4.Issued#4
			    </td>
			</tr>
		</table>';
    return $txtpage;
}

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th', 'A4', 0, '', 3, 3, 3, 13);
$pdf->SetDisplayMode('fullpage');


// $pdf->WriteHTML(
// 	'<style type="text/css">
// 		table {
// 	    border-collapse: collapse;
// 	    width: 100%;
// 	    }

// 	    td, tr, th {
// 	    	border: 0px solid #000000;
// 	        padding: 5px;
// 	        font-size: 8px;
// 	    }

// 	</style>'
// );
//$datafooter = txtPageFooter();
$pdf->SetHtmlFooter($datafooter);

$pdf->WriteHTML($html);

$pdf->Output();
