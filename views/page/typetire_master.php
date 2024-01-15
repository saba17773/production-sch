<?php $this->layout("layouts/base", ['title' => 'Item Master']); ?>

<h1>Type Tire Master</h1>
<hr>

<div class="panel panel-default" id="panel_manage">
	<div class="panel-group">
		<div class="form-group">
				<button class="btn btn-success" id="btn_create"><span class="glyphicon glyphicon-plus"></span> Create New</button>
				<button class="btn btn-info" id="btn_edit"><span class="glyphicon glyphicon-edit"></span> Edit </button>
				<button class="btn btn-warning" id="btn_line"><span class="glyphicon glyphicon-list"></span> Line</button>
		</div>
	</div>
	<div>
	<div id="grid_main"></div>
	</div>
</div>

<div class="modal" id="modal_createnew_group" tabindex="-1" role="dialog" >
	<div class="modal-dialog modal-sm" role="document" style="width:500px;margin:auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
				<h5 class="modal-title">สร้างกลุ่มใหม่</h5>
			</div>
			<div class="modal-body">
				<form id="form_createnew_group">
					<table>
						<tr>
							<td>
								Group Description : 
							</td>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 300px;">
									
									<input type="text" id="txt_new_groupdesc" name="txt_new_groupdesc" class="form-control" required autocomplete="off"/>
								</div>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<br/>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td style="padding: 5px;" align="left">
								<button class="btn btn-success btn-sm" id="btn_new_group">
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

<div class="modal" id="modal_edit_group" tabindex="-1" role="dialog" >
  	<div class="modal-dialog modal-sm" role="document" style="width:500px;margin:auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
				<h5 class="modal-title">แก้ไขกลุ่ม</h5>
			</div>
			<div class="modal-body">
				<form id="form_edit_group">
					<table>
						<tr>
							<td align="right">
								Group ID : &nbsp;
							</td>
							<td style="padding: 5px;" align="left">
								<div class="input-group" style="width: 100px;">
									<input type="text" id="txt_edit_groupid" name="txt_edit_groupid" class="form-control" required autocomplete="off" readonly/>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right">
								Group Description : &nbsp;
							</td>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 300px;">
									<input type="text" id="txt_edit_groupdesc" name="txt_edit_groupdesc" class="form-control" required autocomplete="off"/>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right">
								ลำดับการเรียงข้อมูล : &nbsp;
							</td>
							<td style="padding: 5px;" align="left">
								<div class="input-group" style="width: 100px;">
									<input type="text" id="txt_edit_sortby" name="txt_edit_sortby" class="form-control" required autocomplete="off" />
								</div>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<br/>
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

<div class="modal" id="modal_line_group" tabindex="-1" role="dialog" >
  	<div class="modal-dialog modal-sm" role="document" style="width:600px;margin:auto;">
    	<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
				<h5 class="modal-title">รายละเอียดกลุ่ม : <span id="head_line_id"></span> <span id="head_line_desc"></span></h5> 
			</div>
			<div class="modal-body">

				<div class="panel panel-default" id="panel_create">
					<table>
						<tr>
							<td align="right" style="width: 40%;">
								Detail Description : &nbsp;
							</td>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 300px;">
									<input type="text" id="txt_new_ddesc" name="txt_new_ddesc" class="form-control" required autocomplete="off"/>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right">
								Size : &nbsp;
							</td>
							<td style="padding: 5px;" align="left">
								<div class="input-group" style="width: 150px;">
									<input type="text" id="txt_new_dsize" name="txt_new_dsize" class="form-control" required autocomplete="off" />
								</div>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<br/>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td style="padding: 5px;" align="left">
								<button class="btn btn-success btn-sm" id="btn_new_dsave">
									<span class="glyphicon glyphicon-ok"></span> Save
								</button>
							</td>
						</tr>
					</table>
				</div>

				<div class="panel panel-default" id="panel_edit">
					<table>
						<tr>
							<td align="right">
								<!-- Detail ID : &nbsp; -->
							</td>
							<td style="padding: 5px;" align="left">
								<div class="input-group" style="width: 100px;">
									<input type="hidden" id="txt_edit_did" name="txt_edit_did" class="form-control" required autocomplete="off" readonly/>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right" style="width: 40%;">
								Detail Description : &nbsp;
							</td>
							<td style="padding: 5px;" align="center" >
								<div class="input-group" style="width: 300px;">
									<input type="text" id="txt_edit_ddesc" name="txt_edit_ddesc" class="form-control" required autocomplete="off"/>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right">
								Size : &nbsp;
							</td>
							<td style="padding: 5px;" align="left">
								<div class="input-group" style="width: 150px;">
									<input type="text" id="txt_edit_dsize" name="txt_edit_dsize" class="form-control" required autocomplete="off"/>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right">
								ลำดับ : &nbsp;
							</td>
							<td style="padding: 5px;" align="left">
								<div class="input-group" style="width: 100px;">
									<input type="text" id="txt_edit_dsortby" name="txt_edit_dsortby" class="form-control" required autocomplete="off" />
								</div>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td style="padding: 5px;" align="left">
								<button class="btn btn-success btn-sm" id="btn_edit_dsave">
									<span class="glyphicon glyphicon-ok"></span> Save
								</button>
							</td>
						</tr>
					</table>
					<input type="hidden" id="txt_edit_idauto" name="txt_edit_idauto" class="form-control" readonly/>
				</div>

				<div class="form-group">
					<button class="btn btn-success" id="btn_create_detail"><span class="glyphicon glyphicon-plus"></span> Create New</button>
					<button class="btn btn-info" id="btn_edit_detail"><span class="glyphicon glyphicon-edit"></span> Edit </button>
				<div>

				<br/>
				<div id="grid_line"></div>
			</div>
    	</div>
  	</div>
