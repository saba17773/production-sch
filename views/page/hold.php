<?php $this->layout("layouts/base", ['title' => 'Hold']); ?>

<h1 class="head-text">Hold</h1>

<div class="panel panel-default form-center">
  <div class="panel-body">
  	<form id="holdForm" onsubmit="return hold()">
  		<div class="form-group">
				<label for="defectCode">Defect Code</label>
				<select name="defectCode" id="defectCode" class="form-control input-lg inputs" required></select>
			</div>
			<div class="form-group">
				<label for="holdInput">Barcode</label>
				<input type="text" class="form-control input-lg" name="holdInput" id="holdInput" autocomplete="off">
			</div>
		</form>
  </div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {


		$('#holdInput').focus();
		
		$('#app_alert').on('click', function() {
			$('#holdInput').val('').focus();
		});

		$('#modal_alert').on('hidden.bs.modal', function() {
			$('#holdInput').val('').focus();
		});

		$('select[name=defectCode]').on('change', function() {
			$('#holdInput').val('').focus();
		});

		gojax('get', base_url + '/api/defect/all').done(function(data) {
			$('select[name=defectCode]').html("<option value=''>= กรุณาเลือกข้อมูล =</option>");
			$.each(data, function(index, val) {
				$('select[name=defectCode]').append('<option value="'+val.ID+'">'+val.ID+' - '+val.Description+'</option>');
			});
		});

	});

	function hold() {

		var holdInput = $('#holdInput');
		
		if (!!holdInput.val()) {
			var barcode_hold = holdInput.val();
			gojax('post', base_url+'/api/hold', {
				barcode : holdInput.val(),
				defect: $('#defectCode').val()
			})
			.done(function(data) {
				if (data.status == 200) {
					// window.location = '?mode=success&error=0&barcode='+ barcode_hold;
					$('#top_alert').show();
					$('#top_alert_message').text('Barcode ล่าสุด ' + barcode_hold);
					$('#modal_alert').modal('hide');
				} else {
					// window.location = '?mode=danger&error='+data.message+'&barcode='+barcode_hold;
					$('#top_alert').hide();
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
					$('#holdInput').val(barcode_hold);
				}

				$('#holdForm').trigger('reset');
				$('#holdInput').val('').focus();
				
			});
		} else {
			// window.location = '?mode=danger&error=กรุณากรอกข้อมูล';
			$('#modal_alert').modal({backdrop: 'static'});
			$('#modal_alert_message').text('กรุณากรอกข้อมูล');
			$('#top_alert').hide();
		}

		holdInput.val('').focus();

		return false;
	}
</script>