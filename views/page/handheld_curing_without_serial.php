<?php 

$this->layout("layouts/handheld", ['title' => 'Handheld Curing']); ?>

<style>
	body {
		font-size: 14px;
	}
</style>
<hr>
<div style="padding: 0; width: 100%; text-align: center;" >
  <a class="btn btn-primary btn-block btn-sm" href="<?php echo root; ?>/curing">
  	<span class="glyphicon glyphicon-home"></span> Home
  </a>
  |
  <a class="btn btn-danger btn-block btn-sm" href="<?php echo root; ?>/user/logout">
    <span class="glyphicon glyphicon-log-out"></span> Logout
  </a>
</div>
<hr>
<form id="form_curing" style="padding: 5px; display: block; text-align: center;">
	<div style="padding-bottom:10px;">
		<div style="font-weight:bold; padding-bottom: 5px;">Curing Code</div>
		<input type="text" id="curing_code" name="curing_code" class="inputs" autocomplete="off" style="width:150px;">
	</div>

	<div style="padding-bottom:10px;">
		<div style="font-weight:bold; padding-bottom: 5px;">Barcode</div>
		<input type="text" id="barcode" name="barcode" class="inputs" autocomplete="off" style="width:150px;">
	</div>
	
	<input type="hidden" name="cure_type" value="without_serial" />	
</form>

<div id="show_result"></div>

<script>
	
	jQuery(document).ready(function($) {
		
		$('input[name=barcode]').keydown(function(event) {
			if (event.which === 13) {
				$('form#form_curing').submit();
			}
		});

		$('#show-error').on('click', function() {
			$('#show-error').hide();
			$('#form_curing').trigger('reset');
			$('#curing_code').focus();
		});

		$('form#form_curing').on('submit', function(e) {
			e.preventDefault();

			$('#curing_code').css({'background': '#eeeeee'}).prop('readonly', true);
			// $('#template_code').css({'background': '#eeeeee'}).prop('readonly', true);
			$('#barcode').css({'background': '#eeeeee'}).prop('readonly', true);

			gojax_f('post', '/api/curing/save' ,'#form_curing')
				.done(function(data) {
					if (data.status == 200) {
						$('#show_result')
							.css({
								'margin': '10px 0px', 
								'padding': '0px 5px',
								'color': 'green'
							})
							.text(data.message);
							// Hide Success
							setTimeout(function() {
								$('#show_result').css('margin', '10px auto').text('');
							}, 3000);
					} else {
						$('#show_result')
							.css({
								'margin': '10px 0px', 
								'padding': '0px 5px',
								'text-align': 'center',
								'color': 'red'
							})
							.text(data.message);

							$('#show-error').show();
							$('#show-error-text').text(data.message);
					}
					
					$('#curing_code').css({'background': '#ffffff'}).prop('readonly', false);
					// $('#template_code').css({'background': '#ffffff'}).prop('readonly', false);
					$('#barcode').css({'background': '#ffffff'}).prop('readonly', false);
					$('form#form_curing').trigger('reset');
					document.getElementById("curing_code").focus();
				})
				.fail(function(e) {
					$('#curing_code').css({'background': '#ffffff'}).prop('readonly', false);
					// $('#template_code').css({'background': '#ffffff'}).prop('readonly', false);
					$('#barcode').css({'background': '#ffffff'}).prop('readonly', false);

					$('#show_result').css({
						'margin': '10px auto', 
						'text-align': 'center',
						'padding': '0px 5px',
						'color': 'red'
					})
					.text('cannot send data to server.');
				});
		});

		$("#curing_code").focus();

	});

	function setFocus() {
		document.getElementById('curing_code').focus();
		return false;
	}

	// function form_curing_submit() {
	// 	gojax_f('post', base_url+'/api/curing/save' ,'#form_curing')
	// 		.done(function(data) {
	// 			if (data.status == 200) {
	// 				$('#show_result')
	// 					.css({
	// 						'margin': '10px 0px', 
	// 						'padding': '0px 5px',
	// 						'color': 'green'
	// 					})
	// 					.text(data.message);
	// 					// Hide Success
	// 					setTimeout(function() {
	// 						$('#show_result').css('margin', '10px 0px').text('');
	// 					}, 3000);
	// 			} else {
	// 				$('#show_result')
	// 					.css({
	// 						'margin': '10px 0px', 
	// 						'padding': '0px 5px',
	// 						'color': 'red'
	// 					})
	// 					.text(data.message);

	// 				$('#show-error').show();
	// 				$('#show-error-text').text(data.message);
	// 			}
				
	// 			$('form#form_curing').trigger('reset');
	// 			document.getElementById("curing_code").focus();
	// 		})
	// 		.fail(function() {

	// 			$('#show_result')
	// 				.css({
	// 					'margin': '10px 0px', 
	// 					'padding': '0px 5px',
	// 					'color': 'red'
	// 				})
	// 				.text("ทำรายการไม่สำเร็จ");

	// 			$('form#form_curing').trigger('reset');
	// 			document.getElementById("curing_code").focus();
	// 		});

	// 	return false;
	// }
</script>