<?php 
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>

<h4>Setup Time</h4>
<div id="grid_time"></div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		loadgridtime();

		function loadgridtime() {

		    var dataAdapter = new $.jqx.dataAdapter({
		    datatype: "json",
		    updaterow: function (rowid, rowdata, commit) {
		        gojax('post', '/production/sch/update/time', {
					id 	 	: rowdata.TimeID,
					hours	: rowdata.TimeHour,
					active  : rowdata.Active
		        }).done(function(data) {
		          if (data.result === 200) {
		          	$('#grid_time').jqxGrid('updatebounddata');
		            commit(true);
		          }else{
		          	$('#grid_time').jqxGrid('updatebounddata');
		          }
		        }).fail(function() {
		          	commit(false);
		        });
		    },
		    datafields: [
		      { name: "TimeID", type: "int" },
		      { name: "TimeHour", type: "int" },
		      { name: "TimeMinute", type: "int" },
		      { name: "Active", type: "int" }
	        ],
		      url : '/production/sch/load/time'
		    });

		    return $("#grid_time").jqxGrid({
		        width: '30%',
		        source: dataAdapter,
		        autoheight: true,
		        columnsresize: true,
		        pageable: true,
		        filterable: true,
		        showfilterrow: true,
		        pagesize: 15,
		        editable : true,
		      	columns: [
			       	{ text:"Hours", datafield: "TimeHour", align: 'center'},
			       	{ text:"Minutes", datafield: "TimeMinute", align: 'center', editable: false},
			       	{ text:"Active", datafield: "Active", align: 'center', columntype: 'checkbox'}
				]
		    });

		}

	});
</script>