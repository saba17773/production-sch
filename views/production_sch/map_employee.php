<?php 
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>
<style type="text/css">
  td { 
    padding: 5px;
    text-align: left;
    font-size: 18px;
  }
</style>

<h4>Map Employee<br></h4>

<table width="100%">
	<tr>
		<td width="50%">
			<b>รายชื่อพนักงาน กะ C</b>
			<button class="btn btn-success btn-sm" id="add_shift_c">
				<span class="glyphicon glyphicon-plus"></span> เพิ่มรายชื่อ
			</button>
			<button class="btn btn-danger btn-sm" id="delete_shift_c">
				<span class="glyphicon glyphicon-remove"></span> ลบรายชื่อ
			</button>
		</td>
		<td  width="50%">
			<b>รายชื่อพนักงาน กะ D</b>
			<button class="btn btn-success btn-sm" id="add_shift_d">
				<span class="glyphicon glyphicon-plus"></span> เพิ่มรายชื่อ
			</button>
			<button class="btn btn-danger btn-sm" id="delete_shift_d">
				<span class="glyphicon glyphicon-remove"></span> ลบรายชื่อ
			</button>
		</td>
	</tr>
	<tr>
		<td>
			<div id="grid_c"></div>
		</td>
		<td>
			<div id="grid_d"></div>
		</td>
	</tr>
</table>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		loadgridemployee_c();
		loadgridemployee_d();

		function loadgridemployee_c() {

		    var dataAdapter = new $.jqx.dataAdapter({
		    datatype: "json",
		    datafields: [
		      { name: "Code", type: "string" },
		      { name: "FirstName", type: "string" },
		      { name: "LastName", type: "string" },
		      { name: "DepartmentName", type: "string"}
		    ],
		      url : '/production/sch/load/employee'
		    });

		    return $("#grid_c").jqxGrid({
		        width: '100%',
		        source: dataAdapter,
		        autoheight: true,
		        columnsresize: true,
		        pageable: true,
		        filterable: true,
		        showfilterrow: true,
		        pagesize: 5,
		        selectionmode: 'checkbox',
		     //    rendered: function () {
		     //    	gojax('get', '/production/sch/get/employee', {
				   //      transid  : transid
				   //  }).done(function (data) {
				   //  	for (var key in data) {
					  //       var rowID = (data[key].ID-1);
					  //       var value = $('#grid_employee').jqxGrid('getcellvaluebyid', rowID, "ID");
					  //       $("#grid_employee").jqxGrid('selectrow', rowID);
					  //       $('#grid_employee').jqxGrid('focus');
					  //       // console.log(data);
					  //   }
				   //  });
			    // },
		      	columns: [
			       	{ text:"Code", datafield: "Code", align: 'center'},
			       	{ text:"ชื่อ", datafield: "FirstName", align: 'center'},
			       	{ text:"นามสกุล", datafield: "LastName", align: 'center'},
			       	{ text:"แผนก", datafield: "DepartmentName", align: 'center'}
				]
		    });

		}

		function loadgridemployee_d() {

		    var dataAdapter = new $.jqx.dataAdapter({
		    datatype: "json",
		    datafields: [
		      { name: "Code", type: "string" },
		      { name: "FirstName", type: "string" },
		      { name: "LastName", type: "string" },
		      { name: "DepartmentName", type: "string"}
		    ],
		      url : '/production/sch/load/employee'
		    });

		    return $("#grid_d").jqxGrid({
		        width: '100%',
		        source: dataAdapter,
		        autoheight: true,
		        columnsresize: true,
		        pageable: true,
		        filterable: true,
		        showfilterrow: true,
		        pagesize: 5,
		        selectionmode: 'checkbox',
		     //    rendered: function () {
		     //    	gojax('get', '/production/sch/get/employee', {
				   //      transid  : transid
				   //  }).done(function (data) {
				   //  	for (var key in data) {
					  //       var rowID = (data[key].ID-1);
					  //       var value = $('#grid_employee').jqxGrid('getcellvaluebyid', rowID, "ID");
					  //       $("#grid_employee").jqxGrid('selectrow', rowID);
					  //       $('#grid_employee').jqxGrid('focus');
					  //       // console.log(data);
					  //   }
				   //  });
			    // },
		      	columns: [
			       	{ text:"Code", datafield: "Code", align: 'center'},
			       	{ text:"ชื่อ", datafield: "FirstName", align: 'center'},
			       	{ text:"นามสกุล", datafield: "LastName", align: 'center'},
			       	{ text:"แผนก", datafield: "DepartmentName", align: 'center'}
				]
		    });

		}

		$('#shift_d').on('click', function (event){
			var rowdata = row_selected("#grid_d");
			var rows_selected = [];
			var row_employee = '';
			if (typeof rowdata !== "undefined") {
				var rows = $('#grid_d').jqxGrid('getselectedrowindexes');
				
				for (var i = 0; i < rows.length; i++) {
					row_employee = $('#grid_d').jqxGrid('getrowdata', rows[i]);
					rows_selected.push(row_employee.Code);
				}

				alert(rows_selected);
				
		        
			} else {
				alert("กรุณาเลือกข้อมูล");
			}
			return false;
		});

	});
</script>