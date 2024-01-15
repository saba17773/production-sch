<?php $this->layout("layouts/base", ['title' => 'Warehouse Incoming']); ?>

<style>
	#show_remain {
		position: absolute;
		top: 120px;
		right: 100px;
		font-size: 20em;
		font-weight: bold;
	}
</style>

<div class="head-space"></div>

<div class="panel panel-default form-center">
	<div class="panel-heading">Warehouse Incoming</div>
	<div class="panel-body">
		<form id="form_wh_incoming" class="form-center">
			<div class="form-group">
				<label for="barcode">Barcode</label>
				<input type="text" name="barcode" id="barcode" class="form-control input-lg" autocomplete="off" required />
			</div>
		</form>
	</div>
</div>

<div id="show_remain"></div>

<div class="alert alert-success hide" role="alert" id="showItem" style="margin-top: 20px;">
	<h1 class="text-center" id="txtItemId" style="font-size: 5em; font-weight: bold;">-- Not found. --</h1>
	<h1 class="text-center" id="txtItemName" style="font-size: 5em; font-weight: bold;">-- Not found. --</h1>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#barcode').focus();

		setInterval(function() {
			gojax('get', base_url+'/api/invent/warehouse/total_receive')
			.done(function(data){
				$('#show_remain').text(data.count);
			})
			.fail(function() {
				$('#show_remain').text();
			});
		}, 3000);
		

		$('#modal_alert').on('hidden.bs.modal', function() {
			$('#barcode').val('').focus();
			/* Act on the event */
		});

		$('form#form_wh_incoming').on('submit', function(event) {
			event.preventDefault();
			var temp_barcode = $('#barcode').val();
			/* Act on the event */

			$('#barcode').prop('readonly', true);

			gojax('post', base_url+'/api/warehouse/incoming', {
				barcode: $('#barcode').val()
			})
			.done(function(data) {
				// alert(data.message);
				if (data.status == 200) {
					// window.location = '?success='+data.message;
					$('#top_alert').show();
					$('#top_alert_message').text('Barcode ล่าสุด '+temp_barcode);
					$('#modal_alert').modal('hide');

					gojax('get', base_url+'/api/barcode/'+temp_barcode)
					.done(function(data) {
						$.each(data, function(index, val) {
							$('#showItem').removeClass('hide');
							$('#txtItemId').html(val.ItemID);
							$('#txtItemName').html(val.NameTH);
						});
					});

				} else {
					// window.location = '?error='+data.message;
					$('#modal_alert').modal({backdrop: 'static'});
					$('#modal_alert_message').text(data.message);
					$('#top_alert').hide();
				}

				$('#barcode').prop('readonly', false);
				$('#barcode').val('').focus();
			});
		});
	});
</script>