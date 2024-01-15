<?php
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
$PermissionService = new App\Services\PermissionService;
?>

<style type="text/css">
    .tdline {
        border: 1px solid #DADADA;
        background-color: #E3E3E3;
        padding: 10px;
    }

    /* .grid-column-header{
		height:500px;
	} */
</style>
<B>Order Report</B>
<br>
<form id="form_filter">
    <table align="center" width="100%">
        <tr>
            <td width="40%">
            </td>
            <td align="center" class="tdline">
                <div class="row">
                    <div class="input-group" style="width: 200px;">
                        <input type="text" id="date_sch" name="date_sch" class=form-control required placeholder="เลือกวันที่..." autocomplete="off" />
                        <span class="input-group-btn">
                            <button class="btn btn-info" id="date_sch_show" type="button">
                                <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                            </button>
                        </span>
                    </div>
                </div>
            </td>
            <td width="35%" align="right" valign="top">
                <p class="bg-primary" style="width: 100px; text-align: center;" id="message_statusload"></p>
            </td>
        </tr>
        <tr>
            <td></td>
            <td align="center" class="tdline">
                <div>
                    <label class="radio-inline">
                        <input type="radio" name="shift" id="shift1" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
                        <span style="padding-left: 10px;"><b>C 08.00-20.00</b></span>
                    </label>
                    <span style="padding-left: 30px;"> </span>
                    <label class="radio-inline">
                        <input type="radio" name="shift" id="shift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
                        <span style="padding-left: 10px;"><b>D 20.00-08.00</b></span>
                    </label>
                </div>
            </td>
        </tr>
    </table>
</form>



<p id="txtcomplete"></p>
<input type="hidden" name="date2" id="date2" />
<input type="hidden" name="date3" id="date3" />

<hr>
<div class="alert alert-danger" role="alert" id="message_checkdata"></div>
<!-- grid sch -->
<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_generate_prosch') === true) : ?>
    <button class="btn btn-default" id="btn_reload">
        <span class="glyphicon glyphicon-refresh"></span> Reload Row
    </button>
<?php endif ?>
<BR>
<div id="grid_sch1"></div>


