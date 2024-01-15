<?php $this->layout("layouts/base", ['title' => 'Greentire-Receive Page']); ?>

<div class="panel panel-default" style="margin-top: 10px;">
  <div class="panel-heading">Greentire-Receive</div>
  <div class="panel-body scroll-x">
    <div class="mb-2">
      <button class="btn btn-primary" id="btnImport"><i class="fa fa-upload"></i> Import Template</button>
      <button class="btn btn-warning" id="btnExport"><i class="fa fa-download"></i> Export Template</button>
      <button class="btn btn-success" id="btnView"><i class="fa fa-pencil"></i> View</button>
        <hr>
        <form action="/pd/greentire/import" method="post" enctype="multipart/form-data" id="submitImport" style="background-color: #b9caff;">
          <div class="form-group">
            <label><u>Import File Excel</u></label>
            <label>
              <p id="messageImport"></p>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <label for="">Date</label>
            <span style="padding-left: 30px;"> </span>
            <label class="radio-inline">
              <div class="row">
                <div class="input-group" style="width: 200px;">
                  <input type="text" id="ImportDate" name="ImportDate" class=form-control required  placeholder="เลือกวันที่..." autocomplete="off" required/>
                  <span class="input-group-btn">
                      <button class="btn btn-primary" id="ImportDateShow" type="button">
                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                      </button>
                  </span>
                </div>
              </div>
            </label>
            <span style="padding: 40px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ImportShift" id="ImportShift1" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;" checked required>
              <span style="padding-left: 10px;"><b>C</b></span>
            </label>
            <span style="padding: 10px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ImportShift" id="ImportShift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;" required>
              <span style="padding-left: 10px;"><b>D</b></span>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <label for="">Browse</label>
            <label class="radio-inline">
              <input type="file" name="ImportFile" id="ImportFile" class="form-control" required>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Import</button>
          </div>
        </form>

        <form action="/pd/greentire/export" method="get" id="submitExport" target="_blank" style="background-color: #fff59d;">
          <div class="form-group">
            <label><u>Export File Excel</u></label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <label for="">Date</label>
            <span style="padding-left: 30px;"> </span>
            <label class="radio-inline">
              <div class="row">
                <div class="input-group" style="width: 200px;">
                  <input type="text" id="ExportDate" name="ExportDate" class=form-control required  placeholder="เลือกวันที่..." autocomplete="off" required/>
                  <span class="input-group-btn">
                      <button class="btn btn-warning" id="ExportDateShow" type="button">
                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                      </button>
                  </span>
                </div>
              </div>
            </label>
            <span style="padding: 40px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ExportShift" id="ExportShift1" value="1" style="width: 1.5em; height: 1.5em; margin-top: -1px;" required>
              <span style="padding-left: 10px;"><b>C</b></span>
            </label>
            <span style="padding: 10px;"> </span>
            <label class="radio-inline">
              <input type="radio" name="ExportShift" id="ExportShift2" value="2" style="width: 1.5em; height: 1.5em; margin-top: -1px;" required>
              <span style="padding-left: 10px;"><b>D</b></span>
            </label>
          </div>

          <div class="form-group" style="margin-right: 10px;">
            <button type="submit" class="btn btn-warning"><i class="fa fa-download"></i> Export</button>
          </div>
        </form>
    </div>

  </div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		$('#submitImport').hide();
    	$('#submitExport').hide();

    	$("#btnImport").on("click", function() {
	      $('#submitImport').show();
	      $('#submitExport').hide();
	    });

		$("#btnExport").on("click", function() {
	      $('#submitExport').show();
	      $('#submitImport').hide();
	    });
	});
</script>