<?php $this->layout("layouts/base", ['title' => 'Scheduler Main Page']); ?>

<div class="panel panel-default" style="margin-top: 10px;">
  <div class="panel-heading">Shift Transaction</div>
  <div class="panel-body scroll-x">
    <div class="mb-2">
      <button class="btn btn-primary" id="btnAddShift"><i class="fa fa-plus"></i> เพิ่มรายการ</button>
      <button class="btn btn-default" id="btnAddData"><i class="fa fa-pencil"></i> ตรวจสอบข้อมูล</button>
      <button class="btn btn-info" id="btnReport"><i class="fa fa-file"></i> รายงาน PDF</button>
      <button class="btn btn-success" id="btnReportExcel"><i class="fa fa-file"></i> รายงาน Excel</button>
      <button class="btn btn-danger" id="btnCancel"><i class="fa fa-times"></i> ยกเลิก</button>
      <button class="btn btn-warning" id="btnPlan"><i class="fa fa-calendar"></i> แผนผลิตสร้างโครง</button>
      <button class="btn btn-info" id="btnReportCure"> <i class=" fa fa-file"></i> รายงานอบยาง PDF</button>
      <button class="btn btn-success" id="btnReportCureExcel"><i class="fa fa-file"></i> รายงานอบยาง Excel</button>
    </div>
    <table id="gridShiftTrans" class="mb-2 row-border nowrap" style="width:100%;">
      <thead>
        <tr>
          <th>Id</th>
          <th>Shift Date</th>
          <th>Create By</th>
          <th>Create Date</th>
          <th>Update By</th>
          <th>Update Date</th>
          <th>Confirm By</th>
          <th>Confirm Date</th>
          <th>Module</th>
          <th>Status</th>
        </tr>
        <tr>
          <th>Id</th>
          <th>Shift Date</th>
          <th>Create By</th>
          <th>Create Date</th>
          <th>Update By</th>
          <th>Update Date</th>
          <th>Confirm By</th>
          <th>Confirm Date</th>
          <th>Module</th>
          <th>Status</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- modal add role -->
<div class="modal" id="modalAddShift" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button href="javascript.void(0);" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i>
        </button>
        <h4 class="modal-title">Add New Shift</h4>
      </div>
      <div class="modal-body">
        <form id="formAddShiftTrans" class="mb-2">
          <div class="form-group" style="margin-right: 10px;">
            <label for="">Date</label>
            <input type="text" name="Date" class="form-control" />
          </div>
          <div class="form-group" style="margin-right: 10px;">
            <label for="">Module</label>
            <select name="Module" id="Module" class="form-control"> </select>
          </div>
          <div class="form-group">
            <input type="hidden" name="Shift" id="Shift" value="1" />
            <button class="btn btn-success" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- modal add build -->
