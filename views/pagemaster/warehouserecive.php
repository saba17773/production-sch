<?php $this->layout("layouts/base", ['title' => 'ใบรายงาน รับสินค้าเข้าคลังสินค้า']); ?>
<div class="head-space"></div>
<div class="panel panel-default form-center">
	<div class="panel-heading">ใบรายงาน รับสินค้าเข้าคลังสินค้า</div>
  <div class="panel-body">
    <form id="form_building" method="post" action="<?php echo root; ?>/api/pdf/warehouse"
		onsubmit="return form_building()" target="_blank">

		<div class="form-group">
			<label for="date">Date</label>
			<input type="text" id="datewarehouse" name="datewarehouse" class=form-control required  placeholder="เลือกวันที่..." />
		</div>
		<div class="form-group">
			<label for="shift">Shift</label>
			<select name="shift" id="shift" class="form-control" required>
			  <option value="day">กลางวัน</option>
			  <option value="night">กลางคืน</option>
			</select>
		</div>
        <div class="form-group">
            <label for="brand">Brand</label><br>
            <select name="selectbrand[]" id="selectbrand"  multiple="multiple" style="width: 400px">
            </select>
        </div>
        <div class="form-group">
            <label for="time">Time</label><br>
            <select name="selecttime[]" id="selecttime"  multiple="multiple" style="width: 400px">
            </select>
        </div>
		<input type="hidden" name="warehouse" id="warehouse" value="recive">
		<button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-print"></span> Print</button>

	</form>
  </div>
</div>

<script type="text/javascript">
	$( "#datewarehouse" ).datepicker({dateFormat: 'dd-mm-yy'});
	$('#shift').on('change', function(){
		  var val = $(this).val();
		  if(val === 'day') {
		    //alert('day!') 
		    $('#selecttime').html("");
		  	getPressSide()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
				});
				$('#selecttime').multipleSelect();
			});
		  }
		  else if(val === 'night') {
		    //alert('night!') 
		    $('#selecttime').html("");
		  	getPressSideN()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
				});
				$('#selecttime').multipleSelect();
			});
		  }
  	});

	
	getPressSide()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selecttime').append('<option value="'+ v.TimeID +'">'+v.TimeTo+'-'+v.TimeFrom+'</option>');
				});
				$('#selecttime').multipleSelect();
			});
	getBrand()
			.done(function(data) {
				$.each(data, function(k, v) {
					$('#selectbrand').append('<option value="'+ v.BrandID +'">'+v.BrandName+'</option>');
				});
				$('#selectbrand').multipleSelect();
			});
			
	function getPressSide() {
		return $.ajax({
			url : base_url + '/api/press/allday',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}
	function getPressSideN() {
		return $.ajax({
			url : base_url + '/api/press/allnight',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}
	function getBrand() {
		return $.ajax({
			url : base_url + '/api/brand/allbrand',
			type : 'get',
			dataType : 'json',
			cache : false
		});
	}
</script>