</div>

<!-- <div class="modal" id="modal_create_detail" tabindex="-1" role="dialog" >
  <div class="modal-dialog modal-sm" role="document" style="width:500px;margin:auto;">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
			<h5 class="modal-title">สร้าง sub group ใหม่</h5>
		</div>
		<div class="modal-body">
			<form id="form_create_detail">
				<table>
					<tr>
						<td align="right">
							Detail Description : &nbsp;
						</td>
						<td style="padding: 5px;" align="center">
							<div class="input-group" style="width: 300px;">
								<input type="text" id="txt_new_ddesc" name="txt_new_ddesc" class="form-control" required autocomplete="off"/>
							</div>
						</td>
					</tr>
					<tr>
						<td align="right">
							Size : &nbsp;
						</td>
						<td style="padding: 5px;" align="left">
							<div class="input-group" style="width: 100px;">
								<input type="text" id="txt_new_dsize" name="txt_new_dsize" class="form-control" required autocomplete="off" />
							</div>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<br/>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td style="padding: 5px;" align="left">
							<button class="btn btn-success btn-sm" id="btn_new_dsave">
								<span class="glyphicon glyphicon-ok"></span> Save
							</button>
						</td>
					</tr>
				</table>
				<input type="hidden" id="txt_new_dgrpid" name="txt_new_dgrpid" />
			</form>
		</div>
    </div>
  </div>
</div>

<div class="modal" id="modal_edit_detail" tabindex="-1" role="dialog" >
  	<div class="modal-dialog modal-sm" role="document" style="width:500px;margin:auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
				<h5 class="modal-title">เพิ่ม Sub Group ของกลุ่ม : <span id="head_line_did"></span> <span id="head_line_ddesc"></span></h5>
			</div>
			<div class="modal-body">
				<form id="form_edit_detail">
					<table>
						<tr>
							<td align="right">
								Detail ID : &nbsp;
							</td>
							<td style="padding: 5px;" align="left">
								<div class="input-group" style="width: 100px;">
									<input type="text" id="txt_edit_did" name="txt_edit_did" class="form-control" required autocomplete="off" readonly/>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right">
								Detail Description : &nbsp;
							</td>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 300px;">
									<input type="text" id="txt_edit_ddesc" name="txt_edit_ddesc" class="form-control" required autocomplete="off"/>
								</div>
							</td>
						</tr>
						<tr>
							<td align="right">
								Size : &nbsp;
							</td>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 300px;">
									<input type="text" id="txt_edit_dsize" name="txt_edit_dsize" class="form-control" required autocomplete="off"/>
								</div>
							</td>
						</tr
						<tr>
							<td align="right">
								ลำดับการเรียงข้อมูล : &nbsp;
							</td>
							<td style="padding: 5px;" align="left">
								<div class="input-group" style="width: 100px;">
									<input type="text" id="txt_edit_dsortby" name="txt_edit_dsortby" class="form-control" required autocomplete="off" />
								</div>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<br/>
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
					<input type="text" id="txt_edit_idauto" name="txt_edit_idauto" class="form-control" readonly/>
				</form>
			</div>
		</div>
  	</div>
