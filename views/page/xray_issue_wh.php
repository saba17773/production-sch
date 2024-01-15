<?php $this->layout("layouts/base", ['title' => 'Xray issue into Warehouse']); ?>

<div class="head-space"></div>

<div class="panel panel-default form-center">
	<div class="panel-heading">X-ray issue into warehouse</div>
	<div class="panel-body">
		<form id="form_issue_wh">
			<div class="form-group">
				<label for="barcode">Barcode</label>
				<input type="text" name="barcode" 
				id="barcode" class="form-control input-lg" autocomplete="off" required />
			</div>
		</form>
	</div>
</div>



<script>
	jQuery(document).ready(function($) {

		$('#barcode').focus();

		$('#modal_alert').on('hidden.bs.modal', function() {
			$('#barcode').val('').focus();
			/* Act on the event */
		});

		$('form#form_issue_wh').on('submit', function(event) {
			event.preventDefault();
			/* Act on the event */
			var barcode = $('#barcode').val();
			if(!!$.trim(barcode)) {

				$('#barcode').prop('readonly', true);

				gojax('post', base_url+'/api/xray/issue/wh', {
					barcode: barcode
				})
				.done(function(data) {
					if (data.status == 200) {
						// window.location = '?success='+data.message;
						$('#top_alert').show();
						$('#top_alert_message').text('Barcode ล่าสุด '+ $('#barcode').val());
						$('#modal_alert').modal('hide');
					} else {
						// window.location = '?error='+data.message+'&barcode='+$('#barcode').val();
						$('#top_alert').hide();
						$('#modal_alert').modal({backdrop: 'static'});
						$('#modal_alert_message').text(data.message);
						$('#barcode').val($('#barcode').val());
					}
					// alert(data.message);
					// console.log(data.message);
					$('#barcode').prop('readonly', false);
					$('#barcode').val('').focus();
					// console.log(data);
				});
			} else {
				// alert("Barcode ไม่ถูกต้อง");
				// window.location = '?error=Barcode ไม่ถูกต้อง';
				$('#modal_alert').modal({backdrop: 'static'});
				$('#modal_alert_message').text('Barcode ไม่ถูกต้อง');
				$('#barcode').prop('readonly', false);
			}
		});
	});
</script>
