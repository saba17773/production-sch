<?php $this->layout("layouts/base", ['title' => 'Tracking']); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Tracking</div>
	<div class="panel-body">
		<form id="form_tracking">
			<div class="form-group">
				<input type="text" class="form-control input-lg" name="search" placeholder="Barcode">
			</div>
		</form>		
	</div>
</div>

<!-- Modal -->
<div class="modal" id="modal_detail" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
        	<span class="glyphicon glyphicon-remove"></span>
        	ปิดหน้าต่างนี้
        </button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
       	<table class="table">
       		<tr style="font-size: 2em;">
       			<td>Disposition</td>
       			<td id="d_disposal"></td>
       		</tr>

          <tr>
            <td>Status</td>
            <td id="d_status"></td>
          </tr>
          <tr>
            <td>Barcode Foil</td>
            <td id="d_barcode_foil"></td>
          </tr>
       		<tr>
       			<td>Building MC.</td>
       			<td id="d_building_mc"></td>
       		</tr>
       		<tr>
       			<td>Date Build</td>
       			<td id="d_date_build"></td>
       		</tr>
       		<tr>
       			<td>GT Code</td>
       			<td id="d_gt_code"></td>
       		</tr>
       		<!--  -->
       		<tr>
       			<td>Curing Date</td>
       			<td id="d_curing_date"></td>
       		</tr>
       		<tr>
       			<td>Curing Code</td>
       			<td id="d_curing_code"></td>
       		</tr>
       		<tr>
       			<td>Item Id</td>
       			<td id="d_item_id"></td>
       		</tr>
       		<tr>
       			<td>Item Name</td>
       			<td id="d_item_name"></td>
       		</tr>
       		<tr>
       			<td>Batch No.</td>
       			<td id="d_batch_no"></td>
       		</tr>
       		<tr>
       			<td>Template Serial No</td>
       			<td id="d_template_no"></td>
       		</tr>
       	</table>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {
		var barcode = $("input[name=search]");
		barcode.val('').focus();

		$('#modal_detail').on('hidden.bs.modal', function() {
			$(onFocus).val('').focus();
		});

		$('#form_tracking').submit(function(e) {
			e.preventDefault();

			if ($.trim(barcode.val()) !== '') {
				gojax_f('post', base_url+'/api/search/barcode', '#form_tracking')
				.done(function(data) {
					if (data.status !== 404) {
						$('#modal_detail').modal({backdrop: 'static'});
						$('.modal-title').text('Barcode : ' + data[0].BARCODE);
						// $('#d_barcode').text(data[0].BARCODE);
            
            var status_color = 'black';

            if (data[0].STATUSID === 1) {
              status_color = 'green';
            } else if (data[0].STATUSID === 2) {
              status_color = 'orange';
            } else if (data[0].STATUSID === 3) {
              status_color = 'red';
            } else if (data[0].STATUSID === 4) {
              status_color = 'red';
            } else if (data[0].STATUSID === 5) {
              status_color = 'orange';
            } else {
              status_color = 'black';
            }

						$('#d_disposal').text(data[0].DISPOSAL).css({
              'color': 'green',
              'font-weight': 'bold'
            });
						
						if ( data[0].BARCODEFOIL == null ) { data[0].BARCODEFOIL = "-"; }
						if ( data[0].BUILDINGMC == null ) { data[0].BUILDINGMC = "-"; }
						if ( data[0].BUILDINGDATE == null ) { data[0].BUILDINGDATE = "-"; }
						if ( data[0].GTCODE == null ) { data[0].GTCODE = "-"; }
						if ( data[0].CURINGDATE == null ) { data[0].CURINGDATE = "-"; }
						if ( data[0].CURINGCODE == null ) { data[0].CURINGCODE = "-"; }
						if ( data[0].ITEMID == null ) { data[0].ITEMID = "-"; }
						if ( data[0].ITEMNAME == null ) { data[0].ITEMNAME = "-"; }
						if ( data[0].BATCH == null ) { data[0].BATCH = "-"; }
						if ( data[0].TEMPLATE == null ) { data[0].TEMPLATE = "-"; }
						if ( data[0].STATUS == null ) { data[0].STATUS = "-"; }

            $('#d_barcode_foil').text(data[0].BARCODEFOIL);
						$('#d_building_mc').text(data[0].BUILDINGMC);
						$('#d_date_build').text(data[0].BUILDINGDATE);
						$('#d_gt_code').text(data[0].GTCODE);
						$('#d_curing_date').text(data[0].CURINGDATE);
						$('#d_curing_code').text(data[0].CURINGCODE);
						$('#d_item_id').text(data[0].ITEMID);
						$('#d_item_name').text(data[0].ITEMNAME);
						$('#d_batch_no').text(data[0].BATCH);
						$('#d_template_no').text(data[0].TEMPLATE);
            $('#d_status').text(data[0].STATUS).css({
              'font-weight': 'bold',
              'color': status_color
            });
						onFocus = 'input[name=search]';
					} else {
						alert('ไม่พบข้อมูล');
						barcode.val('').focus();
					}
				});
			} else {
				barcode.val('').focus();
			}
		});
	});
</script>