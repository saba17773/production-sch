<?php $this->layout("layouts/base", ['title' => 'Cured Inventory']); ?>
<h1 class="head-text">Cured Inventory Report</h1>
<hr>
<div class="panel panel-default form-center">
  <div class="panel-body">
    <form id="form_internal" method="post" action="<?php echo root; ?>/api/pdf/cureinventory" target="_blank">

    <div class="form-group" style="display: block;">
      <strong>Type : </strong> 
      <label style="padding-left: 40px;">
        <input type="radio" name="item_group" value="tbr" /> TBR
      </label>
      <label style="padding-left: 40px;">
        <input type="radio" name="item_group" value="pcr" /> PCR
      </label>
    </div>
		
		<button type="submit" class="btn btn-primary btn-lg btn-block">Print</button>

	</form>
  </div>
</div>

<script type="text/javascript">
	
</script>