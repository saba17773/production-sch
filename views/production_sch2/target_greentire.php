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
      <button class="btn btn-primary" style="display: none;" id="btnAddGreentire">
        <i class="fa fa-plus"></i> เพิ่มรายการ
      </button>
      <button class="btn btn-danger" style="display: none;" id="btnDeleteGreentire">
        <i class="fa fa-times"></i> ลบรายการ
      </button>
      <button class="btn btn-default" id="btnAddData"><i class="fa fa-pencil"></i> เบิกให้ เบิกใช้ หน้าเตา</button>
      <input type="hidden" value="<?php echo $date; ?>" id="date1" name="date1">
    </div>
    <table id="gridTargetGreentire" class="mb-2 row-border nowrap" style="width:100%;">
      <thead>
        <tr>
          <th colspan="6" class="text-center" style="padding: 10px;">
            <?php echo getThaiDate($date); ?>
          </th>
          <th colspan="2">BOM <?php echo $shift1; ?></th>
          <th colspan="2">BOM <?php echo $shift2; ?></th>
          <th colspan="2">รวม</th>
        </tr>
        <tr>
          <th rowspan="2">No.</th>
          <th rowspan="2">Item Id</th>
          <th rowspan="2">Size</th>
          <!-- <th rowspan="2">PR</th> -->
          <!-- <th rowspan="2">Code</th> -->
          <!-- <th rowspan="2">Pattern</th> -->
          <th rowspan="2">T/T<br />T/L</th>
          <th rowspan="2">Color</th>
          <th rowspan="2">Weight</th>
          <th colspan="2">รวม BRAND</th>
          <th colspan="2">รวม BRAND</th>
          <th colspan="2">C+D</th>
        </tr>
        <tr>
          <th>เป้าผลิต</th>
          <th>ผลิตได้</th>
          <th>เป้าผลิต</th>
          <th>ผลิตได้</th>
          <th>น้ำหนักเป้าหมาย</th>
          <th>น้ำหนักผลิต</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<div class="modal" id="modalAddGreentire" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button href="javascript.void(0);" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i>
        </button>
        <h4 class="modal-title">Add Greentire</h4>
      </div>
      <div class="modal-body">
        <form id="formAddGreentire" class="mb-2">
          <div class="form-group" style="margin-right: 10px;">
            <label for="">Shift Id</label>
            <input type="text" name="shift_id" value="<?php echo $transId; ?>" required readonly class="form-control" />
          </div>
          <div class="form-group" style="margin-right: 10px;">
            <label for="">Item Greentire</label>
            <span class="label label-primary" id="selectGreentire">เลือก Item</span>
            <input type="text" name="greentire_id" readonly class="form-control" required />
          </div>
          <div class="form-group" style="margin-right: 10px;">
            <label for="">BOM C Plan</label>
            <input type="text" name="bomo_c_plan" class="form-control" required />
          </div>
          <div class="form-group" style="margin-right: 10px;">
            <label for="">BOM D Plan</label>
            <input type="text" name="bomo_d_plan" class="form-control" required />
          </div>
          <div class="form-group" style="margin-right: 10px;">
            <label for="">Weight Plan</label>
            <input type="number" step="0.001" name="weight_plan" class="form-control" required readonly />
          </div>
          <div class="form-group">
            <input type="hidden" name="shift_date" id="shift_date" value="<?php echo date("Y-m-d", strtotime($date)); ?>">
            <button class="btn btn-success" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="modalSelectItem" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button href="javascript.void(0);" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i>
        </button>
        <h4 class="modal-title">Select Greentire</h4>
      </div>
      <div class="modal-body scroll-x">
        <div class="mb-2">
          <button class="btn btn-primary" id="btnSelectItem">เลือก Item</button>
        </div>
        <table id="gridGreentireMaster" class="mb-2 table table-striped table-bordered table-hover table-condensed table-nowrap" style="width:100%;">
          <thead>
            <tr>
              <th>Item Id</th>
              <th>Item Name</th>
              <th>PR</th>
              <th>Pattern</th>
              <th>Color</th>
              <th>TT</th>
              <th>Weight</th>
            </tr>
            <tr>
              <th>Item Id</th>
              <th>Item Name</th>
              <th>PR</th>
              <th>Pattern</th>
              <th>Color</th>
              <th>TT</th>
              <th>Weight</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- form submit input value -->
