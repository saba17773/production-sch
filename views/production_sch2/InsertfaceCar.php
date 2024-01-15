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


<BR>
<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_generate_prosch') === true) : ?>
  <button class="btn btn-default" id="btn_reload">
    <span class="glyphicon glyphicon-refresh"></span> Reload Row
  </button>
<?php endif ?>


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

    $("#btn_reload").on("click", function() {
      $('#grid_sch').jqxGrid('updateBoundData', 'cells');
    });












  });



  function loadgridsch(date_sch, shift) {

    if (shift == 1) {
      var datashift = "แผนสร้าง<BR>กะกลางวัน (เส้น)";
      var dataplan = "ผลิตได้<BR>กะกลางวัน (เส้น)";
    } else {
      var datashift = "แผนสร้าง<BR>กะกลางคืน (เส้น)";
      var dataplan = "ผลิตได้<BR>กะกลางคืน (เส้น)";
    }


    var dataAdapter = new $.jqx.dataAdapter({
      datatype: "json",
      updaterow: function(rowid, rowdata, commit) {
        gojax('post', '/sch2/sch/update/UpdateSchFacetireTableCar', {
          Car1_1: rowdata.Car1_1,
          Car1_2: rowdata.Car1_2,
          Car1_3: rowdata.Car1_3,
          Car1_4: rowdata.Car1_4,
          Car1_5: rowdata.Car1_5,
          Car1_6: rowdata.Car1_6,
          Car1_7: rowdata.Car1_7,
          Car1_8: rowdata.Car1_8,
          CarNumber1_1: rowdata.CarNumber1_1,
          CarNumber1_2: rowdata.CarNumber1_2,
          CarNumber1_3: rowdata.CarNumber1_3,
          CarNumber1_4: rowdata.CarNumber1_4,
          CarNumber1_5: rowdata.CarNumber1_5,
          CarNumber1_6: rowdata.CarNumber1_6,
          CarNumber1_7: rowdata.CarNumber1_7,
          CarNumber1_8: rowdata.CarNumber1_8,
          Pay2_1: rowdata.Pay2_1,
          Pay2_2: rowdata.Pay2_2,
          Pay2_3: rowdata.Pay2_3,
          Pay2_4: rowdata.Pay2_4,
          Pay2_5: rowdata.Pay2_5,
          Pay2_6: rowdata.Pay2_6,
          Pay2_7: rowdata.Pay2_7,
          Pay2_8: rowdata.Pay2_8,
          id: rowdata.ID,
          date_sch: date_sch,
          shift: shift

        }).done(function(data) {
          if (data.status === 200) {
            //$('#grid_sch').jqxGrid('updateBoundData', 'cells');
            commit(true);
          } else {
            //  $('#grid_sch').jqxGrid('updateBoundData', 'cells');
          }
          console.log(data);
        }).fail(function() {
          commit(false);
        });


      },
      datafields: [
        // { name: "ID", type: "int" },
        // { name: "Boiler", type: "string" },
        // { name: "BoilerName", type: "string"},
        // { name: "Employee", type: "int" },
        {
          name: "ID",
          type: "int"
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
          name: "Color",
          type: "string"
        },
        {
          name: "Car1_1",
          type: "strng"
        },
        {
          name: "Car1_2",
          type: "strng"
        },
        {
          name: "Car1_3",
          type: "strng"
        },
        {
          name: "Car1_4",
          type: "strng"
        },
        {
          name: "Car1_5",
          type: "strng"
        },
        {
          name: "Car1_6",
          type: "strng"
        },
        {
          name: "Car1_7",
          type: "strng"
        },
        {
          name: "Car1_8",
          type: "strng"
        },
        {
          name: "CarNumber1_1",
          type: "int"
        },
        {
          name: "CarNumber1_2",
          type: "int"
        },
        {
          name: "CarNumber1_3",
          type: "int"
        },
        {
          name: "CarNumber1_4",
          type: "int"
        },
        {
          name: "CarNumber1_5",
          type: "int"
        },
        {
          name: "CarNumber1_6",
          type: "int"
        },
        {
          name: "CarNumber1_7",
          type: "int"
        },
        {
          name: "CarNumber1_8",
          type: "int"
        },

        {
          name: "Pay2_1",
          type: "int"
        },
        {
          name: "Pay2_2",
          type: "int"
        },
        {
          name: "Pay2_3",
          type: "int"
        },
        {
          name: "Pay2_4",
          type: "int"
        },
        {
          name: "Pay2_5",
          type: "int"
        },
        {
          name: "Pay2_6",
          type: "int"
        },
        {
          name: "Pay2_7",
          type: "int"
        },
        {
          name: "Pay2_8",
          type: "int"
        }


      ],
      // sortcolumn: 'CurID',
      // sortdirection: 'asc',
      url: '/productionfacetire/sch2/loadfacetire?date_sch=' + date_sch + '&shift=' + shift
    });

    var cellclass = function(row, columnfield, value) {

      // if (value === null || value === "") {
      //   x= 0;
      //  return 'white';
      //
      // }
      //  else if (value !== x && value !== null && value !== "") {
      //   x = value;
      //   if(color === 'sky' ){
      color = 'blue'
      //   }
      //   else {
      //       color = 'sky'
      //   }
      return color;
      //  }
      //
      //  else return color;
      //alert(value);

    };

    return $("#grid_sch").jqxGrid({
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

      columns: [

        //{ text:"Id", datafield: "BoilerName", align: 'center', width:'8%', hidden: true, editable:false},
        {
          text: "No.",
          width: 50,
          cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
            return '<div style=\'padding: 5px; color:#000000;\'> ' + (index + 1) + ' </div>';
          }
        },
        {
          text: "Item Id",
          datafield: "ItemID",
          align: 'center',
          width: '100',
          editable: false
        },
        {
          text: "Item Name",
          datafield: "ItemName",
          align: 'center',
          width: '400',
          editable: false
        },
        {
          text: "Color",
          datafield: "Color",
          align: 'center',
          width: '150'
        },
        {
          text: "เบอร์รถ",
          datafield: "Car1_1",
          align: 'center',
          width: '50',
          columngroup: 'car1',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "CarNumber1_1",
          align: 'center',
          width: '60',
          columngroup: 'car1'
        },
        {
          text: "จ่ายออก",
          datafield: "Pay2_1",
          align: 'center',
          width: '50',
          columngroup: 'car1'
        },
        {
          text: "เบอร์รถ",
          datafield: "Car1_2",
          align: 'center',
          width: '50',
          columngroup: 'car2',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "CarNumber1_2",
          align: 'center',
          width: '60',
          columngroup: 'car2'
        },
        {
          text: "จ่ายออก",
          datafield: "Pay2_2",
          align: 'center',
          width: '50',
          columngroup: 'car2'
        },
        {
          text: "เบอร์รถ",
          datafield: "Car1_3",
          align: 'center',
          width: '50',
          columngroup: 'car3',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "CarNumber1_3",
          align: 'center',
          width: '60',
          columngroup: 'car3'
        },
        {
          text: "จ่ายออก",
          datafield: "Pay2_3",
          align: 'center',
          width: '50',
          columngroup: 'car3'
        },
        {
          text: "เบอร์รถ",
          datafield: "Car1_4",
          align: 'center',
          width: '50',
          columngroup: 'car4',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "CarNumber1_4",
          align: 'center',
          width: '60',
          columngroup: 'car4'
        },
        {
          text: "จ่ายออก",
          datafield: "Pay2_4",
          align: 'center',
          width: '50',
          columngroup: 'car4'
        },
        {
          text: "เบอร์รถ",
          datafield: "Car1_5",
          align: 'center',
          width: '50',
          columngroup: 'car5',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "CarNumber1_5",
          align: 'center',
          width: '60',
          columngroup: 'car5'
        },
        {
          text: "จ่ายออก",
          datafield: "Pay2_5",
          align: 'center',
          width: '50',
          columngroup: 'car5'
        },
        {
          text: "เบอร์รถ",
          datafield: "Car1_6",
          align: 'center',
          width: '50',
          columngroup: 'car6',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "CarNumber1_6",
          align: 'center',
          width: '60',
          columngroup: 'car6'
        },
        {
          text: "จ่ายออก",
          datafield: "Pay2_6",
          align: 'center',
          width: '50',
          columngroup: 'car6'
        },
        {
          text: "เบอร์รถ",
          datafield: "Car1_7",
          align: 'center',
          width: '50',
          columngroup: 'car7',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "CarNumber1_7",
          align: 'center',
          width: '60',
          columngroup: 'car7'
        },
        {
          text: "จ่ายออก",
          datafield: "Pay2_7",
          align: 'center',
          width: '50',
          columngroup: 'car7'
        },
        {
          text: "เบอร์รถ",
          datafield: "Car1_8",
          align: 'center',
          width: '50',
          columngroup: 'car8',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "CarNumber1_8",
          align: 'center',
          width: '60',
          columngroup: 'car8'
        },
        {
          text: "จ่ายออก",
          datafield: "Pay2_8",
          align: 'center',
          width: '50',
          columngroup: 'car8'
        }

        //
        // { text:"เบอร์รถ", datafield: "CarNumber2_1",  align: 'center', width:'50',columngroup:'car9', editable:true,cellclassname: cellclass},
        // { text:"จำนวน<BR>เส้น",datafield: "Car2_1", align: 'center', width:'60',columngroup:'car9',cellclassname: cellclass},
        // { text:"เบอร์รถ",datafield: "CarNumber2_2",  align: 'center', width:'50',columngroup:'car10',cellclassname: cellclass},
        // { text:"จำนวน<BR>เส้น",datafield: "Car2_2", align: 'center', width:'60',columngroup:'car10',cellclassname: cellclass},
        // { text:"เบอร์รถ", datafield: "CarNumber2_3",  align: 'center', width:'50',columngroup:'car11',cellclassname: cellclass},
        // { text:"จำนวน<BR>เส้น",datafield: "Car2_3", align: 'center', width:'60',columngroup:'car11',cellclassname: cellclass},
        // { text:"เบอร์รถ",datafield: "CarNumber2_4",  align: 'center', width:'50',columngroup:'car12',cellclassname: cellclass},
        // { text:"จำนวน<BR>เส้น",datafield: "Car2_4", align: 'center', width:'60',columngroup:'car12',cellclassname: cellclass},
        // { text:"เบอร์รถ",datafield: "CarNumber2_5",  align: 'center', width:'50',columngroup:'car13',cellclassname: cellclass},
        // { text:"จำนวน<BR>เส้น",datafield: "Car2_5", align: 'center', width:'60',columngroup:'car13',cellclassname: cellclass},
        // { text:"เบอร์รถ",datafield: "CarNumber2_6",  align: 'center', width:'50',columngroup:'car14',cellclassname: cellclass},
        // { text:"จำนวน<BR>เส้น",datafield: "Car2_6", align: 'center', width:'60',columngroup:'car14',cellclassname: cellclass},
        // { text:"เบอร์รถ",datafield: "CarNumber2_7",  align: 'center', width:'50',columngroup:'car15',cellclassname: cellclass},
        // { text:"จำนวน<BR>เส้น",datafield: "Car2_7", align: 'center', width:'60',columngroup:'car15',cellclassname: cellclass},
        // { text:"เบอร์รถ",datafield: "CarNumber2_8",  align: 'center', width:'50',columngroup:'car16',cellclassname: cellclass},
        // { text:"จำนวน<BR>เส้น",datafield: "Car2_8", align: 'center', width:'60',columngroup:'car16',cellclassname: cellclass}



      ],
      columnGroups: [{
          text: 'คันที่1',
          align: 'center',
          name: 'car1'
        },
        {
          text: 'คันที่2',
          align: 'center',
          name: 'car2'
        },
        {
          text: 'คันที่3',
          align: 'center',
          name: 'car3'
        },
        {
          text: 'คันที่4',
          align: 'center',
          name: 'car4'
        },
        {
          text: 'คันที่5',
          align: 'center',
          name: 'car5'
        },
        {
          text: 'คันที่6',
          align: 'center',
          name: 'car6'
        },
        {
          text: 'คันที่7',
          align: 'center',
          name: 'car7'
        },
        {
          text: 'คันที่8',
          align: 'center',
          name: 'car8'
        }


      ]

    });
  }





  function setItemModal(row) {
    var transid = $("#grid_sch").jqxGrid('getCellValue', row, 'ID');
    //alert(transid);
    // var boiler  = $("#grid_sch").jqxDataTable('getCellValue', row, 'Boiler');
    // var statusid= $("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
    $('#id_trans').val(transid);
    //	if (statusid==1) {
    // loadgriditem(boiler);
    // $('#grid_item').jqxGrid('clearselection');

    $('#modal_item').modal({
      backdrop: 'static'
    });

    var dataAdapter = new $.jqx.dataAdapter({
      datatype: "json",
      datafields: [{
          name: "ItemGT",
          type: "string"
        },
        {
          name: "ItemGTName",
          type: "string"
        },

      ],
      url: '/sch2/sch2/load/item'
    });

    return $("#grid_item").jqxGrid({
      width: '100%',
      source: dataAdapter,
      autoheight: true,
      columnsresize: true,
      pageable: true,
      filterable: true,
      showfilterrow: true,
      pagesize: 20,
      columns: [{
          text: "Item Id",
          datafield: "ItemGT",
          align: 'center',
          width: '15%'
        },
        {
          text: "Name",
          datafield: "ItemGTName",
          align: 'center'
        }

      ]
    });

    //	}

  }

  function setDelete(row) {
    var transid = $("#grid_sch").jqxGrid('getCellValue', row, 'ID');
    // var statusid= $("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
    // if (statusid==1) {
    gojax('post', '/sch2/sch/delete/itemdisbursement', {
      id: transid
    }).done(function(data) {
      if (data.result == 200) {
        $('#grid_sch').jqxGrid('updateBoundData', 'cells');
        // console.log(data);
      } else {
        alert(12345);
      }
    });
    // }

  }

  // function setBoilerAdd(boiler) {
  // 	var user = 'user';
  //     gojax('post', '/production/sch/add/sch', {
  //         	boiler  : boiler,
  //         	date_sch: $("#date_sch").val(),
  //         	shift   : $("input[name=shift]:checked").val(),
  //         	type 	: user
  //       }).done(function(data) {
  //       	if (data.result==200) {
  //       		$('#grid_sch').jqxDataTable('updateBoundData','cells');
  //       		// console.log(data);
  //       	}else{
  //       		// console.log(data);
  //       	}
  //       });
  // }

  function gen_sch(date_gen, gen_shift, copy, date_sch, shift, gen_emp, date_emp, shift_emp) {
    gojax('post', '/productionRecive/sch2/gen/schbilltire', {
      date_gen: date_gen,
      gen_shift: gen_shift,
      date_sch: date_sch,
      shift: shift,
      copy: copy,
      gen_emp: gen_emp,
      date_emp: date_emp,
      shift_emp: shift_emp
    }).done(function(data) {
      console.log(data);
      if (data.result == 200) {
        loadgridsch(date_sch, shift);
        $('#message_checkdata').hide();
        $('#modal_generate').modal('hide');
        $("#grid_sch").show();
        $('#grid_sch').jqxGrid('updatebounddata');
        $('#btn_generate_all').attr('disabled', false);
        $('#btn_generate_item').attr('disabled', false);
      } else {
        //alert(data.message);
        $('#modal_generate').modal('hide');
        $('#grid_sch').jqxGrid('updatebounddata');
        $('#btn_generate_all').attr('disabled', false);
        $('#btn_generate_item').attr('disabled', false);
      }
    });
  }



  function formatDate(date) {
    var d = new Date(date),
      month = '' + (d.getMonth() + 1),
      day = '' + d.getDate(),
      year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [day, month, year].join('-');
  }
</script>