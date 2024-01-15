<?php $this->layout("layouts/base", ['title' => 'Target Greentire']);
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
} ?>

<style>
    table thead tr th {
        text-align: center;
        padding: 10px;
        vertical-align: center;
    }
</style>

<div class="panel panel-default" style="margin-top: 10px;">
    <div class="panel-heading">Target Greentire</div>
    <div class="panel-body scroll-x">
        <div class="mb-2">
            <!-- <button class="btn btn-primary" style="display: none;" id="btnAddGreentire">
                <i class="fa fa-plus"></i> เพิ่มรายการ
            </button>
            <button class="btn btn-danger" style="display: none;" id="btnDeleteGreentire">
                <i class="fa fa-times"></i> ลบรายการ
            </button>
            <button class="btn btn-default" id="btnAddData"><i class="fa fa-pencil"></i> ตรวจสอบข้อมูล</button> -->

        </div>
        <table id="gridTargetGreentire" class="mb-2 row-border nowrap" style="width:100%;">
            <thead>
                <tr>
                    <th colspan="5" class="text-center" style="padding: 10px;">
                        <?php echo getThaiDate($date); ?>
                    </th>
                    <th colspan="3">BOM <?php echo $shift1; ?></th>
                    <th colspan="3">BOM <?php echo $shift2; ?></th>

                </tr>
                <tr>
                    <th>Item Id</th>
                    <th>Size</th>
                    <th>TT/TL</th>
                    <th>Color</th>
                    <th>Weight</th>
                    <th>เบิกใช้</th>
                    <th>เบิกให้</th>
                    <th>หน้าเตา</th>
                    <th>เบิกใช้</th>
                    <th>เบิกให้</th>
                    <th>หน้าเตา</th>

                </tr>
            </thead>
        </table>
    </div>
</div>






<script>
    $(document).ready(function() {

        //$.blockUI();

        loadGrid({
            el: "#gridTargetGreentire",
            processing: true,
            serverSide: true,
            deferRender: true,
            searching: true,
            ordering: false,
            order: [],
            orderCellsTop: true,
            destroy: true,
            paging: true,
            filterHead: 4,
            select: {
                style: "single",
            },
            ajax: {
                url: "/sch2/api/target_billbuy/<?php echo date("Y-m-d", strtotime($date)); ?>",
                method: "post",
            },
            initComplete: function() {
                $.unblockUI();
            },
            fnDrawCallback: function(settings, json) {


            },
            // columnDefs: [

            // ],
            columns: [
                //   {
                //         data: "Id",
                //     },
                {
                    data: "ItemID",
                },
                {
                    data: "ItemName",
                },
                {
                    data: "TT",
                },
                {
                    data: "ColorAll"
                },
                {
                    data: "Weight"
                },
                {
                    data: "BillUse1"
                },
                {
                    data: "BillGive1"
                },
                {
                    data: "faceBoiler1"
                },
                {
                    data: "BillUse2"
                },
                {
                    data: "BillGive2"
                },
                {
                    data: "faceBoiler2"
                },


            ],
        });












    }); // end

    // function serializeColor(color) {
    //     if (color === null || color === "") {
    //         return "";
    //     } else {
    //         return color + "/";
    //     }

    // }

    // function addSeparatorsNF(nStr, inD, outD, sep) {
    //     nStr += '';
    //     var dpos = nStr.indexOf(inD);
    //     var nStrEnd = '';
    //     if (dpos != -1) {
    //         nStrEnd = outD + nStr.substring(dpos + 1, nStr.length);
    //         nStr = nStr.substring(0, dpos);
    //     }
    //     var rgx = /(\d+)(\d{3})/;
    //     while (rgx.test(nStr)) {
    //         nStr = nStr.replace(rgx, '$1' + sep + '$2');
    //     }
    //     return nStr + nStrEnd;
    // }
</script>