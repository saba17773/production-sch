<?php 
    ob_start(); 
    // var_dump($data);exit;
    use App\Components\Utils as U;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Curing AX</title>
</head>
<body style="font-size: 0.7em;">
    <table width="100%" border="1" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="text-align: center;">
                    <img src="<?php echo root; ?>/assets/images/str.jpg" width="150" alt="">
                </th>
                <th style="text-align: center; padding: 30px;">
                    <div>SIAMTRUCK RADIAL CO. LTD.</div>
                    <div>Curing AX Report</div>
                </th>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px; border-bottom: 0px;">
                    <table width="100%" border="0">
                        <tr>
                            <td>
                                Date : <?php echo $date_curing; ?>
                            </td>
                            <td>
                                 Week : <?php echo U::getWeek($date_curing); ?>
                            </td>
                            <td>
                                  Shift : <?php if($shift=="day"){ echo "กลางวัน"; }else{ echo "กลางคืน";} ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </thead>
    </table>
    <table width="100%" border="1" cellspacing="0" style="text-align: center;">
        <thead>
        <tr style="background: #eeeeee;">
            <th>
                Curing Code
            </th>
            <th>
                Item No
            </th>
            <th>
                <?php if ($shift === 'day'){ ?>
                    08.00 - 10.00
                <?php } else { ?>
                    20.00 - 22.00
                <?php } ?>
            </th>
            <th>
                <?php if ($shift === 'day'){ ?>
                    10.00 - 12.00
                <?php } else { ?>
                    22.00 - 24.00
                <?php } ?>
            </th>
            <th>
                <?php if ($shift === 'day'){ ?>
                    12.00 - 14.00
                <?php } else { ?>
                    24.00 - 02.00
                <?php } ?>
            </th>
            <th>
                <?php if ($shift === 'day'){ ?>
                    14.00 - 16.00
                <?php } else { ?>
                    02.00 - 04.00
                <?php } ?>
            </th>
            <th>
                <?php if ($shift === 'day'){ ?>
                    16.00 - 18.00
                <?php } else { ?>
                    04.00 - 06.00
                <?php } ?>
            </th>
            <th>
                <?php if ($shift === 'day'){ ?>
                    18.00 - 20.00
                <?php } else { ?>
                    06.00 - 08.00
                <?php } ?>
            </th>
            <th>
                Total
            </th>
        </tr>
        </thead>
       <!--  <tfoot>
            <tr>
              <td>Ref.WI-MP-1.15</td>
              <td>FM-MP-1.15.1,Issue #3</td>
            </tr>
          </tfoot> -->
        <?php 
        $grandTotal = 0;
        $result = array();
        foreach ($data as $d) {
          $id = $d->CuringCode;
          if (!isset($result[$id])) {
             $result[$id]["Q1"] = $d->Q1;
             $result[$id]["Q2"] = $d->Q2;
             $result[$id]["Q3"] = $d->Q3;
             $result[$id]["Q4"] = $d->Q4;
             $result[$id]["Q5"] = $d->Q5;
             $result[$id]["Q6"] = $d->Q6;
             $result[$id]["code"] = $d->CuringCode;
             $result[$id]["item"] = $d->ItemNo;
          } else if (isset($result[$id])) {
             $result[$id]["Q1"] += $d->Q1;
             $result[$id]["Q2"] += $d->Q2;
             $result[$id]["Q3"] += $d->Q3;
             $result[$id]["Q4"] += $d->Q4;
             $result[$id]["Q5"] += $d->Q5;
             $result[$id]["Q6"] += $d->Q6;
          } else {
             $result[$id] = [
                "code" => $d->CuringCode,
                "item" => $d->ItemNo,
                "Q1" => 0,
                "Q2" => 0,
                "Q3" => 0,
                "Q4" => 0,
                "Q5" => 0,
                "Q6" => 0
             ];
          }
        }
        $total_q1 = 0;
        $total_q2 = 0;
        $total_q3 = 0;
        $total_q4 = 0;
        $total_q5 = 0;
        $total_q6 = 0;

        foreach ($result as $key => $value) { ?>
        <tr>
            <td style="padding: 3px;"><?php echo $key; ?></td>
            <td style="padding: 3px;"><?php if ($value["item"]!==0) echo $value["item"]; ?></td>
            <td style="padding: 3px;"><?php if ($value["Q1"]!==0) echo $value["Q1"]; ?></td>
            <?php $total_q1+=$value['Q1']; ?>
            <td style="padding: 3px;"><?php if ($value["Q2"]!==0) echo $value["Q2"]; ?></td>
            <?php $total_q2+=$value['Q2']; ?>
            <td style="padding: 3px;"><?php if ($value["Q3"]!==0) echo $value["Q3"]; ?></td>
            <?php $total_q3+=$value['Q3']; ?>
            <td style="padding: 3px;"><?php if ($value["Q4"]!==0) echo $value["Q4"]; ?></td>
            <?php $total_q4+=$value['Q4']; ?>
            <td style="padding: 3px;"><?php if ($value["Q5"]!==0) echo $value["Q5"]; ?></td>
            <?php $total_q5+=$value['Q5']; ?>
            <td style="padding: 3px;"><?php if ($value["Q6"]!==0) echo $value["Q6"]; ?></td>
            <?php $total_q6+=$value['Q6']; ?>
            <td style="padding: 3px;">
            <?php 
                $rowTotal = (int)$value["Q1"]+(int)$value["Q2"]+(int)$value["Q3"]+(int)$value["Q4"]+(int)$value["Q5"]+(int)$value["Q6"];
                $grandTotal += $rowTotal;
                if ($rowTotal !== 0) {
                    echo $rowTotal;
                }
            ?>
            </td>
        </tr>
        <?php } ?>
        <tr style="background: #eeeeee">
            <td colspan="2">Total</td>
            <td><?php echo $total_q1; ?></td>
            <td><?php echo $total_q2; ?></td>
            <td><?php echo $total_q3; ?></td>
            <td><?php echo $total_q4; ?></td>
            <td><?php echo $total_q5; ?></td>
            <td><?php echo $total_q6; ?></td>
            <td><?php echo (int)$grandTotal; ?></td>
        </tr>

    </table>
    <!-- <table cellpadding="40" width="100%" align="center">
        <tr>
            <td style="text-align: center; font-weight: bold;">
                ________________________________
                <br> Operator
            </td>
            <td style="text-align: center; font-weight: bold;">
                 ________________________________
                <br> Leader
            </td>
        </tr>
    </table> -->
</body>
</html>
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf=new mPDF('th','A4', 0, '', 2, 2, 2, 2);
// $mpdf->SetHTMLFooter("<span style='text-align: left;'>Ref.WI-MP-1.15</span> <span style='text-align: right;'>FM-MP-1.15.1,Issue #3</span>");
// $mpdf->SetHTMLFooter('
// <table class="table" width="100%">
// <tr class="tr">
//     <td class="td" align="left">
//         Ref.WI-MP-1.15
//     </td>
//     <td class="td" align="right">
//         FM-MP-1.15.1,Issue #3
//     </td>
// </tr>
// </table>
// ');
$mpdf->WriteHTML($html);
$mpdf->Output();