<form id="formModule" method="post">

  <input type="hidden" name="_date" />

</form>

<script>
  $(document).ready(function() {

    $.blockUI();

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
        url: "/sch2/api/target_greentire/<?php echo date("Y-m-d", strtotime($date)); ?>",
        method: "post",
      },
      initComplete: function() {
        $.unblockUI();
      },
      fnDrawCallback: function(settings, json) {

        // // bom C actual
        // $("#gridTargetGreentire .--bomc-actual").editable({
        //   mode: "inline",
        //   showbuttons: false,
        //   type: "text",
        //   name: "BomCActual",
        //   url: "/sch2/api/target_greentire/update",
        //   success: function(data) {
        //     reloadGrid("#gridTargetGreentire", true);
        //     if (data.Result === false) alert(data.Message);
        //   },
        //   error: function(err) {
        //     alert(err.responseText);
        //   },
        // });

        // // bom d actual
        // $("#gridTargetGreentire .--bomd-actual").editable({
        //   mode: "inline",
        //   showbuttons: false,
        //   type: "text",
        //   name: "BomDActual",
        //   url: "/sch2/api/target_greentire/update",
        //   success: function(data) {
        //     reloadGrid("#gridTargetGreentire", true);
        //     if (data.Result === false) alert(data.Message);
        //   },
        //   error: function(err) {
        //     alert(err.responseText);
        //   },
        // });

        // // weight actual
        // $("#gridTargetGreentire .--weight-actual").editable({
        //   mode: "inline",
        //   showbuttons: false,
        //   type: "text",
        //   name: "WeightActual",
        //   url: "/sch2/api/target_greentire/update",
        //   success: function(data) {
        //     reloadGrid("#gridTargetGreentire", true);
        //     if (data.Result === false) alert(data.Message);
        //   },
        //   error: function(err) {
        //     alert(err.responseText);
        //   },
        // });
      },
      columnDefs: [{
          render: function(data, type, row) {
            var color = serializeColor(row.ColorAll);
            return color.substring(0, color.length - 1);
          },
          targets: 4,
        },
        {
          render: function(data, type, row) {
            // if (row.BomCPlan > 0) {
            //   return editable({
            //     className: "--bomc-actual",
            //     id: row.TransId,
            //     data: data,
            //   });
            // } else {
            //   return data === null ? 0 : data;
            // }
            return data === null ? 0 : data;
          },
          targets: 7,
        },
        {
          render: function(data, type, row) {
            // if (row.BomDPlan > 0) {
            //   return editable({
            //     className: "--bomd-actual",
            //     id: row.TransId,
            //     data: data,
            //   });
            // } else {
            //   return data === null ? 0 : data;
            // }
            return data === null ? 0 : data;
          },
          targets: 9,
        },
        {
          render: function(data, type, row) {
            if (data === "" || data === null || data === ".00") {
              return 0;
            } else {
              // return data;
              return addSeparatorsNF(data, ',', '.', ',');
            }
          },
          targets: 10,
        },
        {
          render: function(data, type, row) {
            if (data === "" || data === null || data === ".00") {
              return 0;
            } else {
              // return data;
              return addSeparatorsNF(data, ',', '.', ',');
            }
          },
          targets: 11,
        },
      ],
      columns: [{
          data: "Id",
        },
        {
          data: "ItemId"
        },
        {
          data: "ItemGTName",
        },
        // {
        //   data: "PR"
        // },
        // {
        //   data: "Code"
        // },
        // {
        //   data: "Pattern"
        // },
        {
          data: "TT"
        },
        {
          data: "Color"
        },
        {
          data: "Weight"
        },
        {
          data: "BomCPlan"
        },
        {
          data: "BomCActual"
        },
        {
          data: "BomDPlan"
        },
        {
          data: "BomDActual"
        },
        {
          data: "WeightPlan"
        },
        {
          data: "WeightActual"
        },
      ],
    });

    $("#btnDeleteGreentire").on("click", function() {
      if (confirm("คุณต้องการจะลบรายการนี้ใช่หรือไม่?")) {
        var rowdata = getRowsSelected("#gridTargetGreentire");
        if (rowdata.length > 0) {
          $.ajax({
            url: "/sch2/api/target_greentire/delete",
            type: "post",
            dataType: "json",
            data: {
              id: rowdata[0].TransId
            }
          }).done(function(data) {
            reloadGrid("#gridTargetGreentire");
          });
        } else {
          alert("กรุณาเลือกรายการ");
        }
      }
    });

    $("#btnAddGreentire").on("click", function() {
      $("#modalAddGreentire").modal({
        backdrop: "static"
      });

      $("#formAddGreentire").trigger("reset");
    });

    $("#formAddGreentire").on("submit", function(e) {
      e.preventDefault();
      $.ajax({
        url: "/sch2/api/target_greentire/add",
        type: "post",
        dataType: "json",
        data: {
          shift_id: $("input[name=shift_id]").val(),
          greentire_id: $("input[name=greentire_id]").val(),
          bomo_c_plan: $("input[name=bomo_c_plan]").val(),
          bomo_d_plan: $("input[name=bomo_d_plan]").val(),
          weight_plan: $("input[name=weight_plan]").val(),
          shift_date: $("input[name=shift_date]").val()
        }
      }).done(function(data) {
        if (data.result === true) {
          $("#modalAddGreentire").modal("hide");
          reloadGrid("#gridTargetGreentire");
        } else {
          alert(data.message);
        }
      });
    });

    $("#selectGreentire").on("click", function() {

      $("#modalSelectItem").modal({
        backdrop: "static"
      });

      loadGrid({
        el: "#gridGreentireMaster",
        processing: true,
        serverSide: true,
        deferRender: true,
        searching: true,
        ordering: false,
        order: [],
        orderCellsTop: true,
        destroy: true,
        paging: true,
        select: {
          style: "single",
        },
        ajax: {
          url: "/sch2/api/target_greentire/master",
          method: "post",
        },
        columns: [{
            data: "ItemGT",
          },
          {
            data: "ItemGTName"
          },
          {
            data: "PR"
          },
          {
            data: "Pattern"
          },
          {
            data: "Color"
          },
          {
            data: "TT"
          },
          {
            data: "Weight"
          }
        ],
        columnDefs: [{
            render: function(data, type, row) {
              var color = serializeColor(row.Color) + serializeColor(row.Color2) + serializeColor(row.Color3) + serializeColor(row.Color4) + serializeColor(row.Color5);
              return color.substring(0, color.length - 1);
            },
            targets: 4,
          },
          {
            render: function(data, type, row) {
              return data / 1000;
            },
            targets: 6,
          }
        ],
      });
    });

    $("#btnSelectItem").on("click", function() {
      var rowdata = getRowsSelected("#gridGreentireMaster");
      if (rowdata.length > 0) {
        $("input[name=greentire_id]").val(rowdata[0].ItemGT);
        $("input[name=weight_plan]").val(rowdata[0].Weight);
        $("#modalSelectItem").modal("hide");
      } else {
        alert("กรุณาเลือกรายการ");
      }
    });
    $("#btnAddData").on("click", function() {


      $dateser = $("input[name=date1]").val();
      var str = $dateser;
      var res = str.split(" ");

      // $("#formModule input[name=_shift]").val(data[0].Shift);
      $("#formModule input[name=_date]").val(res);
      $("#formModule").prop("action", "/sch2/module/checkbill");
      $("#formModule").submit();
    });



  }); // end

  function serializeColor(color) {
    if (color === null || color === "") {
      return "";
    } else {
      return color + "/";
    }

  }

  function addSeparatorsNF(nStr, inD, outD, sep) {
    nStr += '';
    var dpos = nStr.indexOf(inD);
    var nStrEnd = '';
    if (dpos != -1) {
      nStrEnd = outD + nStr.substring(dpos + 1, nStr.length);
      nStr = nStr.substring(0, dpos);
    }
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(nStr)) {
      nStr = nStr.replace(rgx, '$1' + sep + '$2');
    }
    return nStr + nStrEnd;
  }
</script>