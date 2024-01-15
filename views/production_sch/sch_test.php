<?php 
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>

<div id="grid_sch"></div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		grid_sch();

		function grid_sch(){
			var dataAdapter = new $.jqx.dataAdapter({
		  	datatype: "json",
		    datafields: [
		          { name: "Boiler", type: "string"}
		      ],
		      url : '/production/sch/loadtest'
		  	});

		    return $("#grid_sch").jqxGrid({
		      width: '100%',
		      source: dataAdapter,
		      autoheight: true,
		      pageSize : 10,
		      // rowsheight : 40,
		      // columnsheight : 40,
		      altrows : true,
		      pageable : true,
		      sortable: true,
		      filterable : true,
		      showfilterrow : true,
		      columnsresize: true,
		      // theme: 'theme',
		      columns: [
		        { text:"Boiler", datafield: "Boiler", width: 100}            
		      ]
		    });
		}

	});
</script>