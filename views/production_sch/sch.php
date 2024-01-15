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
						<span style="padding-left: 10px;"><b>(08:00-20:00)</b></span>
					</label>
					<span style="padding-left: 30px;"> </span>
					<label class="radio-inline">
						<input type="radio" name="shift" id="shift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
						<span style="padding-left: 10px;"><b>(20:00-08:00)</b></span>
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

<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_sendmail_prosch') === true) : ?>
	<!-- <button class="btn btn-info" id="btn_sendmail">
		<span class="glyphicon glyphicon-envelope"></span> Send E-mail
	</button> -->
<?php endif ?>

<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_generate_prosch') === true) : ?>
	<button class="btn btn-default" id="btn_reload">
		<span class="glyphicon glyphicon-refresh"></span> Reload
	</button>
<?php endif ?>

<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_production_sch_confirm1') === true) : ?>
	<button class="btn btn-warning" id="btn_confirm">
		<span class="glyphicon glyphicon-lock"></span> Confirm
	</button>
<?php endif ?>

<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_production_sch_confirm1') === true) : ?>
	<button class="btn btn-danger" id="btn_confirmed">
		<span class="glyphicon glyphicon-lock"></span> Confirmed
	</button>
<?php endif ?>

<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_production_sch_unconfirm') === true) : ?>
	<button class="btn btn-default" id="btn_unconfirm">
		<span class="glyphicon glyphicon-repeat"></span> UnLock
	</button>
<?php endif ?>
<?php if ($PermissionService->getUserAction($_SESSION['user_permission'], 'btn_production_sch_unconfirm') === true) : ?>
	<button class="btn btn-primary" id="btn_buybill">
		<span class="glyphicon glyphicon-plus"></span> เบิกจ่าย
	</button>
<?php endif ?>
<input type="hidden" id="confirm_x">
<p id="txtcomplete"></p>

<hr>
<div class="alert alert-danger" role="alert" id="message_checkdata"></div>
<!-- grid sch -->
<div id="grid_sch"></div>


<!-- new grid -->

<!-- ###################### -->

