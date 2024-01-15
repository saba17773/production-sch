<?php $this->layout("layouts/base", ["title" => "Change Barcode"]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="margin: auto; max-width: 500px;">
    <div class="panel-heading">Change Barcode</div>
    <div class="panel-body">
        <form id="form_change_barcode">
            <div class="form-group">
                <label>Old Barcode</label>
                <input type="text" class="form-control inputs" name="old_barcode" id="old_barcode" autofocus autocomplete="off">  
            </div>
            <div class="form-group">
                <label>New Barcode</label>
                <input type="text" class="form-control inputs" name="new_barcode" id="new_barcode" autocomplete="off">
            </div>
        </form>
    </div>
</div>

<script>
	jQuery(document).ready(function($) {
		$('#modal_alert').on('hidden.bs.modal', function() {
      $(onFocus).focus();
  	});

		$('#new_barcode').keydown(function(event) {
			if (event.which === 13) {
				gojax('post', '/change_barcode/save', {
					old_barcode: $.trim($('#old_barcode').val()),
					new_barcode: $.trim($('#new_barcode').val())
				}).done(function(data) {
					if (data.result !== true ) {
						$('#top_alert').hide();
            $('#modal_alert').modal({backdrop: 'static'});
            $('#modal_alert_message').text(data.message);
					} else {
						$('#top_alert').show();
            $('#modal_alert').modal('hide');
            $('#top_alert_message').text('Barcode ใหม่ : ' + $('#new_barcode').val());
            setTimeout(function(){
            	$('#top_alert').hide();
            }, 2000);
					}
					$('#form_change_barcode').trigger('reset');
					// $('#old_barcode').focus();
					onFocus = '#old_barcode';
				});
			}
		});
	});
</script>