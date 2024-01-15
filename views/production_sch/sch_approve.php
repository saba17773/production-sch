<?php 
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>
<style type="text/css">
  td { 
    padding: 5px;
    text-align: left;
  }
</style>

<!-- dialog employee -->
<div class="modal" id="modal_employee" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
		
        <button type="button" class="btn btn-success pull-right" id="btn_chooseEmployee">
        <span class="glyphicon glyphicon-floppy-saved"></span> เลือก</button>
		
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> ปิด</button>

        <h4 class="modal-title">รายชื่อพนักงาน</h4>
      </div>
      <div class="modal-body">
		<form id="form_employee">
	        <div class="form-group">
	          <input type="hidden" name="id_boiler" id="id_boiler">
	          <input type="hidden" name="id_mold" id="id_mold">
	          <input type="hidden" name="date_sch" id="date_sch">
	          <input type="hidden" name="shift" id="shift">
	          <div id="grid_employee"></div>
	        </div>
      	</form>
      </div>
    </div>
  </div>
</div> 

<!-- dialog item -->
<div class="modal" id="modal_item" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="glyphicon glyphicon-remove-circle"></span>
        </button>
        <h4 class="modal-title">รายการขนาดพิมพ์</h4>
      </div>

      <div class="modal-body">
		<form id="form_item">
	        <div class="form-group">
	          <input type="hidden" name="itemid" id="itemid">
	          <input type="hidden" name="totalcure" id="totalcure">
	          <input type="hidden" name="ratecure" id="ratecure">
	          <input type="hidden" name="netweight" id="netweight">
	          <input type="hidden" name="id_trans" id="id_trans">
	          <div id="grid_item"></div>
	        </div>
      	</form>
      </div>
    </div>
  </div>
</div> 

<!-- dialog remark -->
<div class="modal" id="modal_remark" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        
        <button type="button" class="btn btn-success pull-right" id="btn_chooseRemark">
        <span class="glyphicon glyphicon-floppy-saved"></span> เลือก</button>
		
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> ปิด</button>

        <h4 class="modal-title">หมายเหตุ</h4>
      </div>
      <div class="modal-body">
		<form id="form_remark">
	        <div class="form-group">
	          <input type="hidden" name="id_boiler" id="id_boiler">
	          <input type="hidden" name="id_mold" id="id_mold">
	          <input type="hidden" name="date_sch" id="date_sch">
	          <input type="hidden" name="shift" id="shift">
	          <div id="grid_remark"></div>
	        </div>
      	</form>
      </div>
    </div>
  </div>
</div> 

<!-- dialog listboiler -->
<div class="modal" id="modal_listboiler" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">

        <button type="button" class="btn btn-success pull-right" id="btn_chooseBoiler">
        <span class="glyphicon glyphicon-floppy-saved"></span> เลือก</button>
		
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> ปิด</button>

        <h4 class="modal-title">รายการเตา</h4>
      </div>

      <div class="modal-body">
		<form id="form_listboiler">
	        <div class="form-group">
	          <div id="grid_listboiler"></div>
	        </div>
      	</form>
      </div>
    </div>
  </div>
</div> 

<h4>Approve<br></h4>

<table>
	<tr>
		<td>
			<div id="grid_date"></div>
		</td>
		<td valign="top" align="left">
			<button class="btn btn-info" id="detail" style="width: 130px;"><span class="glyphicon glyphicon-list-alt"></span> รายละเอียด</button><br><br>
			<button class="btn btn-info" id="add_detail" style="width: 130px;"><span class="glyphicon glyphicon-plus"></span> เพิ่มเตา</button><br><br>
			<button class="btn btn-success" id="btn_complete" style="width: 130px;"><span class="glyphicon glyphicon-ok"></span> อนุมัติ </button>
		</td>
		<td valign="top" align="left">
			<b><div id="txtcontent"></div></b>
		</td>
	</tr>
</table>
<hr>

