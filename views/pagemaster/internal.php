<?php $this->layout("layouts/base", ['title' => 'Final Withdraw']); ?>
<h1 class="head-text">Final Withdraw Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_internal" method="post" action="<?php echo root; ?>/api/pdf/internal"
		onsubmit="return form_internal()" target="_blank">

		<div class="form-group">
			<label for="date">Date</label>
			<input type="text" id="date_internal" name="date_internal" class=form-control required  placeholder="เลือกวันที่..." />
		</div>
		
		<button type="submit" class="btn btn-primary btn-lg btn-block">
			<span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report
		</button>

	</form>
  </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$( "#date_internal" ).datepicker({dateFormat: 'dd-mm-yy'});
	});
</script>