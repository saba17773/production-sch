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

    .sky {
        color: black !important;
        background-color: #CCFFFF !important;
    }

    .blue {
        color: black !important;
        background-color: #ccefff !important;
    }
</style>

<div class="row">
    <div class="col-xs-2">
        Date: <input type="text" id="date_sch" name="date_sch" class="form-control" value="<?php echo $_REQUEST['date_sch'] ?>" readonly>
    </div>
    <div class="col-xs-2">
        Shift: <input type="text" id="shift1" name="shift1" class="form-control" value="<?php if ($_REQUEST['shift'] == 1) {
                                                                                            echo "08.00-20.00";
                                                                                        } else echo "20.00-08.00"; ?>" readonly>
    </div>

    <input type="hidden" id="shift" name="shift" class="form-control" value="<?php echo $_REQUEST['shift'] ?>" readonly>

</div>





<p id="txtcomplete"></p>

<hr>
<div class="alert alert-danger" role="alert" id="message_checkdata"></div>
<!-- grid sch -->
<div id="grid_sch"></div>





<script type="text/javascript">
    jQuery(document).ready(function($) {

        $('#message_checkdata').hide();
        var date_sch = $("input[name=date_sch]").val();
        var shift = $("input[name=shift]").val()

        loadgridsch(date_sch, shift);














    });



    function loadgridsch(date_sch, shift) {
        $('#message_statusload').html('<strong>Loading</strong>');
        // var shift = <?php echo $_REQUEST['shift'] ?>
        // alert()

        var dataAdapter = new $.jqx.dataAdapter({
            datatype: "json",
            updaterow: function(rowid, rowdata, commit) {
                gojax('post', '/production/sch/update/sch2', {

                    BillUse: rowdata.BillUse,
                    BillGive: rowdata.BillGive,
                    faceBoiler: rowdata.faceBoiler,
                    id: rowdata.ID,
                    shift: shift,
                    date_sch: date_sch

                }).done(function(data) {
                    if (data.result === 200) {

                        commit(true);
                        //  alert(data.message);
                    } else {

                        commit(false);
                    }
                    // console.log(data);
                }).fail(function() {
                    commit(false);

                });

            },

            datafields: [{
                    name: "ID",
                    type: "int"
                },
                {
                    name: "Boiler",
                    type: "string"
                },
                {
                    name: "BoilerName",
                    type: "string"
                },
                {
                    name: "Employee",
                    type: "int"
                },
                {
                    name: "FullName",
                    type: "string"
                },
                {
                    name: "ItemID",
                    type: "string"
                },
                {
                    name: "ItemName",
                    type: "string"
                },
                {
                    name: "NameTH",
                    type: "string"
                },
                {
                    name: "Time",
                    type: "int"
                },
                {
                    name: "BillUse",
                    type: "int"
                },
                {
                    name: "BillGive",
                    type: "int"
                },
                {
                    name: "faceBoiler",
                    type: "int"
                },
                {
                    name: "MoldID",
                    type: "int"
                },



            ],
            // sortcolumn: 'CurID',
            // sortdirection: 'asc',
            url: '/production/sch/load?date_sch=' + date_sch + '&shift=' + shift,
            async: false
        });

        var setDelete = function(row, column, value) {
            if (value !== "") {
                return "<div style='padding:4px;'>" + value + "</div>";
            } else {
                return "<div style='font-size: 1em; padding:3px;'><button style='width:18px; height:18px; padding: 0.2px;' class='btn btn-danger' onclick='return setDelete(" + row + ")' style=' width:25px;'><b>-</b></button></div>";
            }

        }





        var clearItem = function(row, column, value) {
            if (value !== "") {
                return "<div style='padding:4px;'>" + value + "</div>";
            } else {
                return "<div style='font-size: 1em; padding:3px;'><button style='width:20px; height:21px; padding: 0.2px;' class='btn btn-danger' onclick='return clearItemFunc(" + row + ")'>X</button></div>";
            }

        }


        gojax('get', '/production/sch/confirm/check', {
            date: date_sch,
            shift: shift
        }).done(function(data) {
            confirm = data.result;
            // $('#confirm_x').val(confirm);
            // console.log(confirm);
            if (confirm == 1) {
                $('#btn_generate').attr({
                    disabled: true
                });
                return $("#grid_sch").jqxGrid({
                    width: '100%',
                    source: dataAdapter,
                    pageable: true,
                    altRows: true,
                    columnsResize: true,
                    filterable: true,
                    editable: true,
                    selectionmode: 'singlecell',
                    editmode: 'click',
                    autoheight: true,
                    pageSize: 12,
                    rowsheight: 32,
                    pagesizeoptions: [12, 24],
                    sortable: true,
                    columns: [{
                            text: "เตา",
                            datafield: "BoilerName",
                            align: 'center',
                            width: '5%',
                            hidden: false,
                            editable: false
                        },
                        {
                            text: "ชื่อพนักงาน",
                            datafield: "FullName",
                            align: 'center',
                            width: '8%',
                            editable: false
                        },
                        {
                            text: "พิมพ์",
                            datafield: "MoldID",
                            align: 'center',
                            width: '2%',
                            editable: false
                        },
                        // {
                        // 	text: "",
                        // 	cellsrenderer: setEmployee,
                        // 	width: '4%',
                        // 	editable: false
                        // },
                        {
                            text: "ItemID",
                            datafield: "ItemID",
                            align: 'center',
                            width: '5%',
                            editable: false
                        },
                        {
                            text: "ขนาดพิมพ์",
                            datafield: "ItemName",
                            align: 'center',
                            editable: false
                        },
                        {
                            text: "เบิกใช้",
                            datafield: "BillUse",
                            align: 'center',
                            editable: true
                        },
                        {
                            text: "เบิกให้",
                            datafield: "BillGive",
                            align: 'center',
                            editable: true
                        },
                        {
                            text: "หน้าเตา",
                            datafield: "faceBoiler",
                            align: 'center',
                            editable: true
                        },


                        // {
                        // 	text: "หมายเหตุ",
                        // 	datafield: "Remark",
                        // 	width: '8%',
                        // 	align: 'center',
                        // 	editable: false
                        // },
                        // {
                        // 	text: "",
                        // 	cellsrenderer: setRemark,
                        // 	width: '5%',
                        // 	editable: false
                        // },
                        // {
                        // 	text: "",
                        // 	cellsrenderer: clearItem,
                        // 	width: '2.5%',
                        // 	editable: false
                        // },

                    ],
                    columnGroups: [{
                        text: 'จำนวนการอบยาง',
                        align: 'center',
                        name: 'ProductDetails'
                    }]
                });

            } else if (confirm == 2) {
                $('#btn_generate').attr({
                    disabled: true
                });
                return $("#grid_sch").jqxGrid({
                    width: '50%',
                    source: dataAdapter,
                    pageable: true,
                    altRows: true,
                    columnsResize: true,
                    filterable: true,
                    editable: true,
                    selectionmode: 'singlecell',
                    editmode: 'click',
                    autoheight: true,
                    pageSize: 12,
                    rowsheight: 32,
                    pagesizeoptions: [12, 24],
                    sortable: true,
                    columns: [{
                            text: "เตา",
                            datafield: "BoilerName",
                            align: 'center',
                            width: '20%',
                            hidden: false,
                            editable: false,
                            // cellsrenderer: setBoilerName,
                        },
                        // {
                        // 	text: "ชื่อพนักงาน",
                        // 	datafield: "FullName",
                        // 	align: 'center',
                        // 	width: '8%',
                        // 	editable: false
                        // },
                        {
                            text: "พิมพ์",
                            datafield: "MoldID",
                            align: 'center',
                            width: '5%',
                            editable: false
                        },
                        // {
                        // 	text: "",
                        // 	cellsrenderer: setEmployee,
                        // 	width: '4%',
                        // 	editable: false
                        // },
                        {
                            text: "ItemID",
                            datafield: "ItemID",
                            align: 'center',
                            width: '10%',
                            editable: false
                        },
                        {
                            text: "ขนาดพิมพ์",
                            datafield: "ItemName",
                            align: 'center',
                            width: '40%',
                            editable: false
                        },
                        {
                            text: "เบิกใช้",
                            datafield: "BillUse",
                            align: 'center',
                            width: '7%',
                            editable: true
                        },
                        {
                            text: "เบิกให้",
                            datafield: "BillGive",
                            align: 'center',
                            width: '7%',
                            editable: true
                        },
                        {
                            text: "หน้าเตา",
                            datafield: "faceBoiler",
                            align: 'center',
                            width: '7%',
                            editable: true
                        },


                        // {
                        // 	text: "",
                        // 	cellsrenderer: clearItem,
                        // 	width: '2.5%',
                        // 	editable: false
                        // },

                    ],
                    columnGroups: [{
                        text: 'จำนวนการอบยาง',
                        align: 'center',
                        name: 'ProductDetails'
                    }]
                });

            } else {
                $('#btn_generate').attr({
                    disabled: false
                });
                return $("#grid_sch").jqxGrid({
                    width: '100%',
                    source: dataAdapter,
                    pageable: true,
                    altRows: true,
                    columnsResize: true,
                    filterable: true,
                    editable: true,
                    selectionmode: 'singlecell',
                    editmode: 'click',
                    autoheight: true,
                    pageSize: 12,
                    rowsheight: 32,
                    pagesizeoptions: [12, 24],
                    sortable: true,
                    columns: [{
                            text: "เตา",
                            datafield: "BoilerName",
                            align: 'center',
                            width: '20%',
                            hidden: false,
                            editable: false,
                            // cellsrenderer: setBoilerName,
                        },
                        // {
                        // 	text: "ชื่อพนักงาน",
                        // 	datafield: "FullName",
                        // 	align: 'center',
                        // 	width: '8%',
                        // 	editable: false
                        // },
                        {
                            text: "พิมพ์",
                            datafield: "MoldID",
                            align: 'center',
                            width: '5%',
                            editable: false
                        },
                        // {
                        // 	text: "",
                        // 	cellsrenderer: setEmployee,
                        // 	width: '4%',
                        // 	editable: false
                        // },
                        {
                            text: "ItemID",
                            datafield: "ItemID",
                            align: 'center',
                            width: '10%',
                            editable: false
                        },
                        {
                            text: "ขนาดพิมพ์",
                            datafield: "ItemName",
                            align: 'center',
                            width: '40%',
                            editable: false
                        },
                        {
                            text: "เบิกใช้",
                            datafield: "BillUse",
                            align: 'center',
                            width: '7%',
                            editable: true
                        },
                        {
                            text: "เบิกให้",
                            datafield: "BillGive",
                            align: 'center',
                            width: '7%',
                            editable: true
                        },
                        {
                            text: "หน้าเตา",
                            datafield: "faceBoiler",
                            align: 'center',
                            width: '7%',
                            editable: true
                        },


                        // {
                        // 	text: "",
                        // 	cellsrenderer: clearItem,
                        // 	width: '2.5%',
                        // 	editable: false
                        // },

                    ],
                    columnGroups: [{
                        text: 'จำนวนการอบยาง',
                        align: 'center',
                        name: 'ProductDetails'
                    }]
                });

            }
        });
        // console.log($('#confirm_x').val());

    }
</script>