<?php $this->layout("layouts/base", ['title' => 'Movement']); ?>
<?php $PermissionService = new App\Services\PermissionService; ?>
<div class="head-space"></div>

<div class="panel panel-default">
  <div class="panel-heading">Movement Issue</div>
  <div class="panel-body">
    <div class="btn-panel">
      <?php $detect = new \Mobile_Detect; ?>
      <?php if ($detect->isMobile()){ ?>
        <button class="btn btn-success" id="newIssue">New</button>
        <button class="btn btn-info" id="selectEmp">Select</button>
      <?php } else { ?>
        <button class="btn btn-default" id="line">Line</button>
        <?php if ($PermissionService->getUserAction($_SESSION['user_permission'] , 'print_movement_issue') === true): ?>
          <button class="btn btn-info" id="print" style="display: none;">Print</button>
        <?php endif ?>
      <?php } ?>
    </div>
     <div id="grid_movement"></div>
  </div>
</div>

<!-- Line Modal -->
<div class="modal" id="modal_line" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <!-- Content -->
        <div id="grid_line"></div>
      </div>
    </div>
  </div>
</div>

<script>
	jQuery(document).ready(function($) {

    gojax('get', base_url+'/ismobile').done(function(data) {
      if (data.status === 200) {
        grid_movement_mobile();
      } else {
        grid_movement_desktop();
      }
    });
    
    $('#print').on('click', function() {
      var rowdata = row_selected('#grid_movement');
      if(typeof rowdata !== 'undefined') {
       window.open(base_url+'/movement_issue/print/'+rowdata.InventJournalID+'?mode='+rowdata.JournalTypeID+'&create_date='+rowdata.CreateDate, '_blank');
      }
    });

    $('#grid_movement').on('rowclick', function(event) {
      var args = event.args;
      var boundIndex = args.rowindex;
      var datarow = $("#grid_movement").jqxGrid('getrowdata', boundIndex);
      // console.log(datarow.Status);
      if (datarow.Status === "Completed") {
        $('#print').show();
      } else {
        $('#print').hide();
      }
    });

    $('#line').on('click', function() {
      var rowdata = row_selected('#grid_movement');
      if(typeof rowdata !== 'undefined') {
        $('#modal_line').modal({backdrop: 'static'});
        $('.modal-title').text(rowdata.InventJournalID);
        grid_line(rowdata.InventJournalID);
      }
      
    });

		$('#newIssue').on('click', function(event) {
			event.preventDefault();
			window.location = base_url+'/movement/issue/new';
		});

		$('#selectEmp').on('click', function(event) {
			event.preventDefault();
		
			var rowdata = row_selected('#grid_movement');
			if(typeof rowdata !== 'undefined') {
				window.location = base_url+'/movement/issue/new?j='+rowdata.InventJournalID;
			}
		});
	});

  function grid_line(journalId) {
  
    var dataAdapter = new $.jqx.dataAdapter({
    datatype: 'json',
        datafields: [
          { name: 'BarcodeID', type: 'string'},
          { name: 'CuringCode', type: 'string' },
          { name: 'RN', type: 'string'},
          { name: 'CreateBy', type: 'string'},
          { name: 'CreateDate', type: 'date'}
        ],
        url: base_url + '/api/movement_issue/'+journalId+'/latest'
    });

    return $("#grid_line").jqxGrid({
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
      // theme : 'theme',
      columns: [
         { text:"No.", width : 50, 
          cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
            return '<div style=\'padding: 5px; color:#000000;\'> '+ (index+1) +' </div>';
          }
        },
        { text: 'Barcode', datafield: 'BarcodeID', width: 150},
        { text: 'Curing Code', datafield: 'CuringCode', width: 150},
        { text: 'Requsition Note', datafield: 'RN', width: 200},
        { text: 'Create By', datafield: 'CreateBy', width: 150},
        { text: 'Create Date', datafield: 'CreateDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150}
      ]
    });
  }

	function grid_employee() {
	    var dataAdapter = new $.jqx.dataAdapter({
	      datatype: 'json',
	          datafields: [
	            { name: 'Code', type: 'string'},
	            { name: 'FirstName', type: 'string' },
	            { name: 'LastName', type: 'string'},
	            { name: 'Name', type: 'string'},
	            { name: 'DivisionCode', type: 'string' },
	            { name: 'Department', type: 'string'}
	          ],
	          url: base_url + "/api/employee/all/by_status"
	    });

	     return $("#grid_employee").jqxGrid({
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
	          // theme : 'theme',
	          columns: [
	            { text: 'ID', datafield: 'Code', width: 100},
	            { text: 'Name', datafield: 'Name', width: 220},
	            { text: 'Division Code', datafield: 'DivisionCode', width: 100 },
	            { text: 'Department', datafield: 'Department', width: 100}
	          ]
	    });
	}

  function grid_movement_mobile() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
          datafields: [
            { name: 'InventJournalID', type: 'string'},
            { name: 'EmpCode', type: 'string' },
            { name: 'Department', type: 'string' },
            { name: 'Division', type: 'string' },
            { name: 'Name', type: 'string'},
            { name: 'CreateBy', type: 'string'},
            { name: 'CreateDate', type: 'date'}
          ],
          url: base_url + "/api/movement_issue/all"
    });

    return $("#grid_movement").jqxGrid({
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
          // theme : 'theme',
          columns: [
            { text: 'Journal ID', datafield: 'InventJournalID', width: 100},
            { text: 'Name', datafield: 'Name', width: 200},
            { text: 'Department', datafield: 'Division', width: 100},
            { text: 'Create By', datafield: 'CreateBy', width: 120},
            { text: 'Create Date', datafield: 'CreateDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150}
          ]
    });
  }

	function grid_movement_desktop() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
          datafields: [
            { name: 'InventJournalID', type: 'string'},
            { name: 'JournalTypeID', type: 'string'},
            { name: 'EmpCode', type: 'string' },
            { name: 'Department', type: 'string' },
            { name: 'Division', type: 'string' },
            { name: 'Name', type: 'string'},
            { name: 'Status', type: 'string'},
            { name: 'CreateBy', type: 'string'},
            { name: 'CreateDate', type: 'datetime'},
            { name: 'CompleteBy', type: 'string'},
            { name: 'CompleteDate', type: 'date'}
          ],
          url: base_url + "/api/movement_issue/all"
    });

    return $("#grid_movement").jqxGrid({
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
          // theme : 'theme',
          columns: [
            { text: 'Journal ID', datafield: 'InventJournalID', width: 100},
            { text: 'Name', datafield: 'Name', width: 200},
            { text: 'Department', datafield: 'Division', width: 100},
            { text: 'Status', datafield: 'Status', width: 100,  filtertype: 'checkedlist'},
            { text: 'Create By', datafield: 'CreateBy', width: 120},
            { text: 'Create Date', datafield: 'CreateDate', filtertype: 'range', columntype: 'datetimeinput', cellsformat: 'yyyy-MM-dd HH:mm:ss', width: 150},
            { text: 'Complete By', datafield: 'CompleteBy', width: 120},
            { text: 'Complete Date', datafield: 'CompleteDate', width: 150, cellsformat: 'yyyy-MM-dd HH:mm:ss'}
          ]
    });
	}

	function grid_requsition() {
    var dataAdapter = new $.jqx.dataAdapter({
      datatype: 'json',
          datafields: [
            { name: 'ID', type: 'number'},
            { name: 'Description', type: 'string' }
          ],
          url: base_url + "/api/requsition_note/all"
    });

    return $("#grid_requsition").jqxGrid({
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
          // theme : 'theme',
          columns: [
            { text: 'ID', datafield: 'ID', width: 100},
            { text: 'Description', datafield: 'Description', width: 200}
          ]
    });
	}
</script>