<div id="grid_sch"></div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		loadgriddate();
		$('#detail').hide();
		$('#add_detail').hide();
		$('#btn_complete').hide();
		$('#grid_date').on('rowclick', function (event) {
			$('#detail').show();
			// $('#btn_complete').hide();
		});

		$('#detail').on('click', function (event) {
			var rows = $('#grid_date').jqxGrid('getselectedrowindexes');
			var datarow = $('#grid_date').jqxGrid('getrowdata', rows);
			var datesch = formatDate(datarow.SchDate);
			var shift = datarow.Shift;
  			var status = datarow.Status;
			loadgridsch(datesch,shift,status);
			$('#detail').html('<span class="glyphicon glyphicon-list-alt"> กำลังโหลด...</span>');
  			$('#detail').attr('disabled', true);
  			if (status==1) {
  				$('#add_detail').show();
  			}else{
  				$('#add_detail').hide();
  			}
		});

		$('#btn_chooseEmployee').on('click', function (event){
			var rowdata = row_selected("#grid_employee");
			var rows_selected = [];
			var row_employee = '';
			if (typeof rowdata !== "undefined") {
				var rows = $('#grid_employee').jqxGrid('getselectedrowindexes');
				
				for (var i = 0; i < rows.length; i++) {
					row_employee = $('#grid_employee').jqxGrid('getrowdata', rows[i]);
					rows_selected.push(row_employee.Code);
				}

		        gojax('post', '/production/sch/add/employee', {
		          boiler 	: $('#id_boiler').val(),
		          date_sch  : $("#date_sch").val(),
		          shift 	: $("#shift").val(),
		          mold 	    : $("#id_mold").val(),
		          code  	: rows_selected
		        }).done(function(data) {
		        	if (data.result==200) {
		        		$('#modal_employee').modal('hide');
		        		$('#grid_sch').jqxDataTable('updateBoundData','cells');
		        	}else{
		        		console.log(data);
		        	}
		        });

			} else {
				alert("กรุณาเลือกข้อมูล");
			}
			return false;
		});

		$('#grid_item').on('rowdoubleclick', function (event){
	        var args = event.args;
	        var boundIndex = args.rowindex;        
	        var datarowItem = $("#grid_item").jqxGrid('getrowdata', boundIndex);        
	        
	       	$('#itemid').val(datarowItem.ID);
	       	$('#totalcure').val(720);
	       	$('#ratecure').val(datarowItem.RateCure);
	       	$('#netweight').val(datarowItem.NetWeight);
	       	$('#id_trans').val();
	        if (!!datarowItem.ID) {
		        gojax('post', '/production/sch/add/item', {
		          itemid  	 : $('#itemid').val(),
		          totalcure  : $('#totalcure').val(),
		          ratecure   : $('#ratecure').val(),
		          netweight  : $('#netweight').val(),
		          id      	 : $('#id_trans').val()
		        }).done(function(data) {
		        	if (data.result==200) {
		        		$('#modal_item').modal('hide');
		        		$('#grid_sch').jqxDataTable('updateBoundData','cells');
		        	}else{
		        		console.log(data);
		        	}
		        });
	        }
	        // alert(datarowItem.Itemid);

	    }); 

	    $('#btn_chooseRemark').on('click', function (event){
			var rowdata = row_selected("#grid_remark");
			var rows_selected = [];
			var row_remark = '';
			if (typeof rowdata !== "undefined") {
				var rows = $('#grid_remark').jqxGrid('getselectedrowindexes');
				
				for (var i = 0; i < rows.length; i++) {
					row_remark = $('#grid_remark').jqxGrid('getrowdata', rows[i]);
					rows_selected.push(row_remark.ProblemID);
				}
				
		        gojax('post', '/production/sch/add/remark', {
		          boiler 	: $('#id_boiler').val(),
		          date_sch  : $("#date_sch").val(),
		          shift 	: $("#shift").val(),
		          mold 	    : $("#id_mold").val(),
		          code  	: rows_selected
		        }).done(function(data) {
		        	if (data.result==200) {
		        		$('#modal_remark').modal('hide');
		        		$('#grid_sch').jqxDataTable('updateBoundData','cells');
		        	}else{
		        		console.log(data);
		        	}
		        });

			} else {
				alert("กรุณาเลือกข้อมูล");
			}
			return false;
		});

		$("#btn_complete").on('click',function(event) {

  			var rows = $('#grid_date').jqxGrid('getselectedrowindexes');
			var datarow = $('#grid_date').jqxGrid('getrowdata', rows);
			var datesch = formatDate(datarow.SchDate);
			var shift = datarow.Shift;
  			var status = datarow.Status;

  			if (confirm('คุณต้องการจะอนุมัติ ?\n วันที่ '+datesch+' กะ'+datarow.ShiftName+'')) {
  				
  				if (status==1) {
  					var datesch_com = $("#grid_sch").jqxDataTable('getCellValue', 1, 'SchDate');
  					var shift_com = $("#grid_sch").jqxDataTable('getCellValue', 1, 'Shift');
  					// alert(formatDate(datesch_com)+','+shift_com);
  					$('#btn_complete').html('<span class="glyphicon glyphicon-ok"> กำลังอนุมัติ...</span>');
  					$('#btn_complete').attr('disabled', true);

			    	gojax('post', '/production/sch/complete/sch', {
			          date_sch  : formatDate(datesch_com),
			          shift     : shift_com
			        }).done(function(data) {
			        	if (data.result==200) {
			        		setTimeout(function(){
								$('#grid_sch').jqxDataTable('updateBoundData', 'cells');
								$('#grid_date').jqxGrid('updateBoundData');
							}, 1000);
			        		console.log(data);
			        	}else{
			        		console.log(data);
			        	}
			        });
		        }else{
		        	alert('กรุณาเลือกรายการที่รอนุมัติ');
		        }
		    
		    }

	    });

	    $("#add_detail").on('click',function(event) {
	    	var rows = $('#grid_date').jqxGrid('getselectedrowindexes');
			var datarow = $('#grid_date').jqxGrid('getrowdata', rows);
			var datesch = formatDate(datarow.SchDate);
			var shift = datarow.Shift;
			$('#grid_listboiler').jqxGrid('clearselection');
	    	$('#modal_listboiler').modal({backdrop: 'static'});
	    	loadgrid_listboiler(datesch,shift);
	    });

	    $('#btn_chooseBoiler').on('click', function (event){
			var rowdata = row_selected("#grid_listboiler");
			var rows_selected = [];
			var row_listboiler = '';
			if (typeof rowdata !== "undefined") {
				var rows = $('#grid_listboiler').jqxGrid('getselectedrowindexes');
				
				for (var i = 0; i < rows.length; i++) {
					row_listboiler = $('#grid_listboiler').jqxGrid('getrowdata', rows[i]);
					rows_selected.push(row_listboiler.ID);
				}
				
				// alert(rows_selected);
		        gojax('post', '/production/sch/update/list', {
		          id  	: rows_selected
		        }).done(function(data) {
		        	if (data.result==200) {
		        		$('#modal_listboiler').modal('hide');
		        		$('#grid_sch').jqxDataTable('updateBoundData');
		        	}else{
		        		console.log(data);
		        	}
		        });

			} else {
				alert("กรุณาเลือกข้อมูล");
			}
			return false;
		});

	});

	function loadgriddate(){
		var dataAdapter = new $.jqx.dataAdapter({
	    datatype: "json",
	    datafields: [
	      { name: "SchDate", type: "date" },
	      { name: "Shift", type: "int" },
	      { name: "Status", type: "int" },
	      { name: "ShiftName", type: "string" },
	      { name: "StatusName", type: "string" }
        ],
	      url : '/production/sch/load/date'
	    });

		var addfilter = function () {
            var filtergroup = new $.jqx.filter();
            var date_defult = '<?php echo date('Y-m-d', strtotime(date('Y-m-d'). ' -2 days'));?>';
            var date_defult1 = '<?php echo date('Y-m-d');?>';
            var filter_or_operator = 0;
            var filtervalue = date_defult;
            var filtercondition = 'GREATER_THAN_OR_EQUAL';
            var filter1 = filtergroup.createfilter('datefilter', filtervalue, filtercondition);

            filtervalue = date_defult1;
            filtercondition = 'LESS_THAN';
            var filter2 = filtergroup.createfilter('datefilter', filtervalue, filtercondition);

            filtergroup.addfilter(filter_or_operator, filter1);
            filtergroup.addfilter(filter_or_operator, filter2);
            // add the filters.
            $("#grid_date").jqxGrid('addfilter', 'SchDate', filtergroup);
            // apply the filters.
            $("#grid_date").jqxGrid('applyfilters');
        }

	    return $("#grid_date").jqxGrid({
	        width: '30%',
	        source: dataAdapter,
	        autoheight: true,
	        columnsresize: true,
	        pageable: true,
	        filterable: true,
	        showfilterrow: true,
	        pagesize: 5,
	        ready: function () {
                addfilter();
            },
	      	columns: [
		       	{ text:"วันที่", datafield: "SchDate", width:'40%', cellsformat: 'yyyy-MM-dd' , filtertype: 'range'},
		       	{ text:"กะ", datafield: "ShiftName",width:'30%',filtertype: 'checkedlist'},
          		{ text:"สถานะ", datafield: "StatusName",width:'30%',filtertype: 'checkedlist', filteritems: ['รออนุมัติ','อนุมัติ'], 
                  	cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata){
                      	var status;
	                        if (value =="รออนุมัติ") {
	                            status =  "<div style='padding: 5px; background:#F7DC6F ; color:#000000; font-size:15px;'> <b>รออนุมัติ</b> </div>";
	                        }else if(value =="อนุมัติ"){
	                            status =  "<div style='padding: 5px; background:#2ECC71 ; color:#000000; font-size:15px;'> <b>อนุมัติ</b> </div>";
	                        }
                        return status;
                  	}
          		}
			]
	    });
	}

	function loadgridsch(date_sch,shift,status) {

	    var dataAdapter = new $.jqx.dataAdapter({
	    datatype: "json",
	    updaterow: function (rowid, rowdata, commit) {
	        gojax('post', '/production/sch/update/sch', {
				time 	 : rowdata.Time,
				target 	 : rowdata.Target,
				actual	 : rowdata.Actual,
				scrap  	 : rowdata.Scrap,
				weight 	 : rowdata.Weight,
				arms 	 : rowdata.MoldID,
				id 		 : rowdata.ID

	        }).done(function(data) {
	          if (data.status === 200) {
	            $('#grid_sch').jqxDataTable('updateBoundData', 'cells');
	            commit(true);
	          }else{
	          	$('#grid_sch').jqxDataTable('updateBoundData', 'cells');
	          }
	          console.log(data);
	        }).fail(function() {
	          	commit(false);
	        });
	    },
	    datafields: [
	      { name: "ID", type: "int" },
	      { name: "Boiler", type: "string" },
	      { name: "BoilerName", type: "string"},
	      { name: "Employee", type: "int" },
	      { name: "FullName", type: "string" },
	      { name: "ItemID", type: "string"},
	      { name: "NameTH", type: "string"},
	      { name: "Time", type: "int"},
	      { name: "Target", type: "int"},
	      { name: "Actual", type: "int"},
	      { name: "Scrap", type: "int"},
	      { name: "Weight", type: "int"},
	      { name: "Remark", type: "string"},
	      { name: "MoldID", type: "int"},
	      { name: "CurID", type: "int"},
	      { name: "Status", type: "int"},
	      { name: "SchDate", type: "date"},
	      { name: "Shift", type: "int"}
        ],
	      url : '/production/sch/loadisexist?date_sch='+date_sch+'&shift='+shift
	    });

	    var setDelete = function (row, column, value) {
	      if (value !== "") {
	        return "<div style='padding:4px;'>" + value + "</div>";
	      } else {
	        return "<div style='font-size: 1em; padding:3px;'><button style='width:18px; height:18px; padding: 0.2px;' class='btn btn-danger' onclick='return setDelete("+row+")' style=' width:25px;'><b>-</b></button></div>";
	      }
	      
	    }

	    var setEmployee = function (row, column, value) {
	      if (value !== "") {
	        return "<div style='padding:4px;'>" + value + "</div>";
	      } else {
	        return "<div style='font-size: 1em; padding:3px;'><button style='width:50px; height:21px; padding: 0.2px;' class='btn btn-success' onclick='return setEmployeeModal("+row+")'>เพิ่มชื่อ</button></div>";
	      }
	      
	    }

	    var setItem = function (row, column, value) {
	      if (value !== "") {
	        return "<div style='padding:4px;'>" + value + "</div>";
	      } else {
	        return "<div style='font-size: 1em; padding:3px;'><button style='width:90px; height:21px; padding: 0.2px;' class='btn btn-success' onclick='return setItemModal("+row+")'>เพิ่มขนาดพิมพ์</button></div>";
	      }
	      
	    }

	    var setRemark = function (row, column, value) {
	      if (value !== "") {
	        return "<div style='padding:4px;'>" + value + "</div>";
	      } else {
	        return "<div style='font-size: 1em; padding:3px;'><button style='width:90px; height:21px; padding: 0.2px;' class='btn btn-success' onclick='return setRemarkModal("+row+")'>เพิ่มหมายเหตุ</button></div>";
	      }
	      
	    }

	    return $("#grid_sch").jqxDataTable({
	        width: '100%',
            source: dataAdapter,
            pageable: true,
            altRows: true,
            columnsResize: true,
            filterable: true,
            editable : true,
	        pageSize: 10,
	        sortable: true,
	        rendered: function () {
		        $('#detail').html('<span class="glyphicon glyphicon-list-alt"> รายละเอียด</span>');
  				$('#detail').attr('disabled', false);
  				$('#btn_complete').html('<span class="glyphicon glyphicon-ok"> อนุมัติ</span>');
  				$('#btn_complete').attr('disabled', false);

  				if (status==1) {
  					$('#btn_complete').show();
  				}else{
  					$('#btn_complete').hide();
  				}

		        gojax('get', '/production/sch/complete/check', {
			        date_sch  : date_sch,
			        shift  	  : shift
			    }).done(function (data) {
			    	if (data.result==200) {
			    	if (shift==1) {
						var shiftname = "กะกลางวัน";
					}else{
						var shiftname = "กะกลางคืน";
					}
			    	document.getElementById("txtcontent").style.color = "green";
		        	document.getElementById("txtcontent").innerHTML = "วันที่: "+date_sch+ " กะ: "+shiftname+" ดำเนินการ Completed เสร็จสิ้น "+ "<i>(ไม่สามารถแก้ไขได้)</i>";
			    		$('#txtcontent').show();
			    	}else{
			    		$('#txtcontent').hide();
			    	}
			    });

		    },
            groups: ['BoilerName'],
                groupsRenderer: function(value, rowData, level)
                {
					boiler = value.substring(0, value.indexOf('-'));
					boiler = boiler.split('_').pop();
					var boiler_ = value.split('_').pop();
                    return '<b>เตา : ' + boiler_ + '</b> <button style="width:54px; height:18px; padding: 0.2px;" class="btn btn-success" onclick="return setBoilerAdd(\''+boiler+'\',\''+date_sch+'\',\''+shift+'\')">เพิ่มพิมพ์</button>';
                },
	      	columns: [

		        { text:"เตา", datafield: "BoilerName", align: 'left', width:'8%', hidden: true, editable:false},
		        { text:"", cellsrenderer : setDelete, width:'2.5%', editable:false},
		        { text:"ชื่อพนักงาน", datafield: "FullName", align: 'center', width:'14%', editable:false},
		        { text:"พิมพ์", datafield: "MoldID", align: 'center', width:'3%'},
		        { text:"", cellsrenderer : setEmployee, width:'5%', editable:false},
		        { text:"ขนาดพิมพ์", datafield: "NameTH", align: 'center', width:'18%', editable:false},
		        { text:"", cellsrenderer : setItem, width:'8%', editable:false},
		        { text:"เวลาอบ(นาที)", datafield: "Time", align: 'center', width:'7%'},
		        { text:"เป้า(เส้น)", datafield: "Target", align: 'center', columngroup: 'ProductDetails', width:'6%'},
		        { text:"อบได้(เส้น)", datafield: "Actual", align: 'center', columngroup: 'ProductDetails', width:'6%'},
		       	{ text:"สูญเสีย(เส้น)", datafield: "Scrap", align: 'center', width:'6%'},
		       	{ text:"น้ำหนัก(กรัม)", datafield: "Weight", align: 'center', width:'7%'},
		       	{ text:"หมายเหตุ", datafield: "Remark", align: 'center', editable:false},
		       	{ text:"", cellsrenderer : setRemark, width:'7.5%', editable:false},

			],
                columnGroups: 
                [
                  { text: 'จำนวนการอบยาง', align: 'center', name: 'ProductDetails' }
                ]

	    });

	}

	function setDelete(row) {
	    var transid = $("#grid_sch").jqxDataTable('getCellValue', row, 'ID');
	    var statusid= $("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
	    if (statusid==1) {
		    gojax('post', '/production/sch/delete/sch', {
	          	id  	: transid
	        }).done(function(data) {
	        	if (data.result==200) {
	        		$('#grid_sch').jqxDataTable('updateBoundData','cells');
	        		console.log(data);
	        	}else{
	        		console.log(data);
	        	}
	        });
	    }
	}

	function setEmployeeModal(row) {
	    var transid 	= $("#grid_sch").jqxDataTable('getCellValue', row, 'ID');
	    var boilerid	= $("#grid_sch").jqxDataTable('getCellValue', row, 'Boiler');
	    var moldid 		= $("#grid_sch").jqxDataTable('getCellValue', row, 'MoldID');
	    var statusid 	= $("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
	    var schdate  	= $("#grid_sch").jqxDataTable('getCellValue', row, 'SchDate');
    	var shift    	= $("#grid_sch").jqxDataTable('getCellValue', row, 'Shift');
 		$('#id_trans').val(transid);
 		$('#id_boiler').val(boilerid);
 		$('#id_mold').val(moldid);
 		$('#date_sch').val(formatDate(schdate));
    	$('#shift').val(shift);
 		$('#grid_employee').jqxGrid('clearselection');
 		if (statusid==1) {
	    	loadgridemployee(schdate,shift,boilerid,moldid);
	    	$('#modal_employee').modal({backdrop: 'static'});
	    }
	}

	function loadgridemployee(schdate,shift,boilerid,moldid) {
		var schdate = formatDate(schdate);

	    var dataAdapter = new $.jqx.dataAdapter({
	    datatype: "json",
	    datafields: [
	      { name: "ID", type: "int" },
	      { name: "Code", type: "string" },
	      { name: "FirstName", type: "string" },
	      { name: "LastName", type: "string" },
	      { name: "DepartmentName", type: "string"}
        ],
	      url : '/production/sch/load/employee'
	    });

	    return $("#grid_employee").jqxGrid({
	        width: '100%',
	        source: dataAdapter,
	        autoheight: true,
	        columnsresize: true,
	        pageable: true,
	        filterable: true,
	        showfilterrow: true,
	        pagesize: 5,
	        selectionmode: 'checkbox',
	        rendered: function () {
	        	gojax('get', '/production/sch/get/employee', {
			        schdate  : schdate,
			        shift  	 : shift,
			        boiler   : boilerid,
			        mold     : moldid
			    }).done(function (data) {
			    	for (var key in data) {
				        var rowID = (data[key].ID-1);
				        var value = $('#grid_employee').jqxGrid('getcellvaluebyid', rowID, "ID");
				        $("#grid_employee").jqxGrid('selectrow', rowID);
				        $('#grid_employee').jqxGrid('focus');
				        console.log(rowID);
				    }
			    });
		    },
	      	columns: [
		       	// { text:"Code", datafield: "ID", align: 'center'},
		       	{ text:"Code", datafield: "Code", align: 'center'},
		       	{ text:"ชื่อ", datafield: "FirstName", align: 'center'},
		       	{ text:"นามสกุล", datafield: "LastName", align: 'center'},
		       	{ text:"แผนก", datafield: "DepartmentName", align: 'center'}
			]
	    });

	}

	function setItemModal(row) {
	    var transid = $("#grid_sch").jqxDataTable('getCellValue', row, 'ID');
	    var boiler  = $("#grid_sch").jqxDataTable('getCellValue', row, 'Boiler');
	    var statusid= $("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
 		$('#id_trans').val(transid);
 		if (statusid==1) {
		    loadgriditem(boiler);
		    $('#modal_item').modal({backdrop: 'static'});
		}
	}

	function loadgriditem(boiler) {

	    var dataAdapter = new $.jqx.dataAdapter({
	    datatype: "json",
	    datafields: [
	      { name: "ID", type: "string" },
	      { name: "ItemName", type: "string" },
	      { name: "Pattern", type: "string" },
	      { name: "Brand", type: "string" },
	      { name: "RateCure", type: "string" },
	      { name: "ItemNameThai", type: "string" },
	      { name: "NetWeight", type: "int" }
        ],
	      url : '/production/sch/load/item?boiler='+boiler
	    });

	    return $("#grid_item").jqxGrid({
	        width: '100%',
	        source: dataAdapter,
	        autoheight: true,
	        columnsresize: true,
	        pageable: true,
	        filterable: true,
	        showfilterrow: true,
	        pagesize: 5,
	      	columns: [
		       	{ text:"Item Id", datafield: "ID", align: 'center',width: '15%'},
		       	{ text:"Name", datafield: "ItemName", align: 'center'},
		       	// { text:"Name TH", datafield: "ItemNameThai", align: 'center'},
		       	{ text:"Pattern", datafield: "Pattern", align: 'center',width: '10%'},
		       	{ text:"Brand", datafield: "Brand", align: 'center',width: '10%'},
		       	{ text:"RateCure", datafield: "RateCure", align: 'center',width: '10%'},
		       	{ text:"NetWeight", datafield: "NetWeight", align: 'center',width: '10%'}
			]
	    });

	}

	function setRemarkModal(row){
    	var transid  = $("#grid_sch").jqxDataTable('getCellValue', row, 'ID');
    	var boilerid = $("#grid_sch").jqxDataTable('getCellValue', row, 'Boiler');
	    var moldid   = $("#grid_sch").jqxDataTable('getCellValue', row, 'MoldID');
    	var statusid = $("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
    	var schdate  = $("#grid_sch").jqxDataTable('getCellValue', row, 'SchDate');
    	var shift    = $("#grid_sch").jqxDataTable('getCellValue', row, 'Shift');
 		$('#id_trans').val(transid);
 		$('#id_boiler').val(boilerid);
 		$('#id_mold').val(moldid);
 		$('#date_sch').val(formatDate(schdate));
    	$('#shift').val(shift);
 		$('#grid_remark').jqxGrid('clearselection');
 		if (statusid==1) {
		    loadgridremark(schdate,shift,boilerid,moldid);
		    $('#modal_remark').modal({backdrop: 'static'});
		}
    }

    function loadgridremark(schdate,shift,boilerid,moldid) {
    	var schdate = formatDate(schdate);

	    var dataAdapter = new $.jqx.dataAdapter({
	    datatype: "json",
	    datafields: [
	      { name: "ID", type: "int" },
	      { name: "ProblemID", type: "string" },
	      { name: "Description", type: "string" }
        ],
	      url : '/production/sch/load/remark'
	    });

	    return $("#grid_remark").jqxGrid({
	        width: '100%',
	        source: dataAdapter,
	        autoheight: true,
	        columnsresize: true,
	        pageable: true,
	        filterable: true,
	        showfilterrow: true,
	        pagesize: 5,
	        selectionmode: 'checkbox',
	        rendered: function () {
	        	gojax('get', '/production/sch/get/remark', {
			        schdate  : schdate,
			        shift  	 : shift,
			        boiler   : boilerid,
			        mold     : moldid
			    }).done(function (data) {
			    	for (var key in data) {
				        var rowID = (data[key].ID-1);
				        var value = $('#grid_remark').jqxGrid('getcellvaluebyid', rowID, "ProblemID");
				        $("#grid_remark").jqxGrid('selectrow', rowID);
				        $('#grid_remark').jqxGrid('focus');
				    }
			    });
		    },
	      	columns: [
		       	{ text:"ProblemID", datafield: "ProblemID", align: 'center'},
		       	{ text:"Description", datafield: "Description", align: 'center'}
			]
	    });

	}

	function loadgrid_listboiler(datesch,shift) {

	    var dataAdapter = new $.jqx.dataAdapter({
	    datatype: "json",
	    datafields: [
	      { name: "ID", type: "int" },
	      { name: "Boiler", type: "string" },
	      { name: "MoldID", type: "int" }
        ],
	      url : '/production/sch/load/listboiler?datesch='+datesch+'&shift='+shift
	    });

	    return $("#grid_listboiler").jqxGrid({
	        width: '100%',
	        source: dataAdapter,
	        autoheight: true,
	        columnsresize: true,
	        pageable: true,
	        filterable: true,
	        showfilterrow: true,
	        pagesize: 5,
	        selectionmode: 'checkbox',
	      	columns: [
		       	{ text:"เตา", datafield: "Boiler", align: 'center'},
		       	{ text:"พิมพ์", datafield: "MoldID", align: 'center'}
			]
	    });

	}

	function setBoilerAdd(boiler,date_sch,shift) {
		var leader = 'leader';
	    gojax('post', '/production/sch/add/sch', {
          	boiler  : boiler,
          	date_sch: date_sch,
          	shift   : shift,
          	type    : leader
        }).done(function(data) {
        	if (data.result==200) {
        		$('#grid_sch').jqxDataTable('updateBoundData','cells');
        		console.log(data);
        	}else{
        		console.log(data);
        	}
        });
     	// alert(boiler+'\n'+date_sch+'\n'+shift);
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