</div> -->

<script type="text/javascript">
jQuery(document).ready(function($)
{
	bind_gridmain();
	

  	$("#btn_create").on('click',function(event) 
	{
		$('#modal_createnew_group').modal({backdrop:'static'});
		$("#form_createnew_group")[0].reset();
		
	});

	$("#btn_edit").on('click',function(event) 
	{
		event.preventDefault();
		var rowdata = row_selected('#grid_main');
		if (typeof rowdata !== 'undefined') 
		{
			$('#modal_edit_group').modal({backdrop:'static'});
			id = rowdata.GroupID;
			desc = rowdata.GroupDesc;
			sort = rowdata.Sortby;

			$('#txt_edit_groupid').val(id);
			$('#txt_edit_groupdesc').val(desc);
			$('#txt_edit_sortby').val(sort);
		}
		else 
		{
			$('#modal_alert').modal({backdrop: 'static'});
			$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
		}
		
	});

	$("#btn_line").on('click',function(event) 
	{
		event.preventDefault();
		var rowdata = row_selected('#grid_main');
		if (typeof rowdata !== 'undefined') 
		{
			
			groupid = rowdata.GroupID;
			groupdesc = rowdata.GroupDesc;

			$('#head_line_id').text(groupid);
			$('#head_line_desc').text(groupdesc);

			bind_gridline(groupid) 

			$('#panel_create').hide();
			$('#panel_edit').hide();

			$('#modal_line_group').modal({backdrop:'static'});
			$('#grid_line').jqxGrid('clearselection');
		}
		else 
		{
			$('#modal_alert').modal({backdrop: 'static'});
			$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
		}
		
	});
	
	$("#btn_create_detail").on('click',function(event) 
	{
		// $('#modal_create_detail').modal({backdrop:'static'});
		// $("#form_create_detail")[0].reset();

		// dgrpid = $('#head_line_id').text();
		// $('#txt_new_dgrpid').val(dgrpid);
		$('#panel_create').show();
		$('#panel_edit').hide();

	});

	$("#btn_edit_detail").on('click',function(event) 
	{
		var rowdata = row_selected('#grid_line');
		if (typeof rowdata !== 'undefined')
		{
			$('#panel_create').hide();
			$('#panel_edit').show();

			gid = rowdata.GroupID;
			gdesc = rowdata.GroupDesc;

			did = rowdata.DetailID;
			ddesc = rowdata.DetailDesc;
			dsize = rowdata.Size;
			dsort = rowdata.Sortby;
			didauto = rowdata.ID;;

			$('#txt_edit_did').val(did);
			$('#txt_edit_ddesc').val(ddesc);
			$('#txt_edit_dsize').val(dsize);
			$('#txt_edit_dsortby').val(dsort);
			$('#txt_edit_idauto').val(didauto);
			
		}
		else 
		{
			$('#modal_alert').modal({backdrop: 'static'});
			$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
		}
		
		// event.preventDefault();
		// var rowdata = row_selected('#grid_line');
		// if (typeof rowdata !== 'undefined') 
		// {
		// 	$('#modal_edit_detail').modal({backdrop:'static'});
		// 	gid = rowdata.GroupID;
		// 	gdesc = rowdata.GroupDesc;

		// 	did = rowdata.DetailID;
		// 	ddesc = rowdata.DetailDesc;
		// 	dsize = rowdata.Size;
		// 	dsort = rowdata.Sortby;
		// 	didauto = rowdata.ID;

		// 	$('#head_line_did').text(gid);
		// 	$('#head_line_ddesc').text(gdesc);

		// 	$('#txt_edit_did').val(did);
		// 	$('#txt_edit_ddesc').val(ddesc);
		// 	$('#txt_edit_dsize').val(dsize);
		// 	$('#txt_edit_dsortby').val(dsort);

		// 	$('#txt_edit_idauto').val(didauto);

		// }
		// else 
		// {
		// 	$('#modal_alert').modal({backdrop: 'static'});
		// 	$('#modal_alert_message').text('กรุณาเลือกข้อมูล');
		// }
		
	});

	$("#btn_new_dsave").on('click',function(event)
	{
		// GId = $("#head_line_id").text();
		// DDesc = $("#txt_new_ddesc").val();
		// DSize = $("#txt_new_dsize").val();
	
		insertDetail();

		// alert(DDesc + ' | ' + DSize);

		$('#panel_create').hide();
		$('#panel_edit').hide();
		$("#txt_new_ddesc").val('');
		$("#txt_new_dsize").val('');
	});

	$("#btn_edit_dsave").on('click',function(event)
	{
		// EDDesc = $("#txt_edit_ddesc").val();
		// EDSize = $("#txt_edit_dsize").val();
		// EDSort = $("#txt_edit_dsortby").val();
		// EDautoid = $("#txt_edit_idauto").val();
		updateDetail()

		// alert(DDesc + ' | ' + DSize);

		$('#panel_create').hide();
		$('#panel_edit').hide();
		$("#txt_new_ddesc").val('');
		$("#txt_new_dsize").val('');
	});

	// form_createnew_group
	$('#form_createnew_group').on('submit', function(event) 
	{
		insertGroup();
	});

	$('#form_edit_group').on('submit', function(event) 
	{
		updateGroup();
	});
	
	function bind_gridmain() 
	{
		var dataAdapter = new $.jqx.dataAdapter({
		datatype: 'json',
		datafields: [
			{ name: 'GroupID', type: 'int'},
		{ name: 'GroupDesc', type: 'string'},
		{ name: 'Sortby', type: 'int'}
		],
		url: base_url + "/bindgrid/main"
		});

		return $("#grid_main").jqxGrid({
		width: '100%',
			source: dataAdapter,
			autoheight: true,
			pageSize : 10,
			altrows : true,
			pageable : true,
			sortable: true,
			filterable : true,
			showfilterrow : true,
			columnsresize: true,
			columns: [
			{ text: 'Group ID', datafield: 'GroupID', width: 70},
			{ text: 'Group Description', datafield: 'GroupDesc', width: 200},
			{ text: 'ลำดับ', datafield: 'Sortby', width: 70}
			]
		});
	}

	function bind_gridline(groupid) 
	{
		var dataAdapter = new $.jqx.dataAdapter({
		datatype: 'json',
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'GroupID', type: 'int'},
			{ name: 'DetailID', type: 'int'},
			{ name: 'DetailDesc', type: 'string'},
			{ name: 'Size', type: 'string'},
			{ name: 'Sortby', type: 'int'},
			{ name: 'GroupDesc', type: 'string'}
		],
		url: base_url + "/bindgrid/line/"+groupid
		});

		return $("#grid_line").jqxGrid({
		width: '100%',
			source: dataAdapter,
			autoheight: true,
			pageSize : 5,
			altrows : true,
			pageable : true,
			sortable: true,
			filterable : true,
			showfilterrow : true,
			columnsresize: true,
			columns: [
			{ text: 'Detail ID', datafield: 'DetailID', width: 70},
			{ text: 'Detail Description', datafield: 'DetailDesc', width: 300},
			{ text: 'Size', datafield: 'Size', width: 100},
			{ text: 'ลำดับ', datafield: 'Sortby', width: 100}
			]
		});
	}

	function insertGroup()
	{
		gojax_f('post', base_url+'/insert/group' , '#form_createnew_group')
		.done(function(data) 
		{	
			if(data.result === true)
			{
				$('#grid_main').jqxGrid('updatebounddata');
			}
			else
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text(data.message);
			}
		});
	}

	function updateGroup()
	{
		gojax_f('post', base_url+'/update/group' , '#form_edit_group')
		.done(function(data) 
		{	
			if(data.result === true)
			{
				$('#grid_main').jqxGrid('updatebounddata');
			}
			else
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text(data.message);
			}
		});
	}

	function insertDetail()
	{
		gojax('post', '/insert/detail', {
			  dDesc  : $( "#txt_new_ddesc").val(),
			  dSize  : $( "#txt_new_dsize").val(),
			  gId  : $( "#head_line_id").text() }).done(function(data) 
		{
			if(data.result === true)
			{
				$('#grid_line').jqxGrid('updatebounddata');
					
			}
			else
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text(data.message);
			}
		});
	}

	function updateDetail() 
	{

		gojax('post', '/update/detail', {
			edDesc  : $( "#txt_edit_ddesc").val(),
			edSize  : $( "#txt_edit_dsize").val(),
			edSort  : $( "#txt_edit_dsortby").val(),
			edIdAuto  : $( "#txt_edit_idauto").val() 
			}).done(function(data) 
		{
			if(data.result === true)
			{
				$('#grid_line').jqxGrid('updatebounddata');
					
			}
			else
			{
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text(data.message);
			}
		});
	}


});



</script>
