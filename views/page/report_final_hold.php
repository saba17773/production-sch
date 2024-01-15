<?php $this->layout("layouts/base", ["title" => "Report Final Hold"]); ?>

<h1>Report Final Hold</h1>

<hr>

<div id="grid_final_hold"></div>

<script type="text/javascript">
	jQuery(document).ready(function($){
		grid_final_hold();
	});
	function grid_final_hold(){
		var dataAdapter = new $.jqx.dataAdapter({
		datatype: "json",
      datafields: [
          { name: "CodeID", type: "string" },
          { name: "Barcode", type: "string" },
          { name: "CuringCode", type:"string"},
          { name: "Batch", type:"string"},
          { name: "DefectID", type:"string"},
          { name: "DefectDesc", type:"string"},
          { name: "UpdateDate", type:"date"},
          { name: 'NameTH', type:'string'},
          { name: "PressNo", type:"string"},
          { name: "PressSide", type:"string"},
          { name: "GT_Code", type:"string"},
          { name: 'DateBuild', type:'date'},
          { name: 'Disposal', type: 'string'},
          { name: 'Shift', type: 'string'}
      ],
      // filter : function () {
      //   $('#grid_final_hold').jqxGrid('updatebounddata', 'filter');
      // },
      url : base_url+'/api/report/final/hold'
	});

	return $("#grid_final_hold").jqxGrid({
        width: '100%',
        source: dataAdapter,
        autoheight: true,
        altrows : true,
        sortable: true,
        filterable : true,
        showfilterrow : true,
        columnsresize: true,
        pageable: true,
        pageSize: 20,

        // theme : 'theme',
        columns: [
         { text: 'No.', width: 50,
         	cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
          		return '<div style=\'padding: 5px; color:#000000;\'> '+ (index+1) +' </div>';
            }
       	},
         { text:"วันที่", datafield: "UpdateDate", cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150},
          { text:"Barcode", datafield: "Barcode", width: 100},
          { text:"Curing Code", datafield: "CuringCode", width: 100},
          { text: 'Item Name', datafield: 'NameTH', width: 400},
          { text: 'Batch', datafield: 'Batch', width: 100},
          { text:"Date Build", datafield: 'DateBuild', cellsformat: 'yyyy-MM-dd HH:mm:ss',  width: 180} ,
          { text:"GT Code", datafield: 'GT_Code', width: 100} ,
          { text:"Press No.", datafield: 'PressNo', width: 100} ,
          { text:"Press Side", datafield: 'PressSide', width: 100} ,
           { text:"Shift", datafield: 'Shift', width: 100} ,
           // { text: 'Disposition', datafield: 'Disposal', width: 100},
          { text:"Description", datafield: 'DefectDesc',width: 300}                     
          ]
	    });

	}
</script>