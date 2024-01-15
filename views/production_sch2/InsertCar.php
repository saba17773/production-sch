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
<BR>
<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_generate_prosch') === true) : ?>
  <button class="btn btn-default" id="btn_reload">
    <span class="glyphicon glyphicon-refresh"></span> Reload Row
  </button>
<?php endif ?>
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

  // function checkgridsch(date_sch,shift) {
  //
  // 	gojax('get', '/sch2/sch/data/checkdisbursement', {
  // 		date_sch  : date_sch,
  //         shift  	  : shift
  // 	}).done(function (data) {
  // 		if (data.result==true) {
  // 			loadgridsch(date_sch,shift);
  // 			$("#grid_sch").show();
  // 			$('#message_checkdata').hide();
  // 		}else{
  // 			if (data.status==3) {
  // 				loadgridsch(date_sch,shift);
  // 				$("#grid_sch").show();
  // 				$('#message_checkdata').show();
  // 				$('#message_checkdata').html('<strong>'+data.message+'</strong>');
  // 			}else{
  // 				$("#grid_sch").hide();
  // 				$('#message_checkdata').show();
  // 				$('#message_checkdata').html('<strong>'+data.message+'</strong>');
  // 			}
  // 		}
  // 	});
  //
  // }

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
        gojax('post', '/sch2/sch/update/UpdateSchDisburTableCar', {
          Car1_1: rowdata.Car1_1,
          Car1_2: rowdata.Car1_2,
          Car1_3: rowdata.Car1_3,
          Car1_4: rowdata.Car1_4,
          Car1_5: rowdata.Car1_5,
          Car1_6: rowdata.Car1_6,
          Car1_7: rowdata.Car1_7,
          Car1_8: rowdata.Car1_8,
          Car2_1: rowdata.Car2_1,
          Car2_2: rowdata.Car2_2,
          Car2_3: rowdata.Car2_3,
          Car2_4: rowdata.Car2_4,
          Car2_5: rowdata.Car2_5,
          Car2_6: rowdata.Car2_6,
          Car2_7: rowdata.Car2_7,
          Car2_8: rowdata.Car2_8,

          CarNumber1_1: rowdata.CarNumber1_1,
          CarNumber1_2: rowdata.CarNumber1_2,
          CarNumber1_3: rowdata.CarNumber1_3,
          CarNumber1_4: rowdata.CarNumber1_4,
          CarNumber1_5: rowdata.CarNumber1_5,
          CarNumber1_6: rowdata.CarNumber1_6,
          CarNumber1_7: rowdata.CarNumber1_7,
          CarNumber1_8: rowdata.CarNumber1_8,
          CarNumber2_1: rowdata.CarNumber2_1,
          CarNumber2_2: rowdata.CarNumber2_2,
          CarNumber2_3: rowdata.CarNumber2_3,
          CarNumber2_4: rowdata.CarNumber2_4,
          CarNumber2_5: rowdata.CarNumber2_5,
          CarNumber2_6: rowdata.CarNumber2_6,
          CarNumber2_7: rowdata.CarNumber2_7,
          CarNumber2_8: rowdata.CarNumber2_8,
          // CountOut 	 : rowdata.CountOut,
          // CountNotSpec	 : rowdata.CountNotSpec,
          // CountReal	 : rowdata.CountReal,
          id: rowdata.ID,
          date_sch: date_sch,
          Shift: shift

        }).done(function(data) {
          if (data.status === 200) {
            // $('#grid_sch').jqxGrid('updateBoundData', 'cells');
            commit(true);
          } else {
            // $('#grid_sch').jqxGrid('updateBoundData', 'cells');
            //alert(data.message);
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
          type: "int"
        },
        {
          name: "Car1_2",
          type: "int"
        },
        {
          name: "Car1_3",
          type: "int"
        },
        {
          name: "Car1_4",
          type: "int"
        },
        {
          name: "Car1_5",
          type: "int"
        },
        {
          name: "Car1_6",
          type: "int"
        },
        {
          name: "Car1_7",
          type: "int"
        },
        {
          name: "Car1_8",
          type: "int"
        },
        {
          name: "Car2_1",
          type: "int"
        },
        {
          name: "Car2_2",
          type: "int"
        },
        {
          name: "Car2_3",
          type: "int"
        },
        {
          name: "Car2_4",
          type: "int"
        },
        {
          name: "Car2_5",
          type: "int"
        },
        {
          name: "Car2_6",
          type: "int"
        },
        {
          name: "Car2_7",
          type: "int"
        },
        {
          name: "Car2_8",
          type: "int"
        },

        {
          name: "CarNumber1_1",
          type: "string"
        },
        {
          name: "CarNumber1_2",
          type: "string"
        },
        {
          name: "CarNumber1_3",
          type: "string"
        },
        {
          name: "CarNumber1_4",
          type: "string"
        },
        {
          name: "CarNumber1_5",
          type: "string"
        },
        {
          name: "CarNumber1_6",
          type: "string"
        },
        {
          name: "CarNumber1_7",
          type: "string"
        },
        {
          name: "CarNumber1_8",
          type: "string"
        },
        {
          name: "CarNumber2_1",
          type: "string"
        },
        {
          name: "CarNumber2_2",
          type: "string"
        },
        {
          name: "CarNumber2_3",
          type: "string"
        },
        {
          name: "CarNumber2_4",
          type: "string"
        },
        {
          name: "CarNumber2_5",
          type: "string"
        },
        {
          name: "CarNumber2_6",
          type: "string"
        },
        {
          name: "CarNumber2_7",
          type: "string"
        },
        {
          name: "CarNumber2_8",
          type: "string"
        }

      ],
      // sortcolumn: 'CurID',
      // sortdirection: 'asc',
      url: '/ProductionGreentireDisburs/sch2/loadtire?date_sch=' + date_sch + '&shift=' + shift
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
      columnsheight: 45,
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
          datafield: "CarNumber1_1",
          align: 'center',
          width: '50',
          columngroup: 'car1',
          editable: true
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car1_1",
          align: 'center',
          width: '60',
          columngroup: 'car1'
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber1_2",
          align: 'center',
          width: '50',
          columngroup: 'car2'
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car1_2",
          align: 'center',
          width: '60',
          columngroup: 'car2'
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber1_3",
          align: 'center',
          width: '50',
          columngroup: 'car3'
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car1_3",
          align: 'center',
          width: '60',
          columngroup: 'car3'
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber1_4",
          align: 'center',
          width: '50',
          columngroup: 'car4'
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car1_4",
          align: 'center',
          width: '60',
          columngroup: 'car4'
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber1_5",
          align: 'center',
          width: '50',
          columngroup: 'car5'
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car1_5",
          align: 'center',
          width: '60',
          columngroup: 'car5'
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber1_6",
          align: 'center',
          width: '50',
          columngroup: 'car6'
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car1_6",
          align: 'center',
          width: '60',
          columngroup: 'car6'
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber1_7",
          align: 'center',
          width: '50',
          columngroup: 'car7'
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car1_7",
          align: 'center',
          width: '60',
          columngroup: 'car7'
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber1_8",
          align: 'center',
          width: '50',
          columngroup: 'car8'
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car1_8",
          align: 'center',
          width: '60',
          columngroup: 'car8'
        },

        {
          text: "เบอร์รถ",
          datafield: "CarNumber2_1",
          align: 'center',
          width: '50',
          columngroup: 'car9',
          editable: true,
          cellclassname: cellclass
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car2_1",
          align: 'center',
          width: '60',
          columngroup: 'car9',
          cellclassname: cellclass
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber2_2",
          align: 'center',
          width: '50',
          columngroup: 'car10',
          cellclassname: cellclass
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car2_2",
          align: 'center',
          width: '60',
          columngroup: 'car10',
          cellclassname: cellclass
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber2_3",
          align: 'center',
          width: '50',
          columngroup: 'car11',
          cellclassname: cellclass
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car2_3",
          align: 'center',
          width: '60',
          columngroup: 'car11',
          cellclassname: cellclass
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber2_4",
          align: 'center',
          width: '50',
          columngroup: 'car12',
          cellclassname: cellclass
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car2_4",
          align: 'center',
          width: '60',
          columngroup: 'car12',
          cellclassname: cellclass
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber2_5",
          align: 'center',
          width: '50',
          columngroup: 'car13',
          cellclassname: cellclass
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car2_5",
          align: 'center',
          width: '60',
          columngroup: 'car13',
          cellclassname: cellclass
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber2_6",
          align: 'center',
          width: '50',
          columngroup: 'car14',
          cellclassname: cellclass
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car2_6",
          align: 'center',
          width: '60',
          columngroup: 'car14',
          cellclassname: cellclass
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber2_7",
          align: 'center',
          width: '50',
          columngroup: 'car15',
          cellclassname: cellclass
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car2_7",
          align: 'center',
          width: '60',
          columngroup: 'car15',
          cellclassname: cellclass
        },
        {
          text: "เบอร์รถ",
          datafield: "CarNumber2_8",
          align: 'center',
          width: '50',
          columngroup: 'car16',
          cellclassname: cellclass
        },
        {
          text: "จำนวน<BR>เส้น",
          datafield: "Car2_8",
          align: 'center',
          width: '60',
          columngroup: 'car16',
          cellclassname: cellclass
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
        alert(data.message);
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