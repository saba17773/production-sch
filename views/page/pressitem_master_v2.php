<?php $this->layout("layouts/base", ['title' => 'Press item master']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>

<style type="text/css">
  td { 
    padding: 5px;
    text-align: center;
  }
</style>

<div class="head-space"></div>

<div class="panel panel-default">
  <div class="panel-heading">Cure</div>
  <div class="panel-body">

    <div id="grid_press"></div>
    
      <hr>
      <div class="panel-heading">Map item</div>

      <table width="100%">
        <tr>
          <td valign="top" width="45%">
            <div id="grid_item"></div>
          </td>
          <td valign="top">
            <button class="btn btn-danger" id="btn_right"><b> out > </b></button>
            <button class="btn btn-success" id="btn_left"><b> < in </b></button>
          </td>
          <td valign="top" width="45%">
            <div id="grid_item_all"></div>
          </td>
        </tr>
      </table>

    </div>
</div>

<!-- Modal -->
<!-- <div class="modal" id="modal_create" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Create new</h4>
      </div>
      <div class="modal-body">
        <form id="form_create" onsubmit="return submit_create()">
          <div class="form-group">
            <label for="pressid">pressid</label>
            <input type="text" name="pressid" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="pressname">name</label>
            <input type="text" name="pressname" class="form-control" required>
          </div>

          <div class="form-group" id="form-group-map-item">
            <label for="pressname">item</label>
            <div class="input-group">
               <select name="select_item[]" multiple="multiple" class="select_multiple" id="select_item" style="width:500px;"></select>
            </div>
          </div>

          <input type="hidden" name="pressid">
          <input type="hidden" name="form_type">
          <button class="btn btn-primary" type="submit">Save</button>
        </form>
      </div>
    </div>
  </div>
</div> -->

<!-- Modal -->
<div class="modal" id="modal-press" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close">
          <span class="glyphicon glyphicon-remove"></span>
          Close
        </button>
        <h4 class="modal-title">press</h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_select_item"></div>
      </div>
    </div>
  </div>
</div>

<script>

$(function() {


  grid_press();
  grid_select_item();

  $("#grid_item_all").hide();
  $('#btn_right').hide();
  $('#btn_left').hide();

  var pressid = '';
  $("#grid_press").on('rowselect', function (event) {
      var rowdata = event.args.row;
      if (typeof rowdata !== 'undefined')
      {
          pressid = rowdata["CurID"];
          grid_pressitem(rowdata["CurID"]);
          $("#grid_item_all").show();
          $('#btn_right').show();
          $('#btn_left').show();
      }

  });

  var item_left = [];
  var item_right =[];

   $('#grid_item_all').on('rowselect', function (event) {
        rowselect = event.args.row;
        item_left.push(rowselect["ID"]);

    });

   $('#grid_item_all').on('rowunselect', function (event) {
        rowselect = event.args.row;
        if (typeof rowselect != 'undefined')
        {
            remove(item_left,rowselect["ID"]);
        }
        
    });

   $('#grid_item').on('rowselect', function (event) {
        rowselect = event.args.row;
        item_right.push(rowselect["ITEMID"]);

    });

   $('#grid_item').on('rowunselect', function (event) {
        rowselect = event.args.row;
        if (typeof rowselect != 'undefined')
        {
             remove(item_right,rowselect["ITEMID"]);
        }
       

    });



  $('#btn_left').on('click', function(event) {

      if(item_left.length > 0)
      {  
            $.ajax({
              url : base_url + '/api/pressitem/create',
              type : 'post',
              cache : false,
              dataType : 'json',
              data : {pressid : pressid , item : item_left, process : 'create'}
            })
            .done(function(data) {
              if (data.status !== 200) {
                $('#modal_alert_message').text(data.message);
              } else {
                $('#grid_item').jqxGrid('updatebounddata');
              }
            });
             $('#grid_item_all').jqxGrid('clearselection');
   
      }
      
   });

  $('#btn_right').on('click', function(event) {

      if(item_right.length > 0)
      {  
            $.ajax({
              url : base_url + '/api/pressitem/create',
              type : 'post',
              cache : false,
              dataType : 'json',
              data : {pressid : pressid , item : item_right, process : 'delete'}
            })
            .done(function(data) {
              if (data.status !== 200) {
                $('#modal_alert_message').text("ลบข้อมูลไม่สำเร็จ");
              } else {
                $('#grid_item').jqxGrid('updatebounddata');
              }
            });
            $('#grid_item').jqxGrid('clearselection');
      }
      
   });


   
});

function remove(array, element) {
    const index = array.indexOf(element);
    array.splice(index, 1);
}


function grid_press()
{
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'ID', type: 'int'},
        { name: 'CurID', type: 'string'},
        { name: 'CureSize', type: 'string'},
        { name: 'Company', type: 'string'}
      ],
      url: '/production/sch/load/cure'
    });

    return $("#grid_press").jqxGrid({
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
        { text: 'CurID', datafield: 'CurID', editable: false, width: 100},
        { text: 'CureSize', datafield: 'CureSize', editable: false, width: 200}

      ]
    });
}

function grid_pressitem(id) {

  var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
    datafields: [
      { name: 'ITEMID', type: 'string'},
      { name: 'NAME', type: 'string'}
    ],
    url: '/api/pressitem/all?id=' + id
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
    selectionmode: 'checkbox',
    columns: [
      { text: 'ITEMID', datafield: 'ITEMID', width: '20%'},
      { text: 'NAME', datafield: 'NAME'}

    ]
  });
}

function grid_select_item()
{
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'ID', type: 'string'},
        { name: 'ItemName', type: 'string'}
      ],
      
      filter : function (data) { 
        $('#grid_item_all').jqxGrid('updatebounddata', 'filter'); 
      },
      url: '/api/item/all'
    });

    return $("#grid_item_all").jqxGrid({
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
      selectionmode: 'checkbox',
      columns: [
        { text: 'ID', datafield: 'ID', width: '20%'},
        { text: 'Name', datafield: 'ItemName'}
      ]
    });
}



</script>
