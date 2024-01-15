<?php $this->layout('layouts/base', ['title' => 'Change Code']) ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Change Code</div>
	<div class="panel-body">
		<form id="form_change_code">
			<div class="form-group">
				<label for="copy_barcode">Copy barcode</label>
				<input type="text" class="form-control inputs" name="copy_barcode" id="copy_barcode" autofocus autocomplete="off" required> 
			</div>

			<div class="form-group">
				<label for="barcode">Barcode</label>
				<input type="text" class="form-control inputs" name="barcode" id="barcode" autocomplete="off" required> 
			</div>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$("#barcode").keydown(function(event) {
			if (event.which === 13) {
				gojax('post', '/api/v1/building/change_code', {
					copy_barcode: $.trim($('#copy_barcode').val()),
					barcode: $.trim($('#barcode').val())
				}).done(function(data) {
					alert(data.message);
					$('#form_change_code').trigger('reset');
					$('#copy_barcode').focus();
				});
			}
		});
	});
</script>