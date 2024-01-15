<?php
$this->layout("layouts/base", ['title' => 'Report-Production-Scheduler']);
$PermissionService = new App\Services\PermissionService;
?>

<div class="head-space"></div>

<div class="panel panel-default" style="max-width: 500px; margin: auto;">
    <div class="panel-heading">Report Production-Scheduler</div>
    <div class="panel-body">
        <form id="sch_submit">
            <div class="form-group">
                <label for="date">Date</label>
                <input type="text" id="date_sch" name="date_sch" class=form-control required placeholder="เลือกวันที่..." / autocomplete="off">
                <input type="text" id="open_type" name="open_type" hidden />
            </div>
            <div class="form-group">
                <label for="shift">Shift</label>
                <select name="shift" id="shift" class="form-control" required>
                    <option value="1">(08:00-20:00)</option>
                    <option value="2">(20:00-08:00)</option>
                </select>
            </div>
            <!-- <button type="submit" class="btn btn-primary btn-lg btn-block"><span class="glyphicon glyphicon-print"></span> Print</button> -->
            <div class="btn-group btn-group-justified" role="group">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-lg" id="to_pdf"><span class="glyphicon glyphicon-print"></span> Print to PDF</button>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-lg" id="to_excel"><span class="glyphicon glyphicon-file"></span> Export to Excel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#date_sch").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        $('#to_pdf').on('click', function(event) {
            event.preventDefault();
            $('#open_type').val('pdf');
            $('#to_excel').attr("disabled", true);
            setTimeout(function() {
                $('#to_excel').attr("disabled", false);
            }, 10000);
            $('#sch_submit').submit();

        });

        $('#to_excel').on('click', function(event) {
            event.preventDefault();
            $('#open_type').val('excel');
            $('#to_pdf').attr("disabled", true);
            setTimeout(function() {
                $('#to_pdf').attr("disabled", false);
            }, 10000);
            $('#sch_submit').submit();

        });

        $('#sch_submit').submit(function(e) {
            e.preventDefault();

            var param_date = $('input[name=date_sch]').val();
            var param_shift = $('select[name=shift]').val();
            var open_type = $('#open_type').val();
            if (param_date === '') {
                alert('please select date !');
                $('#date_sch').focus();
                $('#to_pdf').attr("disabled", false);
                $('#to_excel').attr("disabled", false);
            } else {
                window.open('/production/sch/pdf/reportDraw/' + param_date + '/' + param_shift + '/' + open_type + '/view', '_blank');
            }
        });
    });
</script>