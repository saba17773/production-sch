<?php $this->layout('layouts/base', ['title' => 'Greentire Adjust']); ?>

<style>
	div.ui-datepicker{
		font-size: 16px;
	}
</style>

<div class="head-space"></div>

<div class="panel panel-default form-center" id="panel-barcode">
	<div class="panel-heading">Adjust</div>
	<div class="panel-body">
		<label>Code</label> <br>
		<input type="text" name="greentire_code" id="greentire_code" class="form-control input-lg inputs" required autocomplete="off"><br>
		<label>Date</label> <br>
		<input type="text" name="date" id="date" class="form-control input-lg inputs" required>
		<span id="show_batch" style="padding-left: 10px; font-size: 1.2em; color: green; font-weight: bold;"></span>
		<br>
		<label>Barcode</label> <br>
		<input type="text" name="barcode" id="barcode" class="form-control input-lg inputs" required autocomplete="off">
	</div>
</div>

<script>
	jQuery(document).ready(function($) {

		$('#date').datepicker({
			dateFormat: 'yy-mm-dd', 
			maxDate: '0',
			showOn: "button",
      buttonText: "เลือกวันที่",
			onSelect: function(date) {
				gojax('post', '/get_week', {
					datetime: $('input[name=date]').val()
				}).done(function(data) {
					$('#show_batch').text(data.week);
					// $('input[name=date]').val(data.week);
					$('#barcode').val('').focus();
				});

				
      }
    });

    $('.ui-datepicker-trigger').css({
    	'margin': '10px 0px',
    	'font-size': '1.2em'
    });

		$('#greentire_code').val('').focus();

		$('#modal_alert').on('hidden.bs.modal', function() {
			$('#barcode').val('');
			$('#date').val('');
			$('#greentire_code').val('').focus();
		});

		$('#barcode').keydown(function(event) {
			/* Act on the event */
			if (event.which === 13) {
				if ($('#date').val() === "" || $('#date').val().indexOf('-') === -1) {
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text("รูปแบบของวันที่ไม่ถูกต้อง");
				} else {
					$('#barcode').prop('readonly', true);
					gojax('post', base_url+'/api/v1/adjust', {
						greentire_code: $('#greentire_code').val(),
						date: $('#date').val(),
						barcode: $('#barcode').val()
					}).done(function(data) {
						if (data.status !== 200) {
							$('#modal_alert').modal({backdrop: 'static'});
							$('#modal_alert_message').text(data.message);
						} else {
							$('#top_alert').show();
							$('#top_alert_message').text('Barcode ล่าสุด ' + $('#barcode').val());
							$('#barcode').val('');
							$('#date').val('');
							$('#greentire_code').val('').focus();
						}		
						$('#barcode').prop('readonly', false);
					});
				} // date val()
			} // on enter
		});
	});

	function goToBarcode() {
		$('#barcode').val('').focus();
	}
</script>