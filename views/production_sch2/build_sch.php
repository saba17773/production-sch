<?php $this->layout("layouts/base", ['title' => 'Scheduler Build Page']); ?>

<div class="panel panel-default" style="margin-top: 10px;">
  <div class="panel-heading">Build Scheduler</div>
  <div class="panel-body scroll-x">
  	<div class="mb-2">
      <button class="btn btn-default" id="btnViewData"><i class="fa fa-list"></i> ตรวจสอบข้อมูล</button>
    </div>
    <table id="gridBuildTrans" class="mb-2 table table-striped table-bordered table-hover table-condensed table-nowrap" style="width:100%;">
      <thead>
        <tr>
			<th>DateBuild</th>
			<th>Shift</th>
			<th>BuildName</th>
			<th>CreateBy</th>
        </tr>
        <tr>
          	<th>DateBuild</th>
			<th>Shift</th>
			<th>BuildName</th>
			<th>CreateBy</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		loadGrid({
	      el: "#gridBuildTrans",
	      processing: true,
	      serverSide: true,
	      deferRender: true,
	      searching: true,
	      order: [],
	      orderCellsTop: true,
	      destroy: true,
	      select: {
	        style: "single",
	      },
	      ajax: {
	        url: "/sch/api/buildsch/get_all_group",
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
	          data: "BuildName",
	        },
	        {
	          data: "CreateBy",
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

		$("#btnViewData").on("click", function() {
	      var rowdata = getRowsSelected("#gridBuildTrans");
	      if (rowdata[0].Shift=="C") {
	      	shift = 1;
	      }else{
	      	shift = 2;
	      }

	      if (rowdata.length > 0) {
	        var dd = dayjs(rowdata[0].DateBuild).format('YYYY-MM-DD');
        	window.open("/sch/build/list/" + shift + "/" + dd + '/view', '_blank');
	      } else {
	        alert("Please select data.");
	      }
	    });

	});
</script>