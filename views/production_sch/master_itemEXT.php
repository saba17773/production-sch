<?php
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>

<h4>Item Master Extruder</h4>

<div id="grid_item"></div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		loadgriditem(1);

		function loadgriditem(boiler) {

		    var dataAdapter = new $.jqx.dataAdapter({
		    datatype: "json",
		    // updaterow: function (rowid, rowdata, commit) {
		    //     gojax('post', '/production/sch/update/itemGT', {
				// 	ID 	 	: rowdata.Id,
				// 	Color	: rowdata.ColorAll
        //
		    //     }).done(function(data) {
		    //       if (data.result === 200) {
		    //       	$('#grid_item').jqxGrid('updatebounddata');
		    //         commit(true);
		    //       }else{
		    //       	$('#grid_item').jqxGrid('updatebounddata');
		    //       }
		    //     }).fail(function() {
		    //       	commit(false);
		    //     });
        //
		    // },
		    datafields: [
		     // { name: "Id", type: "string" },
		     // { name: "ItemFG", type: "string" },
		      { name: "ItemGT", type: "string" },
		      { name: "ItemBOM", type: "string" },
		      { name: "ITEMNAME", type: "string" },
		      { name: "DSGRIMSIZE", type: "int" },
		      { name: "DSGPATTERNID", type: "int" },
		      { name: "DSGTyre_Types", type: "int" },
		      { name: "DSG_COLOR", type: "string" }

	        ],
		      url : '/production/sch/load/itemEXT?boiler='+boiler
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
			       //	{ text:"Item FG", datafield: "ItemFG", align: 'center',width: '100', editable:false},
			       	{ text:"Item GT", datafield: "ItemGT", align: 'center',width: '100', editable:false},
			       	{ text:"Item EXT", datafield: "ItemBOM", align: 'center',width: '100', editable:false},
              { text:"Name TH", datafield: "ITEMNAME", align: 'center',width: '320', editable:false},
			       	{ text:"PR", datafield: "DSGRIMSIZE", align: 'center',width: '100', editable:false},
			       	{ text:"Pattern", datafield: "DSGPATTERNID", align: 'center',width: '100', editable:false},
			       	{ text:"TT", datafield: "DSGTyre_Types", align: 'center',width: '100',editable:false},
              { text:"Color", datafield: "DSG_COLOR", align: 'center',width: '200'}
              // { text:"Size", datafield: "Size", align: 'center',width: '100',cellsformat: 'F2'},
              // { text:"Type Tires",  datafield: "TypeTires",align: 'center',width: '100'},
              // { text:"Type Tires By Rim", datafield: "TypeTiresByRim",  align: 'center',width: '100'}
              // { text:"Color5", datafield: "Color5", align: 'center',width: '100'}
				]
		    });

		}

	});
</script>