<!-- dialog employee -->
<div class="modal" id="modal_employee" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">

				<button type="button" class="btn btn-success pull-right" id="btn_chooseEmployee">
					<span class="glyphicon glyphicon-floppy-saved"></span> บันทึก</button>

				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close" id="btn_closeEmployee"><span class="glyphicon glyphicon-remove"></span> ปิด</button>

				<button type="button" class="btn btn-danger pull-right" id="btn_deleteEmployee">
					<span class="glyphicon glyphicon-trash"></span> ลบ</button>

				<h4 class="modal-title">รายชื่อพนักงาน</h4>
			</div>
			<div class="modal-body">
				<form id="form_employee">
					<div class="form-group">
						<input type="hidden" name="id_trans" id="id_trans">
						<input type="hidden" name="id_boiler" id="id_boiler">
						<input type="hidden" name="id_mold" id="id_mold">
						<input type="hidden" name="date_sch" id="date_sch">
						<input type="hidden" name="shift" id="shift">
						<input type="hidden" name="gridSchIdForEmployee" id="gridSchIdForEmployee">
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
						<input type="hidden" name="gridSchIdForRemark" id="gridSchIdForRemark">
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
										<span style="padding-left: 5px; font-size: 12px;"><b>(08:00-20:00)</b></span>
									</label>
									<span style="padding-left: 30px;"> </span>
									<label class="radio-inline">
										<input type="radio" name="gen_shift" id="gen_shift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
										<span style="padding-left: 5px; font-size: 12px;"><b>(20:00-08:00)</b></span>
									</label>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<hr>
							</td>
						</tr>
						<tr>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 200px;">
									<label class="checkbox-inline">
										<span style="padding-left: 10px;"><b>C</b></span>
										<input type="checkbox" name="chk_emp" id="chk_emp" style="width: 1.3em; height: 1.3em; margin-top: -1px;">
										<input type="hidden" name="check_emp" id="check_emp" value="0">
										<span style="padding-left: 10px;">รายชื่อพนักงาน</span>
									</label>
								</div>
							</td>
						</tr>

						<tr>
							<td style="padding: 5px;" align="center">
								<div id="gen_emp_date">
									<div class="input-group" style="width: 150px;">
										<input type="text" id="date_gen_emp" name="date_gen_emp" class="form-control" placeholder="เลือกวันที่..." autocomplete="off" />
										<span class="input-group-btn">
											<button class="btn btn-info" id="date_gen_emp_show" type="button">
												<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
											</button>
										</span>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td style="padding: 5px;" align="center">
								<div id="gen_emp_shift">
									<div>
										<label class="radio-inline">
											<input type="radio" name="shift_gen_emp" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
											<span style="padding-left: 5px; font-size: 12px;"><b>(08:00-20:00)</b></span>
										</label>
										<span style="padding-left: 30px;"> </span>
										<label class="radio-inline">
											<input type="radio" name="shift_gen_emp" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
											<span style="padding-left: 5px; font-size: 12px;"><b>(20:00-08:00)</b></span>
										</label>
									</div>
								</div>
							</td>
						</tr>

						<tr>
							<td>
								<hr>
							</td>
						</tr>

						<tr>
							<td style="padding: 5px;" align="center">
								<div id="gen_shift_for">
									ชุดกะ
									<div>
										<label class="radio-inline">
											<input type="radio" name="shift_for" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
											<span style="padding-left: 10px;"><b>C</b></span>
										</label>
										<span style="padding-left: 30px;"> </span>
										<label class="radio-inline">
											<input type="radio" name="shift_for" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;">
											<span style="padding-left: 10px;"><b>D</b></span>
										</label>
									</div>
								</div>
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

		var date_set = "<?php echo date('d-m-Y'); ?>";
		$("#date_sch").val(date_set);
		// $( "#date_gen").val(date_set);

		$('#date_sch').datepicker({
			format: 'dd-mm-yyyy',
			autoclose: true,
			todayHighlight: true,
		});

		$("#btn_reload").on("click", function() {
			$('#grid_sch').jqxGrid('updateBoundData', 'cells');
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

		var time_set = "<?php echo date('H:i'); ?>";

		if (time_set >= "08:01" && time_set <= "20:00") {
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
			$('#btn_generate_all').attr('disabled', false);
			$('#btn_generate_item').attr('disabled', false);
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

		$('#btn_chooseEmployee').on('click', function(event) {
			var rowdata = row_selected("#grid_employee");
			var rows_selected = [];
			var row_employee = '';
			if (typeof rowdata !== "undefined") {
				var rows = $('#grid_employee').jqxGrid('getselectedrowindexes');

				for (var i = 0; i < rows.length; i++) {
					row_employee = $('#grid_employee').jqxGrid('getrowdata', rows[i]);
					rows_selected.push(row_employee.Code);
				}
				// alert($('#id_trans').val());
				// console.log()

				gojax('post', '/production/sch/add/employee', {
					id_trans: $('#id_trans').val(),
					boiler: $('#id_boiler').val(),
					date_sch: $("#date_sch").val(),
					shift: $("input[name=shift]:checked").val(),
					mold: $("#id_mold").val(),
					code: rows_selected
				}).done(function(data) {
					if (data.result == 200) {
						$('#grid_employee').jqxGrid('clearselection');
						$('#modal_employee').modal('hide');
						// $('#grid_sch').jqxDataTable('updateBoundData', 'cells');
						// $("#grid_sch").jqxGrid('setcellvalue', $('#gridSchIdForEmployee').val(), "FullName", 'AAA');



						gojax('get', '/production/sch/load?date_sch=' + $("#date_sch").val() + '&shift=' + $("input[name=shift]:checked").val() + '&id=' + $('#id_trans').val()).done(function(res) {
							$("#grid_sch").jqxGrid('setcellvalue', $('#gridSchIdForEmployee').val(), "FullName", res[0].FullName);
							// $("#grid_sch").jqxGrid('setcellvalue', rowid, "Remark", res[0].Remark);
						});
					} else {
						// console.log(data);
					}
				});

			} else {
				// alert("กรุณาเลือกข้อมูล");
				// var id_trans = $('#id_trans').val();
				// alert(id_trans);
				gojax('post', '/production/sch/delete/employee', {
					id_trans: $('#id_trans').val()
				}).done(function(data) {
					if (data.result == 200) {
						$('#grid_employee').jqxGrid('clearselection');
						$('#modal_employee').modal('hide');
						$('#grid_sch').jqxDataTable('updateBoundData', 'cells');
					} else {
						// console.log(data);
					}
				});
			}
			return false;
		});

		$('#btn_deleteEmployee').on('click', function(event) {

			gojax('post', '/production/sch/delete/employee/id', {
				transid: $('#id_trans').val()
			}).done(function(data) {
				if (data.result == 200) {
					$('#modal_employee').modal('hide');
					$('#grid_sch').jqxGrid('updateBoundData', 'cells');
				} else {
					// console.log(data);
				}
			});

			return false;
		});

		$('#grid_item').on('rowdoubleclick', function(event) {
			var args = event.args;
			var boundIndex = args.rowindex;
			var datarowItem = $("#grid_item").jqxGrid('getrowdata', boundIndex);

			$('#itemid').val(datarowItem.ID);
			$('#ratecure').val(datarowItem.RateCure);
			$('#netweight').val(datarowItem.NetWeight);
			$('#id_trans').val();
			if (!!datarowItem.ID) {
				gojax('post', '/production/sch/add/item', {
					itemid: $('#itemid').val(),
					ratecure: $('#ratecure').val(),
					netweight: $('#netweight').val(),
					id: $('#id_trans').val()
				}).done(function(data) {
					if (data.result == 200) {
						$('#grid_item').jqxGrid('clearselection');
						$('#modal_item').modal('hide');
						$('#grid_sch').jqxGrid('updateBoundData', 'cells');
					} else {
						// console.log(data);
					}
				});
			}

		});

		$('#btn_chooseRemark').on('click', function(event) {

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
					transid: $('#id_trans').val(),
					boiler: $('#id_boiler').val(),
					date_sch: $("#date_sch").val(),
					shift: $("input[name=shift]:checked").val(),
					mold: $("#id_mold").val(),
					code: rows_selected
				}).done(function(data) {
					if (data.result == 200) {
						$('#modal_remark').modal('hide');
						// $('#grid_sch').jqxDataTable('updateBoundData', 'cells');
						// console.log(rows_selected);
						// $("#grid_sch").jqxGrid('setcellvalue', $('#gridSchIdForRemark').val(), "Remark", rows_selected[0]);

						gojax('get', '/production/sch/load?date_sch=' + $("#date_sch").val() + '&shift=' + $("input[name=shift]:checked").val() + '&id=' + $('#id_trans').val()).done(function(res) {
							// $("#grid_sch").jqxGrid('setcellvalue', rowid, "FullName", res[0].FullName);
							$("#grid_sch").jqxGrid('setcellvalue', $('#gridSchIdForRemark').val(), "Remark", res[0].Remark);
						});
					} else {
						// console.log(data);
					}
				});

			} else {
				alert("กรุณาเลือกข้อมูล");
			}
			return false;
		});

		$('#btn_deleteRemark').on('click', function(event) {

			gojax('post', '/production/sch/delete/remark/id', {
				transid: $('#id_trans').val()
			}).done(function(data) {
				if (data.result == 200) {
					$('#modal_remark').modal('hide');
					$('#grid_sch').jqxGrid('updateBoundData', 'cells');
				} else {
					// console.log(data);
				}
			});

			return false;
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

			if ($("input[name=shift_for]:checked").val() == 1) {
				var shift_for = 1;
			} else if ($("input[name=shift_for]:checked").val() == 2) {
				var shift_for = 2;
			}

			gen_sch($("#date_gen").val(), gen_shift, copy, $("#date_sch").val(), shift, gen_emp, date_emp, shift_emp, shift_for);
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

			if ($("input[name=shift_for]:checked").val() == 1) {
				var shift_for = 1;
			} else if ($("input[name=shift_for]:checked").val() == 2) {
				var shift_for = 2;
			}

			gen_sch($("#date_gen").val(), gen_shift, copy, $("#date_sch").val(), shift, gen_emp, date_emp, shift_emp, shift_for);
			return false;
		});

		// on grid_sch changed
		// $("#grid_sch").on('cellvaluechanged', function(event) {
		// 	// event arguments.
		// 	var args = event.args;
		// 	// column data field.
		// 	var datafield = event.args.datafield;
		// 	// row's bound index.
		// 	var rowBoundIndex = args.rowindex;
		// 	// new cell value.
		// 	var value = args.newvalue;
		// 	// old cell value.
		// 	var oldvalue = args.oldvalue;

		// 	console.log([value, oldvalue]);
		// }); 

		$('#btn_confirm').on('click', function(event) {
			$('#btn_confirm').attr({
				disabled: true
			});
			$('#btn_confirm').html('<span class="glyphicon glyphicon-lock"></span> Confirming...');
			gojax('post', '/production/sch/confirm', {
				date: $("#date_sch").val(),
				shift: shift,
				status: 1
			}).done(function(data) {
				if (data.result == true) {
					// $('#grid_sch').jqxGrid('updateBoundData');
					loadgridsch($("#date_sch").val(), shift);
				}
			});
		});

		$('#btn_confirmed').on('click', function(event) {
			$('#btn_confirmed').attr({
				disabled: true
			});
			$('#btn_confirmed').html('<span class="glyphicon glyphicon-lock"></span> Confirming...');
			gojax('post', '/production/sch/confirm', {
				date: $("#date_sch").val(),
				shift: shift,
				status: 2
			}).done(function(data) {
				if (data.result == true) {
					// $('#grid_sch').jqxGrid('updateBoundData');
					loadgridsch($("#date_sch").val(), shift);
				}
			});
		});

		$('#btn_unconfirm').on('click', function(event) {
			$('#btn_unconfirm').attr({
				disabled: true
			});
			$('#btn_unconfirm').html('<span class="glyphicon glyphicon-repeat"></span> UnLocking...');
			gojax('post', '/production/sch/confirm', {
				date: $("#date_sch").val(),
				shift: shift,
				status: 3
			}).done(function(data) {
				if (data.result == true) {
					// $('#grid_sch').jqxGrid('updateBoundData');
					loadgridsch($("#date_sch").val(), shift);
				}
			});
		});

		$("#grid_sch").on('bindingcomplete', function(event) {
			$('#message_statusload').html('<strong>Ready</strong>');
			$('#btn_confirm').attr({
				disabled: false
			});
			$('#btn_confirm').html('<span class="glyphicon glyphicon-lock"></span> Confirm');
			$('#btn_confirmed').attr({
				disabled: false
			});
			$('#btn_confirmed').html('<span class="glyphicon glyphicon-lock"></span> Confirmed');
			$('#btn_unconfirm').attr({
				disabled: false
			});
			$('#btn_unconfirm').html('<span class="glyphicon glyphicon-repeat"></span> UnLock');
		});

		$("#btn_buybill").on('click', function(event) {
			if ($("input[name=shift]:checked").val() == 1) {
				var gen_shift = 1;
			} else if ($("input[name=shift]:checked").val() == 2) {
				var gen_shift = 2;
			}
			var date_sch = $("input[name=date_sch]").val()

			//alert(gen_shift);
			window.open('/insertbuybill?date_sch=' + date_sch + '&shift=' + gen_shift);

		});

	});

	function checkgridsch(date_sch, shift) {
		gojax('get', '/production/sch/data/check', {
			date_sch: date_sch,
			shift: shift
		}).done(function(data) {
			if (data.result == true) {
				loadgridsch(date_sch, shift);
				$("#grid_sch").show();
				$('#message_checkdata').hide();
			} else {
				$('#btn_generate').attr({
					disabled: false
				});
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
			// console.log(data);
		});

	}

	// function checkconfirm(date_sch, shift) {
	// 	var confirm;
	// 	gojax('get', '/production/sch/confirm/check', {
	// 		date : date_sch,
	// 		shift : shift
	// 	}).done(function(data) {
	// 		confirm = data.result;
	// 	});
	// 	return confirm;
	// }

	function loadgridsch(date_sch, shift) {
		//	$('#message_statusload').html('<strong>Loading</strong>');

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: "json",
			updaterow: function(rowid, rowdata, commit) {
				gojax('post', '/production/sch/update/sch', {
					time: rowdata.Time,
					target: rowdata.Target,
					actual1: rowdata.Actual1,
					actual2: rowdata.Actual2,
					actual: rowdata.Actual,
					scrap: rowdata.Scrap,
					weight: rowdata.Weight,
					item: rowdata.ItemID,
					arms: rowdata.MoldID,
					id: rowdata.ID

				}).done(function(data) {
					if (data.result === 200) {
						// $('#grid_sch').jqxDataTable('updateBoundData', 'cells');
						commit(true);

						// gojax('get', '/production/sch/load?date_sch=' + date_sch + '&shift=' + shift + '&id=' + rowdata.ID).done(function(res) {
						// 	$("#grid_sch").jqxGrid('setcellvalue', rowid, "ItemName", res[0].ItemName);
						// 	// $("#grid_sch").jqxGrid('setcellvalue', rowid, "FullName", res[0].FullName);
						// 	$("#grid_sch").jqxGrid('setcellvalue', rowid, "Actual", res[0].Actual);
						// 	$("#grid_sch").jqxGrid('setcellvalue', rowid, "Weight", res[0].Weight);
						// 	// $("#grid_sch").jqxGrid('setcellvalue', rowid, "Remark", res[0].Remark);


						// });

					} else {
						// $('#grid_sch').jqxDataTable('updateBoundData', 'cells');
						commit(false);
					}
					// console.log(data);
				}).fail(function() {
					commit(false);
				});
			},
			// sortcolumn: 'BoilerName',
			// sortdirection: 'asc',
			datafields: [{
					name: "ID",
					type: "int"
				},
				{
					name: "Boiler",
					type: "string"
				},
				{
					name: "BoilerName",
					type: "string"
				},
				{
					name: "Employee",
					type: "int"
				},
				{
					name: "FullName",
					type: "string"
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
					name: "NameTH",
					type: "string"
				},
				{
					name: "Time",
					type: "int"
				},
				{
					name: "Target",
					type: "int"
				},
				{
					name: "Actual1",
					type: "int"
				},
				{
					name: "Actual2",
					type: "int"
				},
				{
					name: "Actual",
					type: "int"
				},
				{
					name: "Scrap",
					type: "int"
				},
				{
					name: "Weight",
					type: "number"
				},
				{
					name: "WeightDefault",
					type: "number"
				},
				{
					name: "Remark",
					type: "string"
				},
				{
					name: "MoldID",
					type: "int"
				},
				{
					name: "CurID",
					type: "int"
				},
				{
					name: "Status",
					type: "int"
				},
				{
					name: "SchDate",
					type: "date"
				},
				{
					name: "Shift",
					type: "int"
				}
			],
			// sortcolumn: 'CurID',
			// sortdirection: 'asc',
			url: '/production/sch/load?date_sch=' + date_sch + '&shift=' + shift,
			async: false
		});

		var setDelete = function(row, column, value) {
			if (value !== "") {
				return "<div style='padding:4px;'>" + value + "</div>";
			} else {
				return "<div style='font-size: 1em; padding:3px;'><button style='width:18px; height:18px; padding: 0.2px;' class='btn btn-danger' onclick='return setDelete(" + row + ")' style=' width:25px;'><b>-</b></button></div>";
			}

		}

		var setEmployee = function(row, column, value) {
			if (value !== "") {
				return "<div style='padding:4px;'>" + value + "</div>";
			} else {
				return "<div style='font-size: 1em; padding:3px;'><button style='width:50px; height:21px; padding: 0.2px;' class='btn btn-success' onclick='return setEmployeeModal(" + row + ")'>เพิ่มชื่อ</button></div>";
				// return "<div style='padding:4px;'>" + row + "</div>";
			}

		}

		var setItem = function(row, column, value) {
			if (value !== "") {
				return "<div style='padding:4px;'>" + value + "</div>";
			} else {
				return "<div style='font-size: 1em; padding:3px;'><button style='width:60px; height:21px; padding: 0.2px;' class='btn btn-success' onclick='return setItemModal(" + row + ")'>เพิ่มพิมพ์</button></div>";
			}

		}

		var setRemark = function(row, column, value) {
			if (value !== "") {
				return "<div style='padding:4px;'>" + value + "</div>";
			} else {
				return "<div style='font-size: 1em; padding:3px;'><button style='width:90px; height:21px; padding: 0.2px;' class='btn btn-success' onclick='return setRemarkModal(" + row + ")'>เพิ่มหมายเหตุ</button></div>";
			}

		}

		var clearItem = function(row, column, value) {
			if (value !== "") {
				return "<div style='padding:4px;'>" + value + "</div>";
			} else {
				return "<div style='font-size: 1em; padding:3px;'><button style='width:20px; height:21px; padding: 0.2px;' class='btn btn-danger' onclick='return clearItemFunc(" + row + ")'>X</button></div>";
			}

		}

		var setBoilerName = function(row, column, value) {
			var boiler = value.substring(0, value.indexOf('/'));
			boiler = boiler.split('_').pop();
			var boiler_ = value.split('_').pop();
			return '<div style="padding:4px;"><b>' + boiler_ + '</b></div>';
			// return "<div style='padding:4px;'>" + value + "</div>";

		}

		gojax('get', '/production/sch/confirm/check', {
			date: date_sch,
			shift: shift
		}).done(function(data) {
			confirm = data.result;
			// $('#confirm_x').val(confirm);
			// console.log(confirm);
			if (confirm == 1) {
				$('#btn_generate').attr({
					disabled: true
				});
				return $("#grid_sch").jqxGrid({
					width: '100%',
					source: dataAdapter,
					pageable: true,
					altRows: true,
					columnsResize: true,
					filterable: true,
					editable: true,
					selectionmode: 'singlecell',
					editmode: 'click',
					autoheight: true,
					pageSize: 12,
					rowsheight: 32,
					pagesizeoptions: [12, 24],
					sortable: true,
					columns: [{
							text: "เตา",
							datafield: "BoilerName",
							align: 'center',
							width: '5%',
							hidden: false,
							editable: false
						},
						{
							text: "ชื่อพนักงาน",
							datafield: "FullName",
							align: 'center',
							width: '8%',
							editable: false
						},
						{
							text: "พิมพ์",
							datafield: "MoldID",
							align: 'center',
							width: '2%',
							editable: false
						},
						// {
						// 	text: "",
						// 	cellsrenderer: setEmployee,
						// 	width: '4%',
						// 	editable: false
						// },
						{
							text: "ItemID",
							datafield: "ItemID",
							align: 'center',
							width: '5%',
							editable: false
						},
						{
							text: "ขนาดพิมพ์",
							datafield: "ItemName",
							align: 'center',
							editable: false
						},
						// {
						// 	text: "",
						// 	cellsrenderer: setItem,
						// 	width: '5%',
						// 	editable: false
						// },
						{
							text: "เวลาอบ(นาที)",
							datafield: "Time",
							align: 'center',
							width: '5%',
							editable: false
						},
						{
							text: "เป้า(เส้น)",
							datafield: "Target",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%',
							editable: false
						},
						{
							text: "รอบ1",
							datafield: "Actual1",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%',
							editable: false
						},
						{
							text: "รอบ2",
							datafield: "Actual2",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%'
						},
						{
							text: "อบได้(เส้น)",
							datafield: "Actual",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%'
						},
						{
							text: "สูญเสีย(เส้น)",
							datafield: "Scrap",
							align: 'center',
							width: '5%'
						},
						{
							text: "น้ำหนัก",
							datafield: "Weight",
							align: 'center',
							width: '4%',
							editable: false,
							cellsformat: 'F3'
						},
						{
							text: "หมายเหตุ",
							datafield: "Remark",
							width: '8%',
							align: 'center',
							editable: false
						},
						{
							text: "",
							cellsrenderer: setRemark,
							width: '5%',
							editable: false
						},
						{
							text: "",
							cellsrenderer: clearItem,
							width: '2.5%',
							editable: false
						},

					],
					columnGroups: [{
						text: 'จำนวนการอบยาง',
						align: 'center',
						name: 'ProductDetails'
					}]
				});

			} else if (confirm == 2) {
				$('#btn_generate').attr({
					disabled: true
				});
				return $("#grid_sch").jqxGrid({
					width: '100%',
					source: dataAdapter,
					pageable: true,
					altRows: true,
					columnsResize: true,
					filterable: true,
					editable: true,
					selectionmode: 'singlecell',
					editmode: 'click',
					autoheight: true,
					pageSize: 12,
					rowsheight: 32,
					pagesizeoptions: [12, 24],
					sortable: true,
					columns: [{
							text: "เตา",
							datafield: "BoilerName",
							align: 'center',
							width: '5%',
							hidden: false,
							editable: false,
							// cellsrenderer: setBoilerName,
						},
						{
							text: "ชื่อพนักงาน",
							datafield: "FullName",
							align: 'center',
							width: '8%',
							editable: false
						},
						{
							text: "พิมพ์",
							datafield: "MoldID",
							align: 'center',
							width: '2%',
							editable: false
						},
						// {
						// 	text: "",
						// 	cellsrenderer: setEmployee,
						// 	width: '4%',
						// 	editable: false
						// },
						{
							text: "ItemID",
							datafield: "ItemID",
							align: 'center',
							width: '5%',
							editable: false
						},
						{
							text: "ขนาดพิมพ์",
							datafield: "ItemName",
							align: 'center',
							editable: false
						},
						// {
						// 	text: "",
						// 	cellsrenderer: setItem,
						// 	width: '5%',
						// 	editable: false
						// },
						{
							text: "เวลาอบ(นาที)",
							datafield: "Time",
							align: 'center',
							width: '5%',
							editable: false
						},
						{
							text: "เป้า(เส้น)",
							datafield: "Target",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%',
							editable: false
						},
						{
							text: "รอบ1",
							datafield: "Actual1",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%',
							editable: false
						},
						{
							text: "รอบ2",
							datafield: "Actual2",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%',
							editable: false
						},
						{
							text: "อบได้(เส้น)",
							datafield: "Actual",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%',
							editable: false
						},
						{
							text: "สูญเสีย(เส้น)",
							datafield: "Scrap",
							align: 'center',
							width: '5%',
							editable: false
						},
						{
							text: "น้ำหนัก",
							datafield: "Weight",
							align: 'center',
							width: '4%',
							editable: false,
							cellsformat: 'F3'
						},
						{
							text: "หมายเหตุ",
							datafield: "Remark",
							width: '8%',
							align: 'center',
							editable: false
						},
						{
							text: "",
							cellsrenderer: setRemark,
							width: '5%',
							editable: false
						},
						{
							text: "",
							cellsrenderer: clearItem,
							width: '2.5%',
							editable: false
						},

					],
					columnGroups: [{
						text: 'จำนวนการอบยาง',
						align: 'center',
						name: 'ProductDetails'
					}]
				});

			} else {
				$('#btn_generate').attr({
					disabled: false
				});
				return $("#grid_sch").jqxGrid({
					width: '100%',
					source: dataAdapter,
					pageable: true,
					altRows: true,
					columnsResize: true,
					filterable: true,
					editable: true,
					selectionmode: 'singlecell',
					editmode: 'click',
					autoheight: true,
					pageSize: 12,
					rowsheight: 32,
					pagesizeoptions: [12, 24],
					sortable: true,
					columns: [{
							text: "เตา",
							datafield: "BoilerName",
							align: 'center',
							width: '5%',
							hidden: false,
							editable: false,
							cellsrenderer: setBoilerName,
						},
						{
							text: "ชื่อพนักงาน",
							datafield: "FullName",
							align: 'center',
							width: '8%',
							editable: false
						},
						{
							text: "พิมพ์",
							datafield: "MoldID",
							align: 'center',
							width: '2%'
						},
						{
							text: "",
							cellsrenderer: setEmployee,
							width: '4%',
							editable: false
						},
						{
							text: "ItemID",
							datafield: "ItemID",
							align: 'center',
							width: '5%'
						},
						{
							text: "ขนาดพิมพ์",
							datafield: "ItemName",
							align: 'center',
							// width: '15%',
							editable: false
						},
						{
							text: "",
							cellsrenderer: setItem,
							width: '5%',
							editable: false
						},
						{
							text: "เวลาอบ(นาที)",
							datafield: "Time",
							align: 'center',
							width: '5%',
							editable: false
						},
						{
							text: "เป้า(เส้น)",
							datafield: "Target",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%'
						},
						{
							text: "รอบ1",
							datafield: "Actual1",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%'
						},
						{
							text: "รอบ2",
							datafield: "Actual2",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%'
						},
						{
							text: "อบได้(เส้น)",
							datafield: "Actual",
							align: 'center',
							columngroup: 'ProductDetails',
							width: '4%',
							editable: false
						},
						{
							text: "สูญเสีย(เส้น)",
							datafield: "Scrap",
							align: 'center',
							width: '5%'
						},
						{
							text: "น้ำหนัก",
							datafield: "Weight",
							align: 'center',
							width: '4%',
							editable: false,
							cellsformat: 'F3'
						},
						{
							text: "หมายเหตุ",
							datafield: "Remark",
							width: '8%',
							align: 'center',
							editable: false
						},
						{
							text: "",
							cellsrenderer: setRemark,
							width: '5%',
							editable: false
						},
						{
							text: "",
							cellsrenderer: clearItem,
							width: '2.5%',
							editable: false
						},

					],
					columnGroups: [{
						text: 'จำนวนการอบยาง',
						align: 'center',
						name: 'ProductDetails'
					}]
				});

			}
		});
		// console.log($('#confirm_x').val());

	}

	function setRemarkModal(row) {

		var rowdata = $("#grid_sch").jqxGrid('getrowdata', row);

		var transid = rowdata.ID; //$("#grid_sch").jqxDataTable('getCellValue', row, 'ID');
		var boilerid = rowdata.Boiler; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Boiler');
		var moldid = rowdata.MoldID; //$("#grid_sch").jqxDataTable('getCellValue', row, 'MoldID');
		var statusid = rowdata.Status; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
		var schdate = rowdata.SchDate; //$("#grid_sch").jqxDataTable('getCellValue', row, 'SchDate');
		var shift = rowdata.Shift; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Shift');

		$('#gridSchIdForRemark').val(row);
		$('#id_trans').val(transid);
		$('#id_boiler').val(boilerid);
		$('#id_mold').val(moldid);
		$('#date_sch').val(formatDate(schdate));
		$('#shift').val(shift);

		if (statusid == 1) {
			$('#grid_remark').jqxGrid('clearselection');

			$('#modal_remark').modal({
				backdrop: 'static'
			});

			var dataAdapter = new $.jqx.dataAdapter({
				datatype: "json",
				datafields: [{
						name: "RowID",
						type: "int"
					},
					{
						name: "ProblemID",
						type: "string"
					},
					{
						name: "Description",
						type: "string"
					}
				],
				url: '/production/sch/load/remark'
			});

			return $("#grid_remark").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				columnsresize: true,
				pageable: true,
				filterable: true,
				showfilterrow: true,
				pagesize: 20,
				selectionmode: 'checkbox',
				rendered: function() {
					gojax('get', '/production/sch/get/remark', {
						transid: transid
					}).done(function(data) {
						for (var key in data) {
							var ProblemID = data[key].ProblemID;
							var rowidProblem = ProblemID.substring(3, 7);
							$("#grid_remark").jqxGrid('selectrow', (rowidProblem - 1));
							$('#grid_remark').jqxGrid('focus');
						}
					});
				},
				columns: [{
						text: "ProblemID",
						datafield: "ProblemID",
						align: 'center'
					},
					{
						text: "Description",
						datafield: "Description",
						align: 'center'
					}
				]
			});

		}
	}

	function setEmployeeModal(row) {
		var rowdata = $("#grid_sch").jqxGrid('getrowdata', row);

		var transid = rowdata.ID; //$("#grid_sch").jqxDataTable('getCellValue', row, 'ID');
		var boilerid = rowdata.Boiler; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Boiler');
		var moldid = rowdata.MoldID; //$("#grid_sch").jqxDataTable('getCellValue', row, 'MoldID');
		var statusid = rowdata.Status; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
		var schdate = rowdata.SchDate; //$("#grid_sch").jqxDataTable('getCellValue', row, 'SchDate');
		var shift = rowdata.Shift; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Shift');

		$('#gridSchIdForEmployee').val(row);
		$('#id_trans').val(transid);
		$('#id_boiler').val(boilerid);
		$('#id_mold').val(moldid);
		$('#date_sch').val(formatDate(schdate));
		$('#shift').val(shift);

		if (statusid == 1) {
			$('#grid_employee').jqxGrid('clearselection');

			$('#modal_employee').modal({
				backdrop: 'static'
			});

			var dataAdapter = new $.jqx.dataAdapter({
				datatype: "json",
				datafields: [{
						name: "ParentID",
						type: "int"
					},
					{
						name: "Code",
						type: "string"
					},
					{
						name: "FirstName",
						type: "string"
					},
					{
						name: "LastName",
						type: "string"
					},
					{
						name: "DepartmentName",
						type: "string"
					}
				],
				url: '/production/sch/load/employee'
			});

			return $("#grid_employee").jqxGrid({
				width: '100%',
				source: dataAdapter,
				autoheight: true,
				columnsresize: true,
				pageable: true,
				filterable: true,
				showfilterrow: true,
				pagesize: 20,
				selectionmode: 'checkbox',
				rendered: function() {
					gojax('get', '/production/sch/get/employee', {
						transid: transid
					}).done(function(data) {
						for (var key in data) {
							var rowID = (data[key].ParentID - 1);
							$("#grid_employee").jqxGrid('selectrow', rowID);
							$('#grid_employee').jqxGrid('focus');
							// console.log(data[key].ParentID+'/'+transid);
						}
					});
				},
				columns: [{
						text: "Code",
						datafield: "Code",
						align: 'center'
					},
					{
						text: "ชื่อ",
						datafield: "FirstName",
						align: 'center'
					},
					{
						text: "นามสกุล",
						datafield: "LastName",
						align: 'center'
					},
					{
						text: "แผนก",
						datafield: "DepartmentName",
						align: 'center'
					}
				]
			});

		}
	}

	function setItemModal(row) {
		var rowdata = $("#grid_sch").jqxGrid('getrowdata', row);
		var transid = rowdata.ID; //$("#grid_sch").jqxDataTable('getCellValue', row, 'ID');
		var boiler = rowdata.Boiler; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Boiler');
		var statusid = rowdata.Status; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
		$('#id_trans').val(transid);
		if (statusid == 1) {
			// loadgriditem(boiler);
			$('#grid_item').jqxGrid('clearselection');

			$('#modal_item').modal({
				backdrop: 'static'
			});

			var dataAdapter = new $.jqx.dataAdapter({
				datatype: "json",
				datafields: [{
						name: "ID",
						type: "string"
					},
					{
						name: "ItemName",
						type: "string"
					},
					{
						name: "Pattern",
						type: "string"
					},
					{
						name: "Brand",
						type: "string"
					},
					{
						name: "RateCure",
						type: "string"
					},
					{
						name: "ItemNameThai",
						type: "string"
					},
					{
						name: "NetWeight",
						type: "int"
					}
				],
				url: '/production/sch/load/item?boiler=' + boiler
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
						datafield: "ID",
						align: 'center',
						width: '15%'
					},
					{
						text: "Name",
						datafield: "ItemName",
						align: 'center'
					},
					// { text:"Name TH", datafield: "ItemNameThai", align: 'center'},
					{
						text: "Pattern",
						datafield: "Pattern",
						align: 'center',
						width: '10%'
					},
					{
						text: "Brand",
						datafield: "Brand",
						align: 'center',
						width: '10%'
					},
					{
						text: "RateCure",
						datafield: "RateCure",
						align: 'center',
						width: '10%'
					},
					{
						text: "NetWeight",
						datafield: "NetWeight",
						align: 'center',
						width: '10%'
					}
				]
			});

		}
	}

	function setDelete(row) {
		var rowdata = $("#grid_sch").jqxGrid('getrowdata', row);

		var transid = rowdata.ID; //$("#grid_sch").jqxDataTable('getCellValue', row, 'ID');
		var statusid = rowdata.Status; //$("#grid_sch").jqxDataTable('getCellValue', row, 'Status');
		if (statusid == 1) {
			gojax('post', '/production/sch/delete/sch', {
				id: transid
			}).done(function(data) {
				if (data.result == 200) {
					$('#grid_sch').jqxDataTable('updateBoundData', 'cells');
					// console.log(data);
				} else {
					// console.log(data);
				}
			});
		}
	}

	function setBoilerAdd(boiler) {
		var user = 'user';
		gojax('post', '/production/sch/add/sch', {
			boiler: boiler,
			date_sch: $("#date_sch").val(),
			shift: $("input[name=shift]:checked").val(),
			type: user
		}).done(function(data) {
			if (data.result == 200) {
				$('#grid_sch').jqxDataTable('updateBoundData', 'cells');
				// console.log(data);
			} else {
				// console.log(data);
			}
		});
	}

	function gen_sch(date_gen, gen_shift, copy, date_sch, shift, gen_emp, date_emp, shift_emp, shift_for) {
		gojax('post', '/production/sch/gen/sch', {
			date_gen: date_gen,
			gen_shift: gen_shift,
			date_sch: date_sch,
			shift: shift,
			copy: copy,
			gen_emp: gen_emp,
			date_emp: date_emp,
			shift_emp: shift_emp,
			shift_for: shift_for
		}).done(function(data) {
			// console.log(data);
			if (data.result == 200) {
				loadgridsch(date_sch, shift);
				$('#message_checkdata').hide();
				$('#modal_generate').modal('hide');
				$('#btn_generate_all').attr('disabled', false);
				$('#btn_generate_item').attr('disabled', false);
				$("#grid_sch").show();
				$('#grid_sch').jqxGrid('updatebounddata');
			} else {
				alert(data.message);
				$('#modal_generate').modal('hide');
				$('#btn_generate_all').attr('disabled', false);
				$('#btn_generate_item').attr('disabled', false);
				$('#grid_sch').jqxGrid('updatebounddata');

			}

		});
	}

	function clearItemFunc(row) {
		var transid = $("#grid_sch").jqxGrid('getCellValue', row, 'ID');
		// console.log(transid);
		gojax('post', '/production/sch/update/clear', {
			id: transid
		}).done(function(data) {
			if (data.result == 200) {
				$('#grid_sch').jqxGrid('updateBoundData', 'cells');
				// console.log(data);
			} else {
				// console.log(data);
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

	// function row_selected(grid_name) {
	// 	var selectedrowindex = $(grid_name).jqxGrid('getselectedrowindex');
	// 	var datarow = $(grid_name).jqxGrid('getrowdata', selectedrowindex);
	// 	return datarow;
	// }
</script>