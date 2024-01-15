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
<div id="grid_sch1"></div>
<BR><BR><BR>
<div id="grid_sch"></div>

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















	});

	function checkgridsch(date_sch, shift) {




		gojax('get', '/sch2/sch/data/checkgridschplantire', {
			date_sch: date_sch,
			shift: shift
		}).done(function(data) {
			if (data.result == true) {

				checkgridsdate(date_sch, shift);
				// loadgridsc1(date_sch, shift);
				//loadgridsch(date_sch, shift);

				// $("#grid_sch1").show();
				//$("#grid_sch").show();



				$('#message_checkdata').hide();
				// $('#message_checkdata').html('<strong>' + data.message + '</strong>');

			} else {
				$("#grid_sch").hide();
				$("#grid_sch1").hide();
				$('#message_checkdata').show();
				$('#message_checkdata').html('<strong>' + data.message + '</strong>');

			}
		});

	}

	function checkgridsdate(date_sch, shift) {


		gojax('get', '/sch2/sch/data/checkgriddateplantire', {
			date_sch: date_sch,
			shift: shift
		}).done(function(data) {
			if (data.date1 == null || data.date1 == '') {
				$date2 = "-";
			} else {
				$dd = data.date1;
				$dateuse1 = $dd.split("-");
				$date2 = $dateuse1[2] + "-" + $dateuse1[1] + "-" + $dateuse1[0];
			}

			if (data.date2 == null || data.date2 == '') {
				$date3 = "-";
			} else {
				$dd2 = data.date2;
				$dateuse2 = $dd2.split("-");
				$date3 = $dateuse2[2] + "-" + $dateuse2[1] + "-" + $dateuse2[0];

			}
			//$date1 = date_sch;
			$("input[name=date2]").val($date2);
			$("input[name=date3]").val($date3);
			loadgridsc1(date_sch, shift, $date2, $date3);
			loadgridsch(date_sch, shift, $date2, $date3);
			$("#grid_sch1").show();
			$("#grid_sch").show();




		});

	}



	function loadgridsch(date_sch, shift) {


		// $date1 = date_sch;
		// $date2 = $('input[name=date2]').val()
		// $date3 = $("input[name=date3]").val();
		$date2 = $date2;
		$date3 = $date3;
		$date1 = date_sch;
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: "json",

			datafields: [


				{
					name: "OrderLackshift",
					type: "int"
				},
				{
					name: "check1",
					type: "float"
				},
				{
					name: "check2",
					type: "float"
				},
				{
					name: "check3",
					type: "float"
				},
				{
					name: "checktotal",
					type: "float"
				},
				{
					name: "check4",
					type: "int"
				},
				{
					name: "ItemId",
					type: "int"
				},
				{
					name: "DSG_COLOR",
					type: "string"
				},
				{
					name: "ITEMNAME",
					type: "string"
				},
				{
					name: "GrandTotal",
					type: "int"
				},
				{
					name: "TotalSystemPD",
					type: "int"
				},
				{
					name: "Total",
					type: "int"
				},
				{
					name: "ActualDay1C",
					type: "int"
				},
				{
					name: "ActualDay1D",
					type: "int"
				},
				{
					name: "ActualDay2C",
					type: "int"
				},
				{
					name: "ActualDay2D",
					type: "int"
				},
				{
					name: "ActualDay3C",
					type: "int"
				},
				{
					name: "ActualDay3D",
					type: "int"
				},
				{
					name: "ShiftDay1C",
					type: "int"
				},
				{
					name: "ShiftDay1D",
					type: "int"
				},
				{
					name: "ShiftDay2C",
					type: "int"
				},
				{
					name: "ShiftDay2D",
					type: "int"
				},
				{
					name: "ShiftDay3C",
					type: "int"
				},
				{
					name: "ShiftDay3D",
					type: "int"
				},
				{
					name: "ITEMNAME_LIST",
					type: "string"
				},
				{
					name: "BL",
					type: "int"
				},
				{
					name: "StockStatus",
					type: "int"
				}


			],

			url: '/productionfacetire/sch2/loadplantire?date_sch=' + date_sch + '&shift=' + shift
		});

		return $("#grid_sch").jqxGrid({
			width: '100%',
			source: dataAdapter,
			pageable: true,
			altRows: true,
			columnsResize: true,
			filterable: true,
			editable: true,
			columnsheight: 50,
			pageSize: 10,
			sortable: true,

			columns: [

				{
					text: "กรีนไทร์<BR>ขาดอบ",
					datafield: "OrderLackshift",
					align: 'center',
					width: '50',
					columngroup: 'Order',
					editable: false
				},
				{
					text: "ลำดับออกยาง<BR>ช่อง 1",
					datafield: "check1",
					align: 'center',
					width: '80',
					cellsformat: 'F2',
					columngroup: 'OrderTire',
					editable: false
				},
				{
					text: "ลำดับออกยาง<BR>ช่อง 2",
					datafield: "check2",
					align: 'center',
					width: '80',
					columngroup: 'OrderTire',
					cellsformat: 'F2',
					editable: false
				},
				{
					text: "ลำดับออกยาง<BR>ช่อง 3",
					datafield: "check3",
					align: 'center',
					width: '80',
					columngroup: 'OrderTire',
					editable: false
				},
				{
					text: "ลำดับออกยาง<BR>ช่อง 4",
					datafield: "check4",
					align: 'center',
					width: '80',
					columngroup: 'OrderTire',
					editable: false
				},
				{
					text: "หน้ายาง",
					datafield: "checktotal",
					align: 'center',
					width: '80',
					columngroup: 'OrderTire',
					editable: false
				},
				{
					text: "No.",
					width: 50,
					cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
						return '<div style=\'padding: 5px; color:#000000;\'> ' + (index + 1) + ' </div>';
					}
				},
				{
					text: "Item Id",
					datafield: "ItemId",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Name",
					datafield: "ITEMNAME",
					align: 'center',
					width: '400',
					editable: false
				},
				{
					text: "Color",
					datafield: "DSG_COLOR",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "แผนก<BR>ออกหน้ายาง",
					datafield: "GrandTotal",
					align: 'center',
					columngroup: 'Stockfacetire',
					width: '80',
					editable: false
				},
				{
					text: "แผนก<BR>สร้างโครง",
					datafield: "TotalSystemPD",
					align: 'center',
					columngroup: 'Stockfacetire',
					width: '80',
					editable: false
				},
				{
					text: "Total<BR>(เส้น)",
					datafield: "Total",
					align: 'center',
					width: '50',
					columngroup: 'Stockfacetire',
					editable: false
				},
				{
					text: "กะกลางวัน",
					datafield: "ActualDay1C",
					align: 'center',
					columngroup: 'plan1',
					width: '100',
					editable: false
				},
				{
					text: "กะกลางคืน",
					datafield: "ActualDay1D",
					align: 'center',
					columngroup: 'plan1',
					width: '100',
					editable: false
				},
				{
					text: "กะกลางวัน",
					datafield: "ActualDay2C",
					align: 'center',
					width: '80',
					columngroup: 'plan2',
					editable: false
				},
				{
					text: "กะกลางคืน",
					datafield: "ActualDay2D",
					align: 'center',
					width: '80',
					columngroup: 'plan2',
					editable: false
				},
				{
					text: "กะกลางวัน",
					datafield: "ActualDay3C",
					align: 'center',
					width: '80',
					columngroup: 'plan3',
					editable: false
				},
				{
					text: "กะกลางคืน",
					datafield: "ActualDay3D",
					align: 'center',
					width: '80',
					columngroup: 'plan3',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางวัน",
					datafield: "ShiftDay1C",
					align: 'center',
					columngroup: 'shiftplan1',
					width: '80',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางคืน",
					datafield: "ShiftDay1D",
					align: 'center',
					columngroup: 'shiftplan1',
					width: '80',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางวัน",
					datafield: "ShiftDay2C",
					align: 'center',
					width: '80',
					columngroup: 'shiftplan2',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางคืน",
					datafield: "ShiftDay2D",
					align: 'center',
					width: '80',
					columngroup: 'shiftplan2',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางวัน",
					datafield: "ShiftDay3C",
					align: 'center',
					width: '80',
					columngroup: 'shiftplan3',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางคืน",
					datafield: "ShiftDay3D",
					align: 'center',
					width: '80',
					columngroup: 'shiftplan3',
					editable: false
				},
				{
					text: "Compound",
					datafield: "ITEMNAME_LIST",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "BL",
					datafield: "BL",
					align: 'center',
					width: '50',
					editable: false
				},
				{
					text: "Status<BR>Stock หน้ายาง",
					datafield: "StockStatus",
					align: 'center',
					width: '100',
					columngroup: 'status',
					editable: false
				}
			],
			columnGroups: [{
					text: 'ลำดับ',
					align: 'center',
					name: 'Order'
				},
				{
					text: 'ลำดับออกยาง',
					align: 'center',
					name: 'OrderTire'
				},
				{
					text: 'Stock หน้ายาง',
					align: 'center',
					name: 'Stockfacetire'
				},
				{
					text: "แผนกสร้างโครง<BR>" + $date1,
					align: 'center',
					name: 'plan1'
				},
				{
					text: 'แผนกสร้างโครง<BR>' + $date2,
					align: 'center',
					name: 'plan2'
				},
				{
					text: 'แผนกสร้างโครง<BR>' + $date3,
					align: 'center',
					name: 'plan3'
				},
				{
					text: 'หน้ายางขาด<BR>' + $date1,
					align: 'center',
					name: 'shiftplan1'
				},
				{
					text: 'หน้ายางขาด<BR>' + $date2,
					align: 'center',
					name: 'shiftplan2'
				},
				{
					text: 'หน้ายางขาด<BR>' + $date3,
					align: 'center',
					name: 'shiftplan3'
				},
				{
					text: $date1,
					align: 'center',
					width: '200',
					name: 'status'
				}
			]

		});
	}

	function loadgridsc1(date_sch, shift, $date2, $date3) {


		//alert(dddaa);

		$date2 = $date2;
		$date3 = $date3;
		$date1 = date_sch;
		// $date2 = 2;
		// $date3 = 3;
		//alert($date1 + "," + $date2 + "," + $date3);
		$('#message_statusload').html('<strong>Loading</strong>');

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: "json",

			datafields: [


				{
					name: "OrderLackshift",
					type: "int"
				},
				{
					name: "check1",
					type: "float"
				},
				{
					name: "check2",
					type: "float"
				},

				{
					name: "ItemId",
					type: "int"
				},
				{
					name: "DSG_COLOR",
					type: "string"
				},
				{
					name: "ITEMNAME",
					type: "string"
				},
				{
					name: "GrandTotal",
					type: "int"
				},
				{
					name: "TotalSystemPD",
					type: "int"
				},
				{
					name: "Total",
					type: "int"
				},
				{
					name: "ActualDay1C",
					type: "int"
				},
				{
					name: "ActualDay1D",
					type: "int"
				},
				{
					name: "ActualDay2C",
					type: "int"
				},
				{
					name: "ActualDay2D",
					type: "int"
				},
				{
					name: "ActualDay3C",
					type: "int"
				},
				{
					name: "ActualDay3D",
					type: "int"
				},
				{
					name: "ShiftDay1C",
					type: "int"
				},
				{
					name: "ShiftDay1D",
					type: "int"
				},
				{
					name: "ShiftDay2C",
					type: "int"
				},
				{
					name: "ShiftDay2D",
					type: "int"
				},
				{
					name: "ShiftDay3C",
					type: "int"
				},
				{
					name: "ShiftDay3D",
					type: "int"
				},
				{
					name: "ITEMNAME_LIST",
					type: "string"
				},
				{
					name: "BL",
					type: "int"
				},
				{
					name: "StockStatus",
					type: "int"
				},
				{
					name: "check3",
					type: "float"
				},
				{
					name: "checktotal",
					type: "float"
				}


			],

			url: '/productionfacetire/sch2/loadplantiregroup1?date_sch=' + date_sch + '&shift=' + shift
		});

		return $("#grid_sch1").jqxGrid({
			width: '100%',
			source: dataAdapter,
			pageable: true,
			altRows: true,
			columnsResize: true,
			filterable: true,
			editable: true,
			columnsheight: 50,
			pageSize: 10,
			sortable: true,

			columns: [

				{
					text: "กรีนไทร์<BR>ขาดอบ",
					datafield: "OrderLackshift",
					align: 'center',
					width: '50',
					columngroup: 'Order',
					editable: false
				},
				{
					text: "ลำดับออกยาง<BR>ช่อง 1",
					datafield: "check1",
					align: 'center',
					width: '80',
					columngroup: 'OrderTire',
					cellsformat: 'F2',
					editable: false
				},
				{
					text: "ลำดับออกยาง<BR>ช่อง 2",
					datafield: "check2",
					align: 'center',
					width: '80',
					columngroup: 'OrderTire',
					cellsformat: 'F2',
					editable: false
				},
				{
					text: "ลำดับออกยาง<BR>ช่อง 3",
					datafield: "check3",
					align: 'center',
					width: '80',
					columngroup: 'OrderTire',
					editable: false
				},
				{
					text: "หน้ายาง",
					datafield: "checktotal",
					align: 'center',
					width: '80',
					columngroup: 'OrderTire',
					editable: false
				},
				{
					text: "No.",
					width: 50,
					cellsrenderer: function(index, datafield, value, defaultvalue, column, rowdata) {
						return '<div style=\'padding: 5px; color:#000000;\'> ' + (index + 1) + ' </div>';
					}
				},
				{
					text: "Item Id",
					datafield: "ItemId",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Name",
					datafield: "ITEMNAME",
					align: 'center',
					width: '400',
					editable: false
				},
				{
					text: "Color",
					datafield: "DSG_COLOR",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "แผนก<BR>ออกหน้ายาง",
					datafield: "GrandTotal",
					align: 'center',
					columngroup: 'Stockfacetire',
					width: '80',
					editable: false
				},
				{
					text: "แผนก<BR>สร้างโครง",
					datafield: "TotalSystemPD",
					align: 'center',
					columngroup: 'Stockfacetire',
					width: '80',
					editable: false
				},
				{
					text: "Total<BR>(เส้น)",
					datafield: "Total",
					align: 'center',
					width: '50',
					columngroup: 'Stockfacetire',
					editable: false
				},
				{
					text: "กะกลางวัน",
					datafield: "ActualDay1C",
					align: 'center',
					columngroup: 'plan1',
					width: '100',
					editable: false
				},
				{
					text: "กะกลางคืน",
					datafield: "ActualDay1D",
					align: 'center',
					columngroup: 'plan1',
					width: '100',
					editable: false
				},
				{
					text: "กะกลางวัน",
					datafield: "ActualDay2C",
					align: 'center',
					width: '80',
					columngroup: 'plan2',
					editable: false
				},
				{
					text: "กะกลางคืน",
					datafield: "ActualDay2D",
					align: 'center',
					width: '80',
					columngroup: 'plan2',
					editable: false
				},
				{
					text: "กะกลางวัน",
					datafield: "ActualDay3C",
					align: 'center',
					width: '80',
					columngroup: 'plan3',
					editable: false
				},
				{
					text: "กะกลางคืน",
					datafield: "ActualDay3D",
					align: 'center',
					width: '80',
					columngroup: 'plan3',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางวัน",
					datafield: "ShiftDay1C",
					align: 'center',
					columngroup: 'shiftplan1',
					width: '80',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางคืน",
					datafield: "ShiftDay1D",
					align: 'center',
					columngroup: 'shiftplan1',
					width: '80',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางวัน",
					datafield: "ShiftDay2C",
					align: 'center',
					width: '80',
					columngroup: 'shiftplan2',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางคืน",
					datafield: "ShiftDay2D",
					align: 'center',
					width: '80',
					columngroup: 'shiftplan2',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางวัน",
					datafield: "ShiftDay3C",
					align: 'center',
					width: '80',
					columngroup: 'shiftplan3',
					editable: false
				},
				{
					text: "หน้ายางขาด<BR>กะกลางคืน",
					datafield: "ShiftDay3D",
					align: 'center',
					width: '80',
					columngroup: 'shiftplan3',
					editable: false
				},
				{
					text: "Compound",
					datafield: "ITEMNAME_LIST",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "BL",
					datafield: "BL",
					align: 'center',
					width: '50',
					editable: false
				},
				{
					text: "Status<BR>Stock หน้ายาง",
					datafield: "StockStatus",
					align: 'center',
					width: '100',
					columngroup: 'status',
					editable: false
				}
			],
			columnGroups: [{
					text: 'ลำดับ',
					align: 'center',
					name: 'Order'
				},
				{
					text: 'ลำดับออกยาง',
					align: 'center',
					name: 'OrderTire'
				},
				{
					text: 'Stock หน้ายาง',
					align: 'center',
					name: 'Stockfacetire'
				},
				{
					text: 'แผนกสร้างโครง<BR>' + $date1,
					align: 'center',
					name: 'plan1'
				},
				{
					text: 'แผนกสร้างโครง<BR>' + $date2,
					align: 'center',
					name: 'plan2'
				},
				{
					text: 'แผนกสร้างโครง<BR>' + $date3,
					align: 'center',
					name: 'plan3'
				},
				{
					text: 'หน้ายางขาด<BR>' + $date1,
					align: 'center',
					name: 'shiftplan1'
				},
				{
					text: 'หน้ายางขาด<BR>' + $date2,
					align: 'center',
					name: 'shiftplan2'
				},
				{
					text: 'หน้ายางขาด<BR>' + $date3,
					align: 'center',
					name: 'shiftplan3'
				},
				{
					text: $date1,
					align: 'center',
					name: 'status'
				}
			]

		});
	}
</script>