<script type="text/javascript">
    jQuery(document).ready(function($) {

        $('#message_checkdata').hide();

        var date_set = '<?php echo date('d-m-Y') ?>';
        $("#date_sch").val(date_set);
        // $( "#date_gen").val(date_set);

        $('#date_sch').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        });
        $('#date_gen').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        });
        $('#date_gen_emp').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
        });

        $('#date_sch').datepicker().on('changeDate', function(ev) {
            if ($("input[name=shift]:checked").val() == 1) {
                var shift = 1;
            } else if ($("input[name=shift]:checked").val() == 2) {
                var shift = 2;
            }
            setTimeout(function() {
                checkgridsch($("#date_sch").val(), shift);
            }, 2000);



        });

        $('#date_sch_show').click(function() {
            $('#date_sch').datepicker('show');
        });


        var time_set = '<?php echo date('H:i'); ?>';

        if (time_set >= '08:01' && time_set <= '20:00') {
            $("input[name=shift][value='1']").attr('checked', 'true');
            var shift = 1;
        } else {
            $("input[name=shift][value='2']").attr('checked', 'true');
            var shift = 2;
        }

        checkgridsch($("#date_sch").val(), shift);

        $("#shift1").on('change', function() {
            if ($(this).is(':checked')) {
                setTimeout(function() {
                    checkgridsch($("#date_sch").val(), shift);
                }, 2000);
            }
            shift = 1;
        });

        $("#shift2").on('change', function() {
            if ($(this).is(':checked')) {
                setTimeout(function() {
                    checkgridsch($("#date_sch").val(), shift);
                }, 2000);
            }
            shift = 2;
        });

        $("#grid_sch1").on('bindingcomplete', function(event) {
            $('#message_statusload').html('<strong>Ready</strong>');
            // $('#btn_confirm').attr({disabled:false});
            // $('#btn_confirm').html('<span class="glyphicon glyphicon-lock"></span> Confirm');		
            // $('#btn_confirmed').attr({disabled:false});
            // $('#btn_confirmed').html('<span class="glyphicon glyphicon-lock"></span> Confirmed');	
            // $('#btn_unconfirm').attr({disabled:false});
            // $('#btn_unconfirm').html('<span class="glyphicon glyphicon-repeat"></span> UnLock');	
        });

        $("#btn_reload").on("click", function() {

            $('#grid_sch1').jqxGrid('updateBoundData', 'cells');

        });



    });

    function checkgridsch(date_sch, shift) {




        gojax('get', '/sch2/sch/data/checkprint', {
            date_sch: date_sch,
            shift: shift
        }).done(function(data) {
            if (data.result == true) {
                loadgridsc1(date_sch, shift);

                $("#grid_sch1").show();


                $('#message_checkdata').hide();
                $('#btn_reload').show();
                // $('#message_checkdata').html('<strong>' + data.message + '</strong>');

            } else {

                $("#grid_sch1").hide();
                $('#message_checkdata').show();
                $('#message_checkdata').html('<strong>' + data.message + '</strong>');
                $('#btn_reload').hide();


            }
        });

    }







    function loadgridsc1(date_sch, shift) {



        $('#message_statusload').html('<strong>Loading</strong>');

        var dataAdapter = new $.jqx.dataAdapter({
            datatype: "json",
            updaterow: function(rowid, rowdata, commit) {

                gojax('post', '/sch2/sch/update/schordersummary', {
                    CountInOrder: rowdata.CountInOrder,
                    id: rowdata.Id,
                    date_sch: date_sch,
                    shift: shift,


                }).done(function(data) {
                    if (data.status === 200) {
                        $('#grid_sch1').jqxGrid('updateBoundData', 'cells');
                        commit(true);
                        //alert(data.message);

                    } else {
                        $('#grid_sch1').jqxGrid('updateBoundData', 'cells');
                        //  alert(data.message);
                    }
                    console.log(data);
                }).fail(function() {
                    commit(false);
                });


            },

            datafields: [


                {
                    name: "Id",
                    type: "int"
                },

                {
                    name: "ItemId",
                    type: "string"
                },
                {
                    name: "ColorAll",
                    type: "string"
                },
                {
                    name: "ItemGTName",
                    type: "string"
                },
                {
                    name: "Actual",
                    type: "int"
                },
                {
                    name: "BomCheck",
                    type: "int"
                },
                {
                    name: "SpareOfcure",
                    type: "int"
                },
                {
                    name: "StockInplan",
                    type: "int"
                },
                {
                    name: "CountIn",
                    type: "int"
                },
                {
                    name: "CountOut",
                    type: "int"
                },
                {
                    name: "CountCure",
                    type: "int"
                },
                {
                    name: "SpareOfcure2",
                    type: "int"
                },
                {
                    name: "CountInOrder",
                    type: "int"
                },
                {
                    name: "GreentireInDept",
                    type: "int"
                },
                {
                    name: "SummaryInDept",
                    type: "int"
                },
                {
                    name: "CalCure",
                    type: "int"
                },
                {
                    name: "SummaryCure",
                    type: "int"
                },
                {
                    name: "CompareCreateRecve",
                    type: "int"
                },
                {
                    name: "CompareBillBuy",
                    type: "int"
                },
                {
                    name: "CompareFaceTire",
                    type: "int"
                },
                {
                    name: "CompareReal",
                    type: "int"
                },
                // {
                // 	name: "StockStatus",
                // 	type: "int"
                // },
                // {
                // 	name: "check3",
                // 	type: "float"
                // },
                // {
                // 	name: "checktotal",
                // 	type: "float"
                // }


            ],

            url: '/productionfacetire/sch2/loadordersummary?date_sch=' + date_sch + '&shift=' + shift
        });

        return $("#grid_sch1").jqxGrid({
            width: '100%',
            source: dataAdapter,
            pageable: true,
            altRows: true,
            columnsResize: true,
            filterable: true,
            editable: true,
            selectionmode: 'singlecell',
            columnsheight: 50,
            pageSize: 10,
            sortable: true,
            editmode: 'click',
            autoheight: true,
            rowsheight: 32,
            pagesizeoptions: [12, 24],

            columns: [

                {
                    text: "No.",
                    width: 50,
                    cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
                        return '<div style=\'padding: 5px; color:#000000;\'> ' + (index + 1) + ' </div>';
                    }
                },

                {
                    text: "Item ID",
                    datafield: "ItemId",
                    align: 'center',
                    width: '85',
                    //	columngroup: 'Order',
                    editable: false
                },
                {
                    text: "Item Name",
                    datafield: "ItemGTName",
                    align: 'center',
                    width: '300',
                    //columngroup: 'OrderTire',
                    cellsformat: 'F2',
                    editable: false
                },
                {
                    text: "Color",
                    datafield: "ColorAll",
                    align: 'center',
                    width: '100',
                    //columngroup: 'OrderTire',
                    cellsformat: 'F2',
                    editable: false
                },
                {
                    text: "สร้างโครง<BR>ผลิตได้(เส้น)",
                    datafield: "Actual",
                    align: 'center',
                    width: '80',
                    //columngroup: 'OrderTire',
                    editable: false
                },
                {
                    text: "อบยาง<BR>ผลิตได้(เส้น)",
                    datafield: "BomCheck",
                    align: 'center',
                    width: '80',
                    //columngroup: 'OrderTire',
                    editable: false
                },
                {
                    text: "Spare<BR>หน้าเตา",
                    datafield: "SpareOfcure",
                    align: 'center',
                    width: '80',
                    columngroup: 'GreentireToppic',
                    editable: false
                },

                {
                    text: "Stock<BR>ในแผนก",
                    datafield: "StockInplan",
                    align: 'center',
                    width: '100',
                    columngroup: 'GreentireToppic',
                    editable: false
                },
                {
                    text: "รับเข้า",
                    datafield: "CountIn",
                    align: 'center',
                    width: '80',
                    columngroup: 'GreentireToppic',
                    editable: false
                },
                {
                    text: "มีกรีนไทร์<BR>ในแผนก",
                    datafield: "GreentireInDept",
                    align: 'center',
                    width: '80',
                    columngroup: 'GreentireToppic',
                    editable: false
                },
                {
                    text: "จ่ายออก",
                    datafield: "CountOut",
                    align: 'center',
                    columngroup: 'GreentireToppic',
                    width: '80',
                    editable: false
                },
                {
                    text: "คงเหลือ<BR>ในแผนก",
                    datafield: "SummaryInDept",
                    align: 'center',
                    columngroup: 'GreentireToppic',
                    width: '80',
                    editable: false
                },
                {
                    text: "อบยางเบิก",
                    datafield: "CountCure",
                    align: 'center',
                    width: '80',
                    columngroup: 'GreentireToppic',
                    editable: false
                },
                {
                    text: "คำนวณ<BR>หน้าเตา",
                    datafield: "CalCure",
                    align: 'center',
                    columngroup: 'GreentireToppic',
                    width: '80',
                    editable: false
                },
                {
                    text: "Spare<BR>หน้าเตา",
                    datafield: "SpareOfcure2",
                    align: 'center',
                    columngroup: 'GreentireToppic',
                    width: '80',
                    editable: false
                },
                {
                    text: "คงเหลือใน<BR>แผนก+หน้าเตา",
                    datafield: "SummaryCure",
                    align: 'center',
                    width: '100',
                    columngroup: 'GreentireToppic',
                    editable: false
                },
                {
                    text: "สร้าง/รับเข้า",
                    datafield: "CompareCreateRecve",
                    align: 'center',
                    width: '80',
                    columngroup: 'CompareToppic',
                    editable: false
                },
                {
                    text: "เบิก/จ่าย",
                    datafield: "CompareBillBuy",
                    align: 'center',
                    width: '80',
                    columngroup: 'CompareToppic',
                    editable: false
                },
                {
                    text: "ยางหน้าเตา<BR>คำนวณ/นับจริง",
                    datafield: "CompareFaceTire",
                    align: 'center',
                    width: '100',
                    columngroup: 'CompareToppic',
                    editable: false
                },
                {
                    text: "นับจริง",
                    datafield: "CountInOrder",
                    align: 'center',
                    width: '80',
                    columngroup: 'CureToppic',
                    editable: true
                },
                {
                    text: "เปรียบเทียบ",
                    datafield: "CompareReal",
                    align: 'center',
                    columngroup: 'CureToppic',
                    width: '80',
                    editable: false
                },

            ],
            columnGroups: [{
                    text: 'กรีนไทร์',
                    align: 'center',
                    name: 'GreentireToppic'
                },
                {
                    text: 'เปรียบเทียบ',
                    align: 'center',
                    name: 'CompareToppic'
                },
                {
                    text: 'หน้าเตา',
                    align: 'center',
                    name: 'CureToppic'
                }

            ]

        });
    }
</script>