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
	<button class="btn btn-primary" id="btnAddCar">
		<i class="fa fa-plus"></i> คันรถ
	</button>
	<button class="btn btn-default" id="btn_reload">
		<span class="glyphicon glyphicon-refresh"></span> Reload
	</button>
	<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_generate_prosch') === true) : ?>
		<button class="btn btn-default" id="btn_reload_row">
			<span class="glyphicon glyphicon-refresh"></span> Reload Row
		</button>
	<?php endif ?>


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

		$("#btn_reload").on('click', function(event) {

			var gen_shift = 0;
			var copy = 0;
			var shift = $("input[name=shift]:checked").val();
			var gen_emp = 0;
			var shift_emp = 0;
			var date_emp = 0;

			//alert(shift);


			gen_schupdate($("#date_sch").val(), shift);
			//	return false;


		});

		$("#btnAddCar").on('click', function(event) {
			if ($("input[name=shift]:checked").val() == 1) {
				var gen_shift = 1;
			} else if ($("input[name=shift]:checked").val() == 2) {
				var gen_shift = 2;
			}
			var date_sch = $("input[name=date_sch]").val()

			//	alert(gen_shift);
			window.open('/insertcar?date_sch=' + date_sch + '&shift=' + gen_shift);

		});

		$("#btn_reload_row").on("click", function() {
			var gen_shift = 0;
			var copy = 0;
			var shift = $("input[name=shift]:checked").val();
			var gen_emp = 0;
			var shift_emp = 0;
			var date_emp = 0;

			//alert(shift);


			gen_schupdateStock($("#date_sch").val(), shift);
			//return false;
			//$('#grid_sch').jqxGrid('updateBoundData', 'cells');
		});


		$('#grid_item').on('rowdoubleclick', function(event) {
			var args = event.args;
			var boundIndex = args.rowindex;
			var datarowItem = $("#grid_item").jqxGrid('getrowdata', boundIndex);
			//alert($('#id_trans').val());
			//
			//  console.log($('#id_trans').val());
			$('#itemid').val(datarowItem.ItemGT);
			// $('#ratecure').val(datarowItem.RateCure);
			// $('#netweight').val(datarowItem.NetWeight);
			// $('#id_trans').val();
			if (!!datarowItem.ItemGT) {
				gojax('post', '/sch2/sch/add/itemDisburs', {
					itemid: $('#itemid').val(),
					id: $('#id_trans').val()

				}).done(function(data) {
					if (data.result == 200) {
						$('#grid_item').jqxGrid('clearselection');
						$('#modal_item').modal('hide');
						$('#grid_sch').jqxGrid('updateBoundData', 'cells');
						//  alert(itemid);
					} else {
						// console.log(data);
					}
				});
			}

			//  console.log(itemid);
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

		gojax('get', '/sch2/sch/data/checkdisbursement', {
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
			var datashift = "แผนผลิต<BR>กะกลางวัน(เส้น)";
			var datashift1 = "แผนผลิต<BR>กะกลางคืน(เส้น)";
			var datashift2 = "ผลิตได้<BR>กะกลางวัน(เส้น)";
		} else {
			var datashift = "แผนผลิต<BR>กะกลางคืน(เส้น)";
			var datashift1 = "แผนผลิต<BR>กะกลางวัน(เส้น)";
			var datashift2 = "ผลิตได้<BR>กะกลางคืน(เส้น)";
		}
		var dataAdapter = new $.jqx.dataAdapter({
			datatype: "json",
			updaterow: function(rowid, rowdata, commit) {
				gojax('post', '/sch2/sch/update/UpdateSchDisburTable', {
					TireNotSpac: rowdata.TireNotSpac,
					date_sch: date_sch,
					shift: shift,
					CalStock: rowdata.CalStock,
					// CountOut 	 : rowdata.CountOut,
					// CountNotSpec	 : rowdata.CountNotSpec,
					// CountReal	 : rowdata.CountReal,
					id: rowdata.ID

				}).done(function(data) {
					if (data.status === 200) {
						//	$('#grid_sch').jqxGrid('updateBoundData', 'cells');
						commit(true);
					} else {
						//	$('#grid_sch').jqxGrid('updateBoundData', 'cells');
					}

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
					name: "Target",
					type: "int"
				},
				{
					name: "Target1",
					type: "int"
				},
				{
					name: "Actual",
					type: "int"
				},
				{
					name: "Stock",
					type: "int"
				},
				{
					name: "Total",
					type: "int"
				},
				{
					name: "TireNotSpac",
					type: "int"
				},
				{
					name: "TotalSystem",
					type: "int"
				},
				{
					name: "CheckCountOut",
					type: "string"
				},
				{
					name: "BL",
					type: "int"
				},
				{
					name: "CompareNum",
					type: "int"
				},
				{
					name: "CompareBill",
					type: "string"
				},
				{
					name: "CountNum",
					type: "CountNum"
				},
				{
					name: "Produce",
					type: "int"
				},
				{
					name: "TotalPayOfCar",
					type: "int"
				},
				{
					name: "Stock2",
					type: "int"
				},
				{
					name: "CalStock",
					type: "int"
				}
				//   { name: "CountPlan", type: "int"}
				//   { name: "SchDate", type: "date"},
				//   { name: "Shift", type: "int"}
			],
			// sortcolumn: 'CurID',
			// sortdirection: 'asc',
			url: '/ProductionGreentireDisburs/sch2/loadtire?date_sch=' + date_sch + '&shift=' + shift
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
			selectionmode: 'singlecell',
			editmode: 'click',
			autoheight: true,
			rowsheight: 32,
			pagesizeoptions: [12, 24],

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
					text: "",
					cellsrenderer: setItem,
					width: '100',
					editable: false
				},
				{
					text: "Color",
					datafield: "Color",
					align: 'center',
					width: '150'
				},
				{
					text: datashift,
					datafield: "Target",
					align: 'center',
					width: '100',
					columngroup: 'ProductPlan'
				},
				{
					text: datashift1,
					datafield: "Target1",
					align: 'center',
					width: '100',
					columngroup: 'ProductPlan',
					editable: true
				},
				{
					text: datashift2,
					datafield: "Actual",
					align: 'center',
					width: '100',
					columngroup: 'ProductPlan',
					editable: true
				},
				{
					text: "เพิ่ม/ลด<BR>Stock",
					datafield: "CalStock",
					align: 'center',
					width: '80',
					editable: true
				},
				{
					text: "Stock<BR>ของกะก่อนหน้า",
					datafield: "Stock2",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Stock",
					datafield: "Stock",
					align: 'center',
					width: '50',
					editable: false
				},
				{
					text: "เบิกจาก<BR>แผนกหน้ายาง",
					datafield: "Total",
					align: 'center',
					width: '90',
					editable: true
				},
				{
					text: "ยางไม่ได้<BR>Spec/ยางเก็บงาน",
					datafield: "TireNotSpac",
					align: 'center',
					width: '115',
					editable: true
				},
				{
					text: "ผลิตได้",
					datafield: "Produce",
					align: 'center',
					width: '50',
					editable: false
				},
				{
					text: "คงเหลือ<BR>ในระบบ",
					datafield: "TotalSystem",
					align: 'center',
					width: '80',
					editable: false
				},
				{
					text: "ไม่มีหน้ายาง<BR>แต่มีตัวเลขสร้าง",
					datafield: "CheckCountOut",
					align: 'center',
					width: '100',
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
					text: "นับจริง",
					datafield: "CountNum",
					align: 'center',
					width: '50',
					editable: false
				},
				{
					text: "เปรียบเทียบ<BR>นับจริง&ในระบบ",
					datafield: "CompareNum",
					align: 'center',
					width: '120',
					editable: false
				},
				{
					text: "แผนกหน้ายาง<BR>จ่ายออก",
					datafield: "TotalPayOfCar",
					align: 'center',
					width: '100'
				},
				{
					text: "เปรียบเทียบ<BR>เบิก-จ่าย",
					datafield: "CompareBill",
					align: 'center',
					width: '100'
				},
				// { text:"เปรียบเทียบ<BR>อบยางเบิก จ่ายออก",datafield: "CheckCountOut", align: 'center', width:'150', editable:false},
				{
					text: "",
					cellsrenderer: setDelete,
					width: '80',
					align: 'center',
					editable: false
				}



			],
			columnGroups: [{
				text: 'แผนผลิตแผนกสร้างโครงประจำวัน',
				align: 'center',
				name: 'ProductPlan'
			}]

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
			url: '/sch2/sch2/load/itemEXT'
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

	function gen_schupdate(date_sch, shift) {
		gojax('post', '/productionRecive/sch2/gen/updatebilltire', {

			date_sch: date_sch,
			shift: shift

		}).done(function(data) {
			//	console.log(data);
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

	function gen_schupdateStock(date_sch, shift) {
		gojax('post', '/productionRecive/sch2/gen/updatebilltireStock', {

			date_sch: date_sch,
			shift: shift

		}).done(function(data) {
			//	console.log(data);
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
</script>