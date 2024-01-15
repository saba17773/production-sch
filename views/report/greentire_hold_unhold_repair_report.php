<?php $this->layout("layouts/base", ["title" => "Greentire Unhold/Unrepair Report"]); ?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
	<div class="panel-heading">Greentire Unhold/Unrepair Report</div>
	<div class="panel-body">
		<form action="<?php echo root; ?>/report/greentire/hold_unhold_repair/pdf" method="post" target="_blank">
			<div class="form-group">
				<label for="date">Date</label>
				<input type="text" id="_date" name="_date" class=form-control required  placeholder="เลือกวันที่..." />
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

			<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-print"></span> Print</button>
		</form>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$( "#_date" ).datepicker({dateFormat: 'dd-mm-yy'});
	});
</script>