<div class="modal" id="modalBuild" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button href="javascript.void(0);" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i>
        </button>
        <h4 class="modal-title">Building</h4>
      </div>
      <div class="modal-body">
        <div class="form-group" style="margin-right: 10px;">
          <button class="btn btn-primary" id="btnImport"><i class="fa fa-upload"></i> Import Build</button>
          <button class="btn btn-warning" id="btnExport"><i class="fa fa-download"></i> Export TargetGreentire</button>
          <button class="btn btn-danger" id="btnClear"><i class="fa fa-remove"></i> Clear Build</button>
          <button class="btn btn-success" id="btnView"><i class="fa fa-pencil"></i> View Build</button>
        </div>

        <hr>

        <form action="/sch/api/import" method="post" enctype="multipart/form-data" id="submitImport" style="background-color: #b9caff;">
          <div class="form-group">
            <label><u>Import File Excel</u></label>
            <label>
              <p id="messageImport"></p>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <label for="">Date</label>
            <span style="padding-left: 30px;"> </span>
            <label class="radio-inline">
              <div class="row">
                <div class="input-group" style="width: 200px;">
                  <input type="text" id="ImportDate" name="ImportDate" class=form-control required placeholder="เลือกวันที่..." autocomplete="off" required />
                  <span class="input-group-btn">
                    <button class="btn btn-primary" id="ImportDateShow" type="button">
                      <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    </button>
                  </span>
                </div>
              </div>
            </label>
            <span style="padding: 30px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ImportShift" id="ImportShift1" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;" checked required>
              <span style="padding-left: 10px;"><b>08.00-20.00</b></span>
            </label>
            <span style="padding: 10px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ImportShift" id="ImportShift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;" required>
              <span style="padding-left: 10px;"><b>20.00-08.00</b></span>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <label for="">Browse</label>
            <label class="radio-inline">
              <input type="file" name="ImportFile" id="ImportFile" class="form-control" required>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Import</button>
          </div>
        </form>

        <form action="/sch/build/export" method="get" id="submitExport" target="_blank" style="background-color: #fff59d;">
          <div class="form-group">
            <label><u>Export File Excel</u></label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <label for="">Date</label>
            <span style="padding-left: 30px;"> </span>
            <label class="radio-inline">
              <div class="row">
                <div class="input-group" style="width: 200px;">
                  <input type="text" id="ExportDate" name="ExportDate" class=form-control required placeholder="เลือกวันที่..." autocomplete="off" required />
                  <span class="input-group-btn">
                    <button class="btn btn-warning" id="ExportDateShow" type="button">
                      <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    </button>
                  </span>
                </div>
              </div>
            </label>
            <span style="padding: 30px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ExportShift" id="ExportShift1" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;" required>
              <span style="padding-left: 10px;"><b>08.00-20.00</b></span>
            </label>
            <span style="padding: 10px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ExportShift" id="ExportShift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;" required>
              <span style="padding-left: 10px;"><b>20.00-08.00</b></span>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <button type="submit" class="btn btn-warning"><i class="fa fa-download"></i> Export</button>
          </div>
        </form>

        <form id="submitClear" style="background-color: #ff9a7a;">
          <div class="form-group">
            <label><u>Clear Data</u></label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <label for="">Date</label>
            <span style="padding-left: 30px;"> </span>
            <label class="radio-inline">
              <div class="row">
                <div class="input-group" style="width: 200px;">
                  <input type="text" id="ClearDate" name="ClearDate" class=form-control required placeholder="เลือกวันที่..." autocomplete="off" required />
                  <span class="input-group-btn">
                    <button class="btn btn-danger" id="ClearDateShow" type="button">
                      <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    </button>
                  </span>
                </div>
              </div>
            </label>
            <span style="padding: 30px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ClearShift" id="ClearShift1" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;" required>
              <span style="padding-left: 10px;"><b>08.00-20.00</b></span>
            </label>
            <span style="padding: 10px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ClearShift" id="ClearShift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;" required>
              <span style="padding-left: 10px;"><b>20.00-08.00</b></span>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <button type="submit" class="btn btn-danger"><i class="fa fa-remove"></i> Clear</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<!-- form submit input value -->
<form id="formModule" method="post">
  <input type="hidden" name="_shift" />
  <input type="hidden" name="_date" />
  <input type="hidden" name="_module" />
  <input type="hidden" name="_transId" />
</form>

