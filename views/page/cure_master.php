<?php $this->layout("layouts/base", ['title' => 'Cure Master']); ?>

<h1>Cure Master</h1>
<hr>

<div class="panel panel-default" id="panel_manage">
    <div class="panel-group">
        <div class="form-group">
            <button class="btn btn-success" id="btn_create"><span class="glyphicon glyphicon-plus"></span> Create New</button>
            <button class="btn btn-info" id="btn_edit"><span class="glyphicon glyphicon-edit"></span> Edit </button>
            <!-- <button class="btn btn-warning" id="btn_line"><span class="glyphicon glyphicon-list"></span> Line</button> -->
        </div>
    </div>
    <div>
        <div id="grid_main"></div>
    </div>
</div>

<div class="modal" id="modal_createnew_group" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document" style="width:500px;margin:auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
                <h5 class="modal-title">สร้างเตาใหม่</h5>
            </div>
            <div class="modal-body">
                <form id="form_createnew_group">
                    <table>

                        <tr>
                            <td align="right">
                                Cur ID : &nbsp;
                            </td>
                            <td style="padding: 5px;" align="center">
                                <div class="input-group" style="width: 300px;">
                                    <input type="text" id="txt_CurID" name="txt_CurID" class="form-control" required autocomplete="off" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                Cure Size : &nbsp;
                            </td>
                            <td style="padding: 5px;" align="left">
                                <div class="input-group" style="width: 100px;">
                                    <input type="text" id="txt_CureSize" name="txt_CureSize" class="form-control" required autocomplete="off" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td style="padding: 5px;" align="left">
                                <button class="btn btn-success btn-sm" id="btn_edit_group">
                                    <span class="glyphicon glyphicon-ok"></span> Save
                                </button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal" id="modal_edit_group" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document" style="width:500px;margin:auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
                <h5 class="modal-title">แก้ไขเตา</h5>
            </div>
            <div class="modal-body">
                <form id="form_edit_group">
                    <table>
                        <tr>
                            <td align="right">
                                ID : &nbsp;
                            </td>
                            <td style="padding: 5px;" align="left">
                                <div class="input-group" style="width: 100px;">
                                    <input type="text" id="txt_edit_groupid" name="txt_edit_groupid" class="form-control" required autocomplete="off" readonly />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                Cur ID : &nbsp;
                            </td>
                            <td style="padding: 5px;" align="center">
                                <div class="input-group" style="width: 300px;">
                                    <input type="text" id="txt_edit_groupdesc" name="txt_edit_groupdesc" class="form-control" required autocomplete="off" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                CureSize : &nbsp;
                            </td>
                            <td style="padding: 5px;" align="left">
                                <div class="input-group" style="width: 100px;">
                                    <input type="text" id="txt_edit_sortby" name="txt_edit_sortby" class="form-control" required autocomplete="off" />
                                </div>
                            </td>
                        </tr>

                        <tr>



                            <td align="center">
                                <input type="radio" name="txt_active" id="_active" value="1" /> Active &nbsp;
                            </td>
                            <td>
                                <input type="radio" name="txt_active" id="_disactive" value="0" /> No Active
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td style="padding: 5px;" align="left">
                                <button class="btn btn-success btn-sm" id="btn_edit_group">
                                    <span class="glyphicon glyphicon-ok"></span> Save
                                </button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
    jQuery(document).ready(function($) {
        bind_gridmain();


        $("#btn_create").on('click', function(event) {
            $('#modal_createnew_group').modal({
                backdrop: 'static'
            });
            $("#form_createnew_group")[0].reset();

        });

        $("#btn_edit").on('click', function(event) {
            event.preventDefault();
            var rowdata = row_selected('#grid_main');
            if (typeof rowdata !== 'undefined') {
                $('#modal_edit_group').modal({
                    backdrop: 'static'
                });
                id = rowdata.ID;
                desc = rowdata.CurID;
                sort = rowdata.CureSize;
                active = rowdata.Active;

                if (active == 1) {
                    $("#_active").prop("checked", true);
                } else {
                    $("#_disactive").prop("checked", true);
                }

                $('#txt_edit_groupid').val(id);
                $('#txt_edit_groupdesc').val(desc);
                $('#txt_edit_sortby').val(sort);
            } else {
                $('#modal_alert').modal({
                    backdrop: 'static'
                });
                $('#modal_alert_message').text('กรุณาเลือกข้อมูล');
            }

        });



        $("#btn_create_detail").on('click', function(event) {
            // $('#modal_create_detail').modal({backdrop:'static'});
            // $("#form_create_detail")[0].reset();

            // dgrpid = $('#head_line_id').text();
            // $('#txt_new_dgrpid').val(dgrpid);
            $('#panel_create').show();
            $('#panel_edit').hide();

        });







        // form_createnew_group
        $('#form_createnew_group').on('submit', function(event) {
            insertGroup();
        });

        $('#form_edit_group').on('submit', function(event) {
            updateGroup();

        });

        function bind_gridmain() {
            var dataAdapter = new $.jqx.dataAdapter({
                datatype: 'json',
                datafields: [{
                        name: 'ID',
                        type: 'int'
                    },
                    {
                        name: 'CurID',
                        type: 'string'
                    },
                    {
                        name: 'CureSize',
                        type: 'string'
                    },
                    {
                        name: 'Active',
                        type: 'int'
                    }
                ],
                url: base_url + "/cure/main"
            });

            return $("#grid_main").jqxGrid({
                width: '100%',
                source: dataAdapter,
                autoheight: true,
                pageSize: 10,
                altrows: true,
                pageable: true,
                sortable: true,
                filterable: true,
                showfilterrow: true,
                columnsresize: true,
                columns: [{
                        text: 'CurID',
                        datafield: 'CurID',
                        width: 70
                    },
                    {
                        text: 'CureSize',
                        datafield: 'CureSize',
                        width: 200
                    }
                    // {
                    //     text: 'Active',
                    //     datafield: 'Active',
                    //     width: 200
                    // }
                ]
            });
        }



        function insertGroup() {

            gojax_f('post', base_url + '/insert/cure', '#form_createnew_group')
                .done(function(data) {
                    if (data.result === true) {
                        $('#grid_main').jqxGrid('updatebounddata');
                    } else {
                        $('#modal_alert').modal({
                            backdrop: 'static'
                        });
                        $('#modal_alert_message').text(data.message);
                    }
                });
        }

        function updateGroup() {

            gojax_f('post', base_url + '/update/cureschmater', '#form_edit_group')
                .done(function(data) {
                    if (data.result === true) {
                        $('#grid_main').jqxGrid('updatebounddata');
                    } else {
                        $('#modal_alert').modal({
                            backdrop: 'static'
                        });
                        $('#modal_alert_message').text(data.message);
                    }
                });
        }

        function insertDetail() {
            gojax('post', '/insert/detail', {
                dDesc: $("#txt_new_ddesc").val(),
                dSize: $("#txt_new_dsize").val(),
                gId: $("#head_line_id").text()
            }).done(function(data) {
                if (data.result === true) {
                    $('#grid_line').jqxGrid('updatebounddata');

                } else {
                    $('#modal_alert').modal({
                        backdrop: 'static'
                    });
                    $('#modal_alert_message').text(data.message);
                }
            });
        }

        function updateDetail() {

            gojax('post', '/update/detail', {
                edDesc: $("#txt_edit_ddesc").val(),
                edSize: $("#txt_edit_dsize").val(),
                edSort: $("#txt_edit_dsortby").val(),
                edIdAuto: $("#txt_edit_idauto").val()
            }).done(function(data) {
                if (data.result === true) {
                    $('#grid_line').jqxGrid('updatebounddata');

                } else {
                    $('#modal_alert').modal({
                        backdrop: 'static'
                    });
                    $('#modal_alert_message').text(data.message);
                }
            });
        }


    });
</script>