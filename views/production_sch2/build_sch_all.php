<?php $this->layout("layouts/base", ['title' => 'Scheduler Build Page']); ?>

<div class="panel panel-default" style="margin-top: 10px;">
  <div class="panel-heading">Build Scheduler</div>
  <div class="panel-body scroll-x">
    <table id="gridBuildTrans" class="mb-2 table table-striped table-bordered table-hover table-condensed table-nowrap" style="width:100%;">
      <thead>
        <tr>
			<th>DateBuild</th>
			<th>Shift</th>
			<th>ItemId</th>
			<th>ItemName</th>
			<th>OrderWeek</th>
			<th>Number BL</th>
			<th>BL</th>
			<th>TargetTemp</th>
			<th>Adjust</th>
			<th>Target</th>
			<th>Actual</th>
			<th>Remark</th>
			<th>Over / Lose</th>
			<th>CreateDate</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		var date = '<?php echo $date; ?>';
		var shift = '<?php echo $shift; ?>';
		loadGrid({
	      el: "#gridBuildTrans",
	      processing: true,
	      serverSide: true,
	      deferRender: true,
	      searching: false,
	      order: [],
	      orderCellsTop: true,
	      destroy: true,
	      select: {
	        style: "single",
	      },
	      ajax: {
	        url: "/sch/api/buildsch/get_all?date="+date+"&shift="+shift,
	        method: "post",
	      },
	      initComplete: function() {
	        $.unblockUI();
	      },
	      columns: [
	      	{
	          data: "DateBuild",
	          name: "date"
	        },
	        {
	          data: "Shift",
	        },
	        {
	          data: "ItemId",
	        },
	        {
	          data: "ItemGTName",
	        },
	        {
	          data: "OrderWeek",
	        },
	        {
	          data: "NumberBL",
	        },
	        {
	          data: "BL",
	        },
	        {
	          data: "TargetTemp",
	        },
	        {
	          data: "Adjust",
	        },
	        {
	          data: "Target",
	        },
	        {
	          data: "Actual",
	        },
	        {
	          data: "Remark",
	        },
	        {
	          data: "OverLose",
	        },
	        {
	          data: "CreateDate",
	        }
	      ],
	      columnDefs: [{
	        render: function(data, type, row) {
	          return dayjs(data).format('DD-MM-YYYY');
	        },
	        targets: 0,
	        render: function(data, type, row) {
              if (row.Shift=="C") {
                return "08:00-20:00";
              }else{
                return "20:00-08:00";
              }
            }, targets: 1
	      }, ],
	    });

	});
</script>