<?php $this->layout("layouts/base", ['title' => 'Building']); ?>
<h1 class="head-text">Building Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_building" method="post" action="<?php echo root; ?>/api/pdf/building" target="_blank">

		<div class="form-group">
			<label for="date">Date</label>
			<input type="text" id="date_building" name="date_building" class=form-control required  placeholder="เลือกวันที่..." />
		</div>
		<div class="form-group">
			<label for="shift">Shift</label>
			<select name="shift" id="shift" class="form-control" required>
			  <option value="day">กลางวัน</option>
			  <option value="night">กลางคืน</option>
			</select>
		</div>

		<div class="form-group" style="display: block;">
			<strong>Type : </strong> 
			<label style="padding-left: 40px;">
				<input type="radio" name="item_group" value="tbr" /> TBR
			</label>
			<label style="padding-left: 40px;">
				<input type="radio" name="item_group" value="pcr" /> PCR
			</label>
		</div>

		<!-- <div class="form-group">
			<label for="group">Group</label>
			<select name="group" id="group" class="form-control" required></select>
		</div> -->
		
		<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>

	</form>
  </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$( "#date_building" ).datepicker({dateFormat: 'dd-mm-yy'});

		getPressSide()
			.done(function(data) {
				$('select[name=group]').html('');
				$.each(data, function(index, val) {
					$('select[name=group]').append('<option value="'+val.ID+'">'+val.Description+'</option>');
				});
			});
	});

	function getPressSide() {
		return $.ajax({
			url : base_url + '/api/shift/all',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}

</script>