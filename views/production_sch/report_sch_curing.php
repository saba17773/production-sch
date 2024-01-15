<?php 
$this->layout("layouts/base", ['title' => 'Report-Production-Scheduler']);
$PermissionService = new App\Services\PermissionService;
?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Report Production-Scheduler Curing</div>
	<div class="panel-body">
		<form id="sch_curing">
			<div class="form-group">
				<label for="date">Year-Month</label>
				<input class="form-control" type="text" id="date_sch" name="date_sch" autocomplete="off">
				<input type="text" id="check_type" name="check_type" hidden/>
			</div>
			<div class="form-group">
				<label for="sch_shift">Shift</label>
				<select name="sch_shift" id="sch_shift" class="form-control" required>
					<option value="1">(08:00-20:00)</option>
					<option value="2">(20:00-08:00)</option>
				</select>
			</div>
			<div class="btn-group btn-group-justified" role="group">
				<div class="btn-group">
					<button type="button" class="btn btn-primary btn-lg" id="to_pdf"><span class="glyphicon glyphicon-print"></span> Print to PDF</button>
				</div>
			  	<div class="btn-group">
			  		<button type="button" class="btn btn-success btn-lg" id="to_excel"><span class="glyphicon glyphicon-file"></span> Export to Excel</button>
			  	</div>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#date_sch").datepicker( {
		    minViewMode: 1,
	        format: 'yyyy-mm',
	        autoclose: true,
	        todayHighlight: true
		});

		$('#to_pdf').on('click', function(event) {
	        event.preventDefault();
	        $('#to_excel').attr("disabled", true);
	        setTimeout(function () {
	        $('#to_excel').attr("disabled", false);
	        }, 10000);
	        $('input[name=check_type]').val(1);
	        $('#sch_curing').submit();

        });

        $('#to_excel').on('click', function(event) {
	        event.preventDefault();
	        $('#to_pdf').attr("disabled", true);
	        setTimeout(function () {
	        $('#to_pdf').attr("disabled", false);
	        }, 10000);
	        $('input[name=check_type]').val(2);
	        $('#sch_curing').submit();

        });

        $('#sch_curing').submit(function(e) {
			e.preventDefault();
			
			var param_date = $('input[name=date_sch]').val();
			var param_shift = $('select[name=sch_shift]').val();
			var param_checktype = $('input[name=check_type]').val();

			if (param_date === '' ) {
				alert('please select date !');
				$('#date_sch').focus();
				$('#to_pdf').attr("disabled", false);
				$('#to_excel').attr("disabled", false);
			} else {
				window.open('/production/sch/pdf/report/curing/' + param_date + '/' + param_shift + '/' + param_checktype + '/view', '_blank');
			}

		});		

	});
</script>