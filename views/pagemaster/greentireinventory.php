<?php $this->layout("layouts/base", ['title' => 'GreenTire Inventory']); ?>
<h1 class="head-text">GreenTire Inventory Report</h1>
<hr>
<div class="panel panel-default" style="max-width: 400px; margin: 0 auto;">
  <div class="panel-body">
    <form id="form_internal" method="post" action="<?php echo root; ?>/api/pdf/inventory" target="_blank">

    	<div class="form-group" style="display: block;">
			<strong>Type : </strong> 
			<label style="padding-left: 40px;">
				<input type="radio" name="item_group" value="tbr" /> TBR
			</label>
			<label style="padding-left: 40px;">
				<input type="radio" name="item_group" value="pcr" /> PCR
			</label>
		</div>
		
		<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> View Report</button>

	</form>
  </div>
</div>

<script type="text/javascript">
	
</script>