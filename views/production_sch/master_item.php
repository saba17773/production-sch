<?php 
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>

<h4>Item Master</h4>

<div id="grid_item"></div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		loadgriditem(1);

		function loadgriditem(boiler) {

		    var dataAdapter = new $.jqx.dataAdapter({
		    datatype: "json",
		    updaterow: function (rowid, rowdata, commit) {
		        gojax('post', '/production/sch/update/item', {
					ID 	 	: rowdata.ID,
					Color1	: rowdata.Color1,
					Color2  : rowdata.Color2,
					Color3	: rowdata.Color3,
					Color4  : rowdata.Color4,
					Color5	: rowdata.Color5
		        }).done(function(data) {
		          if (data.result === 200) {
		          	$('#grid_item').jqxGrid('updatebounddata');
		            commit(true);
		          }else{
		          	$('#grid_item').jqxGrid('updatebounddata');
		          }
		        }).fail(function() {
		          	commit(false);
		        });
		    },
		    datafields: [
		      { name: "ID", type: "string" },
		      { name: "ItemName", type: "string" },
		      { name: "Pattern", type: "string" },
		      { name: "Brand", type: "string" },
		      { name: "RateCure", type: "string" },
		      { name: "ItemNameThai", type: "string" },
		      { name: "NetWeight", type: "int" },
		      { name: "Color1", type: "string" },
		      { name: "Color2", type: "string" },
		      { name: "Color3", type: "string" },
		      { name: "Color4", type: "string" },
		      { name: "Color5", type: "string" }
	        ],
		      url : '/production/sch/load/item?boiler='+boiler
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
			       	{ text:"Item Id", datafield: "ID", align: 'center',width: '10%', editable:false},
			       	{ text:"Name", datafield: "ItemName", align: 'center', editable:false},
			       	{ text:"Name TH", datafield: "ItemNameThai", align: 'center', editable:false},
			       	{ text:"Pattern", datafield: "Pattern", align: 'center',width: '10%', editable:false},
			       	{ text:"Brand", datafield: "Brand", align: 'center',width: '5%', editable:false},
			       	{ text:"RateCure", datafield: "RateCure", align: 'center',width: '5%', editable:false},
			       	{ text:"NetWeight", datafield: "NetWeight", align: 'center',width: '8%', editable:false},
			       	{ text:"Color1", datafield: "Color1", align: 'center',width: '5%'},
			       	{ text:"Color2", datafield: "Color2", align: 'center',width: '5%'},
			       	{ text:"Color3", datafield: "Color3", align: 'center',width: '5%'},
			       	{ text:"Color4", datafield: "Color4", align: 'center',width: '5%'},
			       	{ text:"Color5", datafield: "Color5", align: 'center',width: '5%'}
				]
		    });

		}

	});
</script>