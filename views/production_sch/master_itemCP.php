<?php
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>

<h4>Item Master Compound</h4>

<div id="grid_item"></div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		loadgriditem(1);

		function loadgriditem(boiler) {

		    var dataAdapter = new $.jqx.dataAdapter({
		    datatype: "json",

		    datafields: [

		      { name: "ItemBOM", type: "string" },
		      { name: "ItemCP", type: "string" },
		      { name: "ITEMNAME", type: "string" },
		      { name: "DSGRIMSIZE", type: "int" },
		      { name: "DSGPATTERNID", type: "int" },
		      { name: "DSGTyre_Types", type: "int" },
		      { name: "DSG_COLOR", type: "string" }

	        ],
		      url : '/production/sch/load/itemCP?boiler='+boiler
		    });

		    return $("#grid_item").jqxGrid({
		        width: '100%',
		        source: dataAdapter,
		        autoheight: true,
		        columnsresize: true,
		        pageable: true,
		        filterable: true,
		        showfilterrow: true,
		        pagesize: 15,
		        editable : true,
		      	columns: [

			       	{ text:"Item EXT", datafield: "ItemBOM", align: 'center',width: '100', editable:false},
			       	{ text:"Item CP", datafield: "ItemCP", align: 'center',width: '100', editable:false},
              { text:"Name TH", datafield: "ITEMNAME", align: 'center',width: '320', editable:false},
			       	{ text:"PR", datafield: "DSGRIMSIZE", align: 'center',width: '100', editable:false},
			       	{ text:"Pattern", datafield: "DSGPATTERNID", align: 'center',width: '100', editable:false},
			       	{ text:"TT", datafield: "DSGTyre_Types", align: 'center',width: '100',editable:false},
              { text:"Color", datafield: "DSG_COLOR", align: 'center',width: '200'}

				]
		    });

		}

	});
</script>
