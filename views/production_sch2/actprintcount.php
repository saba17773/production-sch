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

	.green {
		color: black !important;
		background-color: #CCFFCC !important;
	}
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
			<td width="35%">
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

<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_generate_prosch') === true) : ?>
	<button class="btn btn-info" id="btn_generate">
		<span class="glyphicon glyphicon-list-alt"></span> Generate Scheduler
	</button>
<?php endif ?>
<br><br>
<div class="mb-2">
	<button class="btn btn-primary" id="btnAddGreentire">
		<i class="fa fa-plus"></i> เพิ่มรายการ
	</button>

	<button class="btn btn-warning" id="btnAddReloadrow">
		Reload row
	</button>

</div>

<p id="txtcomplete"></p>

<hr>
<div class="alert alert-danger" role="alert" id="message_checkdata"></div>
<!-- grid sch -->
<div id="grid_sch"></div>


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

				<button type="button" class="btn btn-danger pull-right" id="btn_deleteRemark">
					<span class="glyphicon glyphicon-trash"></span> ลบ</button>

				<h4 class="modal-title">หมายเหตุ</h4>
			</div>
			<div class="modal-body">
				<form id="form_remark">
					<div class="form-group">
						<input type="hidden" name="id_trans" id="id_trans">
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

<div class="modal" id="modal_generate" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
				<h5 class="modal-title">โปรดเลือกวันที่ต้องการข้อมูล</h5>
			</div>

			<div class="modal-body">
				<form id="form_generate">
					<table>
						<tr>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 150px;">
									<input type="text" id="date_gen" name="date_gen" class="form-control" required placeholder="เลือกวันที่..." autocomplete="off" />
									<span class="input-group-btn">
										<button class="btn btn-info" id="date_gen_show" type="button">
											<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
										</button>
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<td style="padding: 5px;" align="center">
								<div>
									<label class="radio-inline">
										<input type="radio" name="gen_shift" id="gen_shift1" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
										<span style="padding-left: 10px;"><b>C 08.00-20.00</b></span>
										<BR><BR>
										<input type="radio" name="gen_shift" id="gen_shift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
										<span style="padding-left: 10px;"><b>D 20.00-08.00</b></span>
									</label>
									<!-- <span style="padding-left: 30px;"> </span>
				          <label class="radio-inline">
				          <input type="radio" name="gen_shift" id="gen_shift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
				          <span style="padding-left: 10px;"><b>D 20.00-08.00</b></span>
				          </label> -->
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<hr>
							</td>
						</tr>
						<tr>
							<td>
								<hr>
							</td>
						</tr>

						<tr>
							<td style="padding: 10px;" align="center">
								<button class="btn btn-success btn-sm" id="btn_generate_all">
									<span class="glyphicon glyphicon-ok"></span> Generate Copy
								</button>
								<button class="btn btn-primary btn-sm" id="btn_generate_item">
									<span class="glyphicon glyphicon-ok"></span> Generate New
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
			checkgridsch($("#date_sch").val(), shift);
		});

		$('#date_sch_show').click(function() {
			$('#date_sch').datepicker('show');
		});
		$('#date_gen_show').click(function() {
			$('#date_gen').datepicker('show');
		});
		$('#date_gen_emp_show').click(function() {
			$('#date_gen_emp').datepicker('show');
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

		$("#btn_sendmail").on('click', function(event) {
			$('#btn_sendmail').text('กำลังส่ง...');
			$('#btn_sendmail').attr('disabled', true);

			gojax('post', '/production/sch/sendmail/sch', {
				date_sch: $("#date_sch").val(),
				shift: shift
			}).done(function(data) {
				if (data.result == 200) {
					setTimeout(function() {
						loadgridsch($("#date_sch").val(), shift);
						alert("ดำเนินการส่งเมลล์เรียบร้อยแล้ว");
						$('#btn_sendmail').text('ส่งอีเมลล์');
						$('#btn_sendmail').attr('disabled', false);
					}, 1000);
					// console.log(data);
				} else {
					$('#btn_sendmail').text('ส่งอีเมลล์');
					$('#btn_sendmail').attr('disabled', false);
					// console.log(data);
				}
			});

		});

		$("#btn_generate").on('click', function(event) {
			$('#modal_generate').modal({
				backdrop: 'static'
			});
			$('#gen_emp_date').hide();
			$('#gen_emp_shift').hide();

			$('#chk_emp').prop('checked', false);

			$("#chk_emp").on('change', function() {

				if ($(this).is(':checked')) {
					$('#check_emp').val(1);
					$('#gen_emp_date').show();
					$('#gen_emp_shift').show();
				} else {
					$('#check_emp').val(0);
					$('#gen_emp_date').hide();
					$('#gen_emp_shift').hide();
				}

			});

		});
		$("#btnAddGreentire").on('click', function(event) {

			var gen_shift = 0;
			var copy = 0;
			var shift = $("input[name=shift]:checked").val();
			var gen_emp = 0;
			var shift_emp = 0;
			var date_emp = 0;

			//alert($("#date_gen").val());


			gen_sch($("#date_gen").val(), gen_shift, copy, $("#date_sch").val(), shift, gen_emp, date_emp, shift_emp);
			//	return false;


		});
		$("#btnAddReloadrow").on('click', function(event) {

			$('#grid_sch').jqxGrid('updateBoundData', 'cells');
		});




		$('#grid_item').on('rowdoubleclick', function(event) {
			var args = event.args;
			var boundIndex = args.rowindex;
			var datarowItem = $("#grid_item").jqxGrid('getrowdata', boundIndex);

			$('#itemid').val(datarowItem.ItemGT);
			if ($("input[name=shift]:checked").val() == 1) {
				var gen_shift = 1;
			} else if ($("input[name=shift]:checked").val() == 2) {
				var gen_shift = 2;
			}

			if (!!datarowItem.ItemGT) {
				gojax('post', '/sch2/sch/add/itemprint', {
					itemid: $('#itemid').val(),
					id: $('#id_trans').val(),
					sch_date: $('#date_sch').val(),
					shift: gen_shift

				}).done(function(data) {
					if (data.result == 200) {
						$('#grid_item').jqxGrid('clearselection');
						$('#modal_item').modal('hide');
						$('#grid_sch').jqxGrid('updateBoundData', 'cells');

					} else {
						// console.log(data);
						alert('ไม่สามารถเพิ่มรายการได้')
					}
				});
			}


		});



		$('#btn_generate_all').on('click', function(event) {
			$('#btn_generate_all').attr('disabled', true);
			$('#btn_generate_item').attr('disabled', true);
			if ($("input[name=gen_shift]:checked").val() == 1) {
				var gen_shift = 1;
			} else if ($("input[name=gen_shift]:checked").val() == 2) {
				var gen_shift = 2;
			}
			var copy = 1

			if ($('#check_emp').val() == 1) {
				var gen_emp = 1;
				var date_emp = $('#date_gen_emp').val();
				if ($("input[name=shift_gen_emp]:checked").val() == 1) {
					var shift_emp = 1;
				} else if ($("input[name=shift_gen_emp]:checked").val() == 2) {
					var shift_emp = 2;
				}
			}

			gen_sch($("#date_gen").val(), gen_shift, copy, $("#date_sch").val(), shift, gen_emp, date_emp, shift_emp);
			return false;
		});
		$('#btn_generate_item').on('click', function(event) {
			$('#btn_generate_item').attr('disabled', true);
			$('#btn_generate_all').attr('disabled', true);
			if ($("input[name=gen_shift]:checked").val() == 1) {
				var gen_shift = 1;
			} else if ($("input[name=gen_shift]:checked").val() == 2) {
				var gen_shift = 2;
			}
			var copy = 0

			if ($('#check_emp').val() == 0) {
				var gen_emp = 0;
				var date_emp = $('#date_gen_emp').val();
				var shift_emp = $('#shift_gen_emp').val();
				if ($("input[name=shift_gen_emp]:checked").val() == 1) {
					var shift_emp = 1;
				} else if ($("input[name=shift_gen_emp]:checked").val() == 2) {
					var shift_emp = 2;
				}
			}

			gen_sch($("#date_gen").val(), gen_shift, copy, $("#date_sch").val(), shift, gen_emp, date_emp, shift_emp);
			return false;
		});

	});

	function checkgridsch(date_sch, shift) {

		gojax('get', '/sch2/sch/data/checkprint', {
			date_sch: date_sch,
			shift: shift
		}).done(function(data) {
			if (data.result == true) {
				loadgridsch(date_sch, shift);
				$("#grid_sch").show();
				$('#message_checkdata').hide();
			} else {
				if (data.status == 3) {
					loadgridsch(date_sch, shift);
					$("#grid_sch").show();
					$('#message_checkdata').show();
					$('#message_checkdata').html('<strong>' + data.message + '</strong>');
				} else {
					$("#grid_sch").hide();
					$('#message_checkdata').show();
					$('#message_checkdata').html('<strong>' + data.message + '</strong>');
				}
			}
		});

	}

	function loadgridsch(date_sch, shift) {

		if (shift == 1) {
			var datashift = "แผนสร้างกะกลางวัน (เส้น)";
			var dataplan = "ผลิตได้ กะกลางวัน (เส้น)";
			var plan1 = "แผนสร้าง<BR>กะกลางวัน (เส้น)";
			var plan2 = "แผนผลิต<BR>กะกลางคืน";
		} else {
			var datashift = "แผนสร้างกะกลางคืน (เส้น)";
			var dataplan = "ผลิตได้ กะกลางคืน (เส้น)";
			var plan1 = "แผนสร้าง<BR>กะกลางคืน (เส้น)";
			var plan2 = "แผนผลิต<BR>กะกลางวัน";
		}
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: "json",
			updaterow: function(rowid, rowdata, commit) {
				gojax('post', '/sch2/sch/update/schprintTable', {
					SumPrint: rowdata.SumPrint,
					Countprintcure: rowdata.Countprintcure,
					Rateprint: rowdata.Rateprint,
					CountPrintcurFG: rowdata.CountPrintcurFG,
					RatePrintFG: rowdata.RatePrintFG,
					CountCure: rowdata.CountCure,
					SpareOfcure: rowdata.SpareOfcure,
					id: rowdata.ID

				}).done(function(data) {
					if (data.status === 200) {
						// $('#grid_sch').jqxGrid('updateBoundData', 'cells');
						commit(true);
					} else {
						// $('#grid_sch').jqxGrid('updateBoundData', 'cells');
					}
					// console.log(data);
				}).fail(function() {
					commit(false);
				});


			},
			datafields: [

				{
					name: "ID",
					type: "int"
				},
				{
					name: "ItemId",
					type: "string"
				},
				{
					name: "ItemGTName",
					type: "string"
				},
				{
					name: "Color",
					type: "string"
				},
				{
					name: "SumPrint",
					type: "int"
				},
				{
					name: "TotalPrint",
					type: "string"
				},
				{
					name: "Time",
					type: "int"
				},
				{
					name: "Countprintcure",
					type: "int"
				},
				{
					name: "Rateprint",
					type: "int"
				},
				{
					name: "TimeCureFG",
					type: "int"
				},
				{
					name: "CountPrintcurFG",
					type: "int"
				},
				{
					name: "RatePrintFG",
					type: "int"
				},
				{
					name: "GreentireShift",
					type: "int"
				},
				{
					name: "GreentireDay",
					type: "int"
				},
				{
					name: "CountCure",
					type: "int"
				},
				{
					name: "SpareOfcure",
					type: "int"
				},
				{
					name: "StockInplan",
					type: "int"
				},
				{
					name: "Total",
					type: "int"
				},
				{
					name: "TotalHours",
					type: "int"
				},
				{
					name: "PersenGreentire",
					type: "float"
				},
				{
					name: "LackShift",
					type: "int"
				},
				{
					name: "TargetTemp",
					type: "int"
				},
				{
					name: "OrderLackshift",
					type: "int"
				},
				{
					name: "LackShift2",
					type: "int"
				},
				{
					name: "CountPrint",
					type: "int"
				},
				{
					name: "CureDay",
					type: "int"
				},
				{
					name: "BL",
					type: "int"
				},
				{
					name: "Actual",
					type: "int"
				},
				{
					name: "TireLackShift",
					type: "int"
				},
				{
					name: "TireLackDay",
					type: "int"
				},
				{
					name: "TotalShareMold",
					type: "int"
				},
				{
					name: "GroupId",
					type: "string"
				}


			],
			// sortcolumn: 'CurID',
			// sortdirection: 'asc',
			url: '/productionPrint/sch2/load?date_sch=' + date_sch + '&shift=' + shift
		});

		var setDelete = function(row, column, value) {
			if (value !== "") {
				return "<div style='padding:4px;'>" + value + "</div>";
			} else {
				return "<div style='font-size: 1em; padding:3px;'><button style='width:18px; height:18px; padding: 0.2px;' class='btn btn-danger' onclick='return setDelete(" + row + ")' style=' width:25px;'><b>X</b></button></div>";
			}

		}



		var setItem = function(row, column, value) {
			if (value !== "") {
				return "<div style='padding:4px;'>" + value + "</div>";
			} else {
				return "<div style='font-size: 1em; padding:3px;'><button style='width:60px; height:21px; padding: 0.2px;' class='btn btn-success' onclick='return setItemModal(" + row + ")'>เพิ่มพิมพ์</button></div>";
			}

		}
		var x = 0;
		var color = 'sky';
		var cellclass = function(row, columnfield, value) {

			if (value === null || value === "") {
				x = 0;
				return 'white';

			} else if (value !== x && value !== null && value !== "") {
				x = value;
				if (color === 'sky') {
					color = 'green'
				} else {
					color = 'sky'
				}
				return color;
			} else return color;
			//	alert("data=>"+x+"value=>"+value);

		};



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
			selectionmode: 'singlecell',
			editmode: 'click',
			autoheight: true,
			rowsheight: 32,
			pagesizeoptions: [12, 24],
			//  groups: ['BoilerName'],
			//       groupsRenderer: function(value, rowData, level)
			//       {
			// boiler = value.substring(0, value.indexOf('/'));
			// boiler = boiler.split('_').pop();
			// var boiler_ = value.split('_').pop();
			//  return '<b>เตา : ' + boiler_ + '</b>';
			//       },
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
					datafield: "ItemId",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Item Name",
					datafield: "ItemGTName",
					align: 'center',
					width: '400',
					editable: false
				},
				{
					text: "",
					cellsrenderer: setItem,
					width: '80',
					editable: false
				},
				{
					text: "Color",
					datafield: "Color",
					align: 'center',
					editable: false,
					width: '100'
				},
				{
					text: "จำนวนพิมพ์<BR>ทั้งหมด",
					datafield: "TotalShareMold",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "Group",
					datafield: "GroupId",
					align: 'center',
					width: '80',
					editable: false,
					cellclassname: cellclass,
				},
				{
					text: "จำนวนพิมพ์<BR>เปิดอบ",
					datafield: "Countprintcure",
					align: 'center',
					width: '80',
					editable: true
				},
				{
					text: "Rate(เส้น)<BR>กะ/พิมพ์",
					datafield: "Rateprint",
					align: 'center',
					width: '80',
					editable: true
				},
				{
					text: "อบยางเบิก<BR>(เส้น)",
					datafield: "CountCure",
					align: 'center',
					width: '80',
					editable: false
				}, // chang key to data cure facereport 2020-07-13
				{
					text: "Spareหน้าเตา<BR>(เส้น)",
					datafield: "SpareOfcure",
					align: 'center',
					width: '80',
					editable: false
				}, // chang key to data cure facereport 2020-07-13
				{
					text: "จำนวนพิมพ์<BR>เปิดอบ",
					datafield: "CountPrintcurFG",
					align: 'center',
					columngroup: 'printcureFG',
					editable: true,
					width: '80'
				},
				{
					text: "Rate(เส้น)<BR>กะ/พิมพ์",
					datafield: "RatePrintFG",
					align: 'center',
					columngroup: 'printcureFG',
					editable: true,
					width: '80'
				},
				{
					text: "เวลาอบ<BR>(นาที)",
					datafield: "TimeCureFG",
					align: 'center',
					columngroup: 'printcureFG',
					editable: false,
					width: '50'
				},


				//---------------------------- key data -----------------------------------------------------------
				{
					text: "จำนวนพิมพ์<BR>คงเหลือ",
					datafield: "TotalPrint",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "เวลาอบ<BR>(นาที)",
					datafield: "Time",
					align: 'center',
					width: '50',
					editable: false
				},
				{
					text: "เส้น/กะ",
					datafield: "GreentireShift",
					align: 'center',
					columngroup: 'Greentire',
					editable: false,
					width: '50'
				},
				{
					text: "เส้น/วัน",
					datafield: "GreentireDay",
					align: 'center',
					columngroup: 'Greentire',
					editable: false,
					width: '50'
				},
				{
					text: "แผนกกรีนไทร์<BR>(เส้น)",
					datafield: "StockInplan",
					align: 'center',
					columngroup: 'Stock',
					editable: false,
					width: '80'
				},
				{
					text: "หน้าเตา+<BR>แผนกกรีนไทร์",
					datafield: "Total",
					align: 'center',
					columngroup: 'Stock',
					editable: false,
					width: '5%'
				},
				{
					text: "ชม.",
					datafield: "TotalHours",
					align: 'center',
					columngroup: 'amountGreentire',
					editable: false,
					width: '50'
				},
				{
					text: "%",
					datafield: "PersenGreentire",
					align: 'center',
					columngroup: 'amountGreentire',
					editable: false,
					width: '50',
					cellsformat: 'F2'
				},
				{
					text: "กรีนไทร์<BR>ขาดในกะ(เส้น)",
					datafield: "LackShift",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: plan1,
					datafield: "TargetTemp",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "ลำดับกรีนไทร์<BR>ขาดอบ",
					datafield: "OrderLackshift",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "ยางขาด<BR>สร้างในกะ",
					datafield: "LackShift2",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "อบได้/วัน",
					datafield: "CureDay",
					align: 'center',
					columngroup: 'Type',
					width: '80',
					editable: false
				},
				{
					text: "จำนวนพิมพ์",
					datafield: "CountPrint",
					align: 'center',
					columngroup: 'Type',
					width: '80',
					editable: false
				},
				{
					text: "Balance<BR> เส้น",
					datafield: "BL",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: plan2,
					datafield: "Actual",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "ยางขาด<BR> ต่อ กะ",
					datafield: "TireLackShift",
					align: 'center',
					width: '50',
					editable: false
				},
				{
					text: "ยางขาด<BR> ต่อ วัน",
					datafield: "TireLackDay",
					align: 'center',
					width: '100',
					editable: false
				},

				// { text:"นับจริง", datafield: "CountReal", width:'7.5%', editable:true,cellsformat: 'F2'},
				//  { text:"เปรียบเทียบนับจริง & ในระบบ",datafield: "Chekdata", width:'7.5%', editable:false,cellsformat: 'F2'},
				// { text:"เปรียบเทียบ สร้างโครง ผลิตได้รับเข้า",datafield: "CheckCountShift", width:'7.5%', editable:false},
				// { text:"เปรียบเทียบ อบยางเบิก จ่ายออก",datafield: "CheckCountOut", width:'7.5%', editable:false},
				{
					text: "",
					cellsrenderer: setDelete,
					width: '100',
					editable: false
				}



			],
			columnGroups: [{
					text: 'พิมพ์อบร่วม',
					align: 'center',
					name: 'printcureFG'
				},
				{
					text: 'กรีนไทร์',
					align: 'center',
					name: 'Greentire'
				},
				{
					text: 'Stock',
					align: 'center',
					name: 'Stock'
				},
				{
					text: 'เหลือกรีนไทร์',
					align: 'center',
					name: 'amountGreentire'
				},
				{
					text: 'แยกตามชนิด Type',
					align: 'center',
					name: 'Type'
				}
			]

		});
	}





	function setItemModal(row) {
		var transid = $("#grid_sch").jqxGrid('getCellValue', row, 'ID');
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
		gojax('post', '/sch2/sch/delete/rowprint', {
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



	function gen_sch(date_gen, gen_shift, copy, date_sch, shift, gen_emp, date_emp, shift_emp) {
		gojax('post', '/productionRecive/sch2/gen/schprint', {
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