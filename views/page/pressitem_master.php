<?php $this->layout("layouts/base", ['title' => 'Press item master']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>


<div class="head-space"></div>

<div class="panel panel-default">
  <div class="panel-heading">Press item</div>
  <div class="panel-body">
    <div class="btn-panel">

        <button class="btn btn-warning" id="map_item">Map Item</button>

    </div>

    <table width="100%">
  <tr>
    <td valign="top" width="40%">
      <div id="grid_press"></div>
    </td>
    <td valign="top">
      <div id="grid_item"></div>
    </td>
  </tr>
  </table>

  </div>
</div>

<!-- Modal -->
<div class="modal" id="modal_create" tabindex="-1" role="dialog">
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
              <!-- <input type="text" class="form-control" name="itemid" readonly> -->
              <!-- <span class="input-group-btn"> -->
               <!--  <button class="btn btn-info" id="select_press" type="button">เลือก item</button> -->
               <select name="select_item[]" multiple="multiple" class="select_multiple" id="select_item" style="width:500px;"></select>
              <!-- </span> -->
            </div><!-- /input-group -->
          </div>

          <input type="hidden" name="pressid">
          <input type="hidden" name="form_type">
          <button class="btn btn-primary" type="submit">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

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


  var item_selected = [];


  grid_press();


  $('#map_item').on('click', function(event) {
      $('#form-group-map-item').show();
      var rowdata = row_selected("#grid_press");
      if (typeof rowdata !== 'undefined') {

        $('#modal_create').modal({backdrop: 'static'});
        $('input[name=pressid]').prop('readonly', true);
        $('input[name=pressname]').prop('readonly', true);
        $('input[name=form_type]').val('map_item');
        $('.modal-title').text('Map Item');
        $('input[name=pressid]').val(rowdata.ID);
        $('input[name=pressname]').val(rowdata.Description);

        var cast_item = [];

        gojax('get', base_url+'/api/pressitem/all', {id:rowdata.ID})
          .done(function(data) {
            $.each(data, function(k, v) {
                cast_item.push(v.ITEMID)
            });

        });


       $('#select_item').html('').multipleSelect({placeholder: 'เลือกข้อมูล', filter: true});
       gojax('get', base_url+'/api/item/all')
       .done(function(data) {
         $('#select_item').html("");
           $.each(data, function(k, v) {
             if($.inArray(v.ID,cast_item)=== -1)
             {
                $('#select_item').append('<option value="'+ v.ID +'" >'+v.ID+'</option>');
             }
             else {
                $('#select_item').append('<option value="'+ v.ID +'"selected>'+v.ID+'</option>');
             }

           });
           $('#select_item')
							.multipleSelect({
								placeholder: 'เลือกข้อมูล',
								single: true
					});
       });

      }
      else //end if
      {
        alert('Select press!!');
      }
  });

  $('#select_press').on('click', function(event) {
      $('#modal-press').modal({backdrop: 'static'});
  });

  //
  // $('#grid_press').on('rowdoubleclick', function(event) {
  //     var rowdata = row_selected('#grid_press');
  //     $('input[name=pressname]').val(rowdata.Description);
  //     $('#modal-press').modal('hide');
  // });


  $("#grid_press").on('rowselect', function (event) {
      var rowdata = event.args.row;
      if (typeof rowdata !== 'undefined')
      {
          grid_pressitem(rowdata["ID"]);
      }

  });



});


function submit_create()
{
    var i = $('#select_item').val();
    if(i)
    {
        $.ajax({
  				url : base_url + '/api/pressitem/create',
  				type : 'post',
  				cache : false,
  				dataType : 'json',
  				data : $('form#form_create').serialize()
  			})
  			.done(function(data) {
  				if (data.status !== 200) {
  					$('#modal_alert').modal({backdrop: 'static'});
  					$('#modal_alert_message').text(data.message);

  				} else {
  					$('#modal_create').modal('hide');
  					$('#grid_item').jqxGrid('updatebounddata');
  				}
  				$('button').prop('disabled', false);
  			});
        return false;
    }
    else {
      $('#modal_alert').modal({backdrop: 'static'});
			$('#modal_alert_message').text('กรุณาใส่ข้อมูลให้ครบถ้วน');
    }
}

function grid_select_item()
{
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'ID', type: 'string'},
        { name: 'NameTH', type: 'string'}
      ],
      url: '/api/item/all'
    });

    return $("#grid_select_item").jqxGrid({
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
        { text: 'ID', datafield: 'ID', editable: false, width: 100},
        { text: 'Name', datafield: 'NameTH', editable: false, width: 600}
      ]
    });
}


function grid_press()
{
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
      datafields: [
        { name: 'ID', type: 'string'},
        { name: 'Description', type: 'string'}
      ],
      url: '/api/press/all'
    });

    return $("#grid_press").jqxGrid({
      width: '50%',
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
        { text: 'ID', datafield: 'ID', editable: false, width: 100},
        { text: 'Description', datafield: 'Description', editable: false, width: 200}

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
    width: '80%',
    source: dataAdapter,
    autoheight: true,
    pageSize : 10,
    altrows : true,
    pageable : true,
    sortable: true,
    filterable : true,
    showfilterrow : true,
    columnsresize: true,
    // editable: true,
    columns: [
      { text: 'ITEMID', datafield: 'ITEMID', editable: false, width: 100},
      { text: 'NAME', datafield: 'NAME', editable: false, width: 500}

    ]
  });
}
</script>