<script>
  $(document).ready(function() {
    // ################ init ################

    $.blockUI();

    $("input[name=Date]").datepicker({
      dateFormat: "yy-mm-dd",
    });

    loadGrid({
      el: "#gridShiftTrans",
      processing: true,
      serverSide: true,
      deferRender: true,
      searching: true,
      order: [],
      orderCellsTop: true,
      destroy: true,
      select: {
        style: "single",
      },
      ajax: {
        url: "/sch2/api/shift_trans/get_all",
        method: "post",
      },
      initComplete: function() {
        $.unblockUI();
      },
      columns: [{
          data: "Id",
        },
        {
          data: "ShiftDate",
        },
        {
          data: "CreateBy",
        },
        {
          data: "CreateDate",
        },
        {
          data: "UpdateBy",
        },
        {
          data: "UpdateDate",
        },
        {
          data: "ConfirmBy",
        },
        {
          data: "ConfirmDate",
        },
        {
          data: "Module",
        },
        {
          data: "Status"
        }
      ],
      // columnDefs: [{
      //   render: function(data, type, row) {
      //     return dayjs(data).format('DD-MM-YYYY');
      //   },
      //   targets: 1,
      // }, ],
    });

    // ################ end init ################

    // form add shift trans
    $("#formAddShiftTrans").on("submit", function(e) {
      e.preventDefault();
      $.blockUI();

      ajax({
        url: "/sch2/api/shift_trans/add",
        type: "post",
        data: $("#formAddShiftTrans").serialize(),
      }).done(function(data) {
        $.unblockUI();
        if (data.result === true) {
          $("#modalAddShift").modal("hide");
          reloadGrid("#gridShiftTrans");
        } else {
          alert(data.message);
        }
      });
    });

    // add shift
    $("#btnAddShift").on("click", function() {
      $("#modalAddShift").modal({
        backdrop: "static",
      });

      $("#formAddShiftTrans").trigger("reset");

      ajax({
        url: "/sch2/api/shift/all",
        type: "post",
      }).done(function(data) {
        generateDropdown({
          selector: "select[name=Shift]",
          data: data,
          id: "ID",
          value: "Description",
        });
      });

      ajax({
        url: "/sch2/api/module/all",
        type: "post",
      }).done(function(data) {
        generateDropdown({
          selector: "select[name=Module]",
          data: data,
          id: "Id",
          value: "Description",
        });
      });
    });

    // add data
    $("#btnAddData").on("click", function() {
      var rowdata = getRowsSelected("#gridShiftTrans");
      console.log(rowdata);
      if (rowdata.length > 0 && rowdata[0].Status === "Open") {
        ajax({
          url: "/sch2/api/shift_trans/get_by_id",
          type: "post",
          data: {
            id: rowdata[0].Id,
          },
        }).done(function(data) {
          $("#formModule input[name=_shift]").val(data[0].Shift);
          $("#formModule input[name=_date]").val(data[0].ShiftDate);
          $("#formModule input[name=_module]").val(data[0].ModuleId);
          $("#formModule input[name=_transId]").val(data[0].Id);
          $("#formModule").prop("action", "/sch2/module/" + data[0].ModuleId);
          $("#formModule").submit();
        });
      } else {
        alert("ไม่สามารถทำรายการได้");
      }
    });

    // cancel
    $("#btnCancel").on("click", function() {
      if (confirm("Are you sure?")) {
        var rowdata = getRowsSelected("#gridShiftTrans");
        if (rowdata.length > 0) {
          ajax({
            url: "/sch2/api/shift_trans/cancel",
            type: "post",
            data: {
              id: rowdata[0].Id,
            },
          }).done(function(data) {
            console.log(data);
            reloadGrid("#gridShiftTrans");
          });
        } else {
          alert("Please select data.");
        }
      }

    });

    $("#btnReport").on("click", function() {
      var rowdata = getRowsSelected("#gridShiftTrans");
      if (rowdata.length > 0) {
        var dd = dayjs(rowdata[0].ShiftDate).format('YYYY-MM-DD');
        window.open("/sch2/api/target_greentire/report/" + rowdata[0].Id + "/" + dd);
      } else {
        alert("กรุณาเลือกรายการ");
      }
    });

    $("#btnReportExcel").on("click", function() {
      var rowdata = getRowsSelected("#gridShiftTrans");
      if (rowdata.length > 0) {
        var dd = dayjs(rowdata[0].ShiftDate).format('YYYY-MM-DD');
        window.open("/sch2/api/target_greentire/report_excel/" + rowdata[0].Id + "/" + dd);
      } else {
        alert("กรุณาเลือกรายการ");
      }
    });

    $("#btnReportCure").on("click", function() {
      var rowdata = getRowsSelected("#gridShiftTrans");
      if (rowdata.length > 0) {
        var dd = dayjs(rowdata[0].ShiftDate).format('YYYY-MM-DD');
        window.open("/sch2/api/target_greentireCure/report/" + rowdata[0].Id + "/" + dd);
      } else {
        alert("กรุณาเลือกรายการ");
      }
    });

    $("#btnReportCureExcel").on("click", function() {
      var rowdata = getRowsSelected("#gridShiftTrans");
      if (rowdata.length > 0) {
        var dd = dayjs(rowdata[0].ShiftDate).format('YYYY-MM-DD');
        window.open("/sch2/api/target_greentire/reportCure_excel/" + rowdata[0].Id + "/" + dd);
      } else {
        alert("กรุณาเลือกรายการ");
      }
    });

    $("#btnPlan").on("click", function() {
      $("#modalBuild").modal({
        backdrop: "static",
      });
    });

    $('#submitImport').hide();
    $('#submitExport').hide();
    $('#submitClear').hide();

    $("#btnImport").on("click", function() {
      $('#submitImport').show();
      $('#submitExport').hide();
      $('#submitClear').hide();
    });
    $("#btnExport").on("click", function() {
      $('#submitImport').hide();
      $('#submitClear').hide();
      $('#submitExport').show();
    });
    $("#btnClear").on("click", function() {
      $('#submitImport').hide();
      $('#submitExport').hide();
      $('#submitClear').show();
    });
    $("#btnView").on("click", function() {
      $('#submitImport').hide();
      $('#submitExport').hide();
      $('#submitClear').hide();
      window.open("/sch/build");
    });

    // import
    $('#ImportDate').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      todayHighlight: true
    });

    $('#ImportDate').change(function() {
      setTimeout(function() {
        if ($("input[name=ImportShift]:checked").val() == 1) {
          var shift = 1;
        } else {
          var shift = 2;
        }
        var date = $('#ImportDate').val();
        ajax({
          url: "/sch/api/build/import/ckeck",
          type: "post",
          data: {
            date: date,
            shift: shift
          },
        }).done(function(data) {
          if (data.result == true) {
            $('#messageImport').html("<font color='red'>" + data.message + "</font>");
          } else {
            $('#messageImport').html("<font color='green'>" + data.message + "</font>");
          }
        });
      }, 1000);
    });

    $("input[name=ImportShift]").change(function() {
      setTimeout(function() {
        if ($("input[name=ImportShift]:checked").val() == 1) {
          var shift = 1;
        } else {
          var shift = 2;
        }
        var date = $('#ImportDate').val();
        ajax({
          url: "/sch/api/build/import/ckeck",
          type: "post",
          data: {
            date: date,
            shift: shift
          },
        }).done(function(data) {
          if (data.result == true) {
            $('#messageImport').html("<font color='red'>" + data.message + "</font>");
          } else {
            $('#messageImport').html("<font color='green'>" + data.message + "</font>");
          }
        });
      }, 1000);
    });

    $('#ImportDateShow').click(function() {
      $('#ImportDate').datepicker('show');
    });

    // export
    $('#ExportDate').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      todayHighlight: true,
    });

    $('#ExportDateShow').click(function() {
      $('#ExportDate').datepicker('show');
    });

    // clear
    $('#ClearDate').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      todayHighlight: true,
    });

    $('#ClearDateShow').click(function() {
      $('#ClearDate').datepicker('show');
    });

    $('#submitClear').submit(function(e) {
      if (confirm("Are you sure?")) {
        var date = $('#ClearDate').val();
        if ($("input[name=ImportShift]:checked").val() == 1) {
          var shift = 1;
        } else {
          var shift = 2;
        }

        ajax({
          url: "/sch/api/build/clear",
          type: "post",
          data: {
            date: date,
            shift: shift
          },
        }).done(function(data) {
          // console.log(data);
          alert(data.message);
        });
      }
      return false;
    });

    // end
  });
</script>