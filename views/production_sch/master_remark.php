<?php 
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>

<h4>Problem Master</h4>
<button class="btn btn-info" id="btn_addremark">
	<span class="glyphicon glyphicon-plus"></span> Create
</button> 
<!-- <button class="btn btn-danger" id="btn_deleteremark">
	<span class="glyphicon glyphicon-remove"></span> Delete
</button>  -->
<hr>
<div id="grid_remark"></div>

<div class="modal" id="modal_remark" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="glyphicon glyphicon-remove-circle"></span>
        </button>
        <h4 class="modal-title">หมายเหตุ</h4>
      </div>

      <div class="modal-body">
		<form id="form_remark">
	        <div class="form-group">
	          	<input type="text" class="form-control" name="txt_remark" id="txt_remark">
	          	<hr>
	          	<button type="submit" class="btn btn-primary">
	          		<span class="glyphicon glyphicon-save"></span> Save
	      		</button>
	        </div>
      	</form>
      </div>
    </div>
  </div>
</div> 

<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		loadgridremark();

		$("#btn_addremark").on('click',function(event) {
			$('#modal_remark').modal({backdrop: 'static'});
		});

		$("#form_remark").submit(function(event) {
			gojax('post', '/production/sch/create/remark', {
	          txt_remark  : $('#txt_remark').val()
	        }).done(function(data) {
	        	if (data.result==200) {
	        		$('#modal_remark').modal('hide');
	        		$('#grid_remark').jqxGrid('updatebounddata','cells');
	        		alert(data.message);
	        	}else{
	        		alert(data.message);
	        		$('#grid_remark').jqxGrid('updatebounddata','cells');
	        	}
	        });
			
			return false;
		});

		$("#btn_deleteremark").on('click', function(event) {
			var rowdata = row_selected('#grid_remark');
    		if (typeof rowdata !== 'undefined') {
				if(confirm("Are You Sure?")){
					gojax('post', '/production/sch/delete/remark', {
			          id  : rowdata.ID,
			          problemid : rowdata.ProblemID
			        }).done(function(data) {
			        	if (data.result==200) {
			        		$('#grid_remark').jqxGrid('updatebounddata','cells');
			        		alert(data.message);
			        	}else{
			        		alert(data.message);
			        		$('#grid_remark').jqxGrid('updatebounddata','cells');
			        	}
			        });
				}
			}else{
				alert("Please select rows!");
			}
			return false;
		});

		function loadgridremark() {

		    var dataAdapter = new $.jqx.dataAdapter({
		    datatype: "json",
		    updaterow: function (rowid, rowdata, commit) {
		        gojax('post', '/production/sch/update/remark', {
					id 	 	: rowdata.ID,
					name	: rowdata.Description
		        }).done(function(data) {
		          if (data.result === 200) {
		          	$('#grid_remark').jqxGrid('updatebounddata');
		            commit(true);
		          }else{
		          	$('#grid_remark').jqxGrid('updatebounddata');
		          }
		        }).fail(function() {
		          	commit(false);
		        });
		    },
		    datafields: [
		      { name: "ID", type: "int" },
		      { name: "ProblemID", type: "string" },
		      { name: "Description", type: "string" }
	        ],
		      url : '/production/sch/load/remark'
		    });

		    return $("#grid_remark").jqxGrid({
		        width: '50%',
		        source: dataAdapter,
		        autoheight: true,
		        columnsresize: true,
		        pageable: true,
		        filterable: true,
		        showfilterrow: true,
		        pagesize: 15,
		        editable : true,
		      	columns: [
			       	{ text:"ProblemID", datafield: "ProblemID", align: 'center',width: '15%', editable: false},
			       	{ text:"ProblemName", datafield: "Description", align: 'center'}
				]
		    });

		}

	});
</script>