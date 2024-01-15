<?php $this->layout("layouts/base", ['title' => 'Item Master']); ?>

<h1>Item Master</h1>

<hr>

<div>
  <div id="grid_item"></div>
</div>

<script>

$(function() {
  grid_item();
});

function grid_item() {
  var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
    datafields: [
      { name: 'ID', type: 'string'},
      { name: 'NameTH', type: 'string'},
      { name: 'QtyPerPallet', type: 'number'},
      { name: 'ManualBatch', type: 'bool'},
      { name: 'CheckSerial', type: 'bool'}
    ],
    url: '/api/v2/item/all',
    updaterow: function (rowid, rowdata, commit) {
      gojax('post', '/api/v1/item/update_master', {
        itemId: rowdata.ID,
        manualBatch: rowdata.ManualBatch,
        checkSerial: rowdata.CheckSerial
      }).done(function(data) {
        // console.log(data);
      });
      commit(true);
    }
  });

	return $("#grid_item").jqxGrid({
    width: '100%',
    source: dataAdapter, 
    autoheight: true,
    pageSize : 10,
    altrows : true,
    pageable : true,
    sortable: true,
    filterable : true,
    showfilterrow : true,
    columnsresize: true,
    editable: true,
    columns: [
      { text: 'Item ID', datafield: 'ID', editable: false, width: 100},
      { text: 'Item Name', datafield: 'NameTH', editable: false, width: 500},
      { text: 'Qty/Pallet', datafield: 'QtyPerPallet', editable: false, width: 100},
      { text: 'Manual Batch', datafield: 'ManualBatch', columntype: 'checkbox', filtertype: 'bool', width: 100},
      { text: 'Check Serial', datafield: 'CheckSerial', columntype: 'checkbox', filtertype: 'bool', width: 100}
    ]
  });
}
</script>
