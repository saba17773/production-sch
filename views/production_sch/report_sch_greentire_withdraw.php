<?php 
$this->layout("layouts/base", ['title' => 'Report-Production-Scheduler']);
$PermissionService = new App\Services\PermissionService;
?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Report Production-Scheduler Greentire</div>
	<div class="panel-body">
		<form action="<?php echo root; ?>/production/sch/pdf/report/greentire/withdraw" method="post" target="_blank">
			<div class="row">
				<div class="form-group col-md-6">
					<label for="date_sch">วันที่เบิกให้</label>
					<input type="text" id="date_sch" name="date_sch" class=form-control required  placeholder="เลือกวันที่..." / autocomplete="off">
					<label for="shift">Shift</label>
					<select name="shift" id="shift" class="form-control" required>
					  <option value="1">(08:00-20:00)</option>
					  <option value="2">(20:00-08:00)</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label for="date_sch_pay">วันที่เบิกจ่าย</label>
					<input type="text" id="date_sch_pay" name="date_sch_pay" class=form-control required  placeholder="เลือกวันที่..." / autocomplete="off">
					<label for="shift_pay">Shift</label>
					<select name="shift_pay" id="shift_pay" class="form-control" required>
					  <option value="1">(08:00-20:00)</option>
					  <option value="2">(20:00-08:00)</option>
					</select>
				</div>
			</div>

			<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-print"></span> Print</button>
		</form>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$( "#date_sch" ).datepicker({
			format: 'dd-mm-yyyy',
			autoclose: true,
			todayHighlight: true
		});
		$( "#date_sch_pay" ).datepicker({
			format: 'dd-mm-yyyy',
			autoclose: true,
			todayHighlight: true
		});
	});
</script>