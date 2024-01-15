<?php 
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>

<style>
    input[type=radio] {
        width: 20px;
        height: 20px;
    }
    input[type=number] {
        width: 80px;
    }
</style>

<h4>Setup Scheduler Report</h4>


<div class="col-lg-6">
    
    <form id="form_fileter">
        <div class="form-inline">
            <div class="input-group" style="width: 200px;">
                <input type="text" id="date_sch" name="date_sch" class=form-control required  placeholder="Date" autocomplete="off"/>
                <span class="input-group-btn">
                    <button class="btn btn-info" id="date_sch_show" type="button">
                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    </button>
                </span>
            </div>
            <div class="form-group">
                <table style="font-size: 16px; font-weight:bold;">
                    <tr valign="middle">
                        <td style="padding: 10px;">
                            <input type="hidden" id="value_shift" value=1>
                            <input type="radio" id="shiftc" name="shift" value="1" checked> 08.00-20.00
                        </td>
                        <td style="padding: 10px;">
                            <input type="radio" id="shiftd" name="shift" value="2"> 20.00-08.00
                        </td>
                    </tr>
                </table>
            </div>
            <div class="form-group">
                <button type="button" id="btn_search" class="btn btn-info"><span class="glyphicon glyphicon-search"> </span> View</button>
            </div>
        </div>
    </form>
    <hr>
    <form id="form_sch">
        <table class="table table-bordered table-striped table-hover" width="100%">
            <thead>
                <th colspan="4">จำนวนพนักงาน</th>
            </thead>
            <tr>
                <td width="20%">หัวหน้าแผนก</td>
                <td width="15%"><input type="number" name="Senior" id="Senior"></td>
                <td width="20%">พน.เปลี่ยนแบลดเดอร์</td>
                <td width="15%"><input type="number" name="EmpBladder" id="EmpBladder"></td>
            </tr>
            <tr>
                <td width="20%">หัวหน้าหน่วย</td>
                <td width="15%"><input type="number" name="SectionHead"id="SectionHead"></td>
                <td width="20%">พน.เก็บยางหลังเตา</td>
                <td width="15%"><input type="number" name="EmpCuringBack"id="EmpCuringBack"></td>
            </tr>
            <tr>
                <td width="20%">Auditor/Tranner</td>
                <td width="15%"><input type="number" name="Auditor" id="Auditor"></td>
                <td width="20%">พน.ซ่อมยาง</td>
                <td width="15%"><input type="number" name="EmpMantain" id="EmpMantain"></td>
            </tr>
            <tr>
                <td width="20%">พน.อบยาง</td>
                <td width="15%"><input type="number" name="EmpCuring" id="EmpCuring"></td>
                <td width="20%">พน.ตัดหนวด/ปาดขอบ</td>
                <td width="15%"><input type="number" name="EmpCutting" id="EmpCutting"></td>
            </tr>
            <tr>
                <td width="20%">พน.จัดเก็บ/เข้าคลัง</td>
                <td width="15%"><input type="number" name="EmpWarehoure" id="EmpWarehoure"></td>
                <td width="20%">รวมพนักงานทั้งหมด</td>
                <td width="15%"><input type="number" id="SumEmpLine" readonly="true" style="background-color: #DCDCDC;"></td>
            </tr>

            <tr>
                <td width="20%">จํานวนพนักงานที่มาทํางาน</td>
                <td width="15%"><input type="number" name="EmpWorking" id="EmpWorking"></td>
                <td width="20%">จํานวนพนักงานลาพักร้อน</td>
                <td width="15%"><input type="number" name="EmpSummer" id="EmpSummer"></td>
            </tr>
            <tr>
                <td width="20%">จํานวนพนักงานลาป่วย</td>
                <td width="15%"><input type="number" name="EmpSeak" id="EmpSeak"></td>
                <td width="20%">จํานวนพนักงานลากิจ</td>
                <td width="15%"><input type="number" name="EmpLeave" id="EmpLeave"></td>
            </tr>
            <tr>
                <td width="20%">จํานวนพนักงานไม่แจ้ง</td>
                <td width="15%"><input type="number" name="EmpNoInfo" id="EmpNoInfo"></td>
                <td width="20%">จํานวนพนักงานลาทั้งหมด</td>
                <td width="15%"><input type="number" id="SumEmpOff" readonly="true" style="background-color: #DCDCDC;"></td>
            </tr>
            <tr>
                <td>หมายเหตุ</td>
                <td colspan="3">
                    <textarea rows="3" cols= 100 name="Remark" id="Remark"></textarea>
                </td>
            </tr>
            <!-- <thead>
                <th colspan="4">การผลิต</th>
            </thead>
            <tr>
                <td width="20%">จํานวนเตาอบยางที่เปิดอบ</td>
                <td width="15%"><input type="number" name=""></td>
                <td width="20%">จํานวนพิมพ์ที่อบ</td>
                <td width="15%"><input type="number" name=""></td>
            </tr>
            <tr>
                <td width="20%">เป้าหมายการผลิต</td>
                <td width="15%"><input type="number" name=""></td>
                <td width="20%">ผลิตได้</td>
                <td width="15%"><input type="number" name=""></td>
            </tr> -->
            <tr>
                <td colspan="4">
                    <input type="hidden" name="schdate_" id="schdate_">
                    <input type="hidden" name="shift_" id="shift_">
                    <button type="submit" class="btn btn-success">บันทึก</button>
                </td>
            </tr>
        </table>
    </form>
    
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        
        $("#date_sch").datepicker( {
		    format: 'dd-mm-yyyy',
		    autoclose: true,
	        todayHighlight: true,
		});
        
        $('#date_sch_show').click(function() {
          $('#date_sch').datepicker('show');
        });

        $("input[name=shift]").bind('click', function() {
            if ($("input[name=shift]:checked").val() == 1) {
                $("#value_shift").val(1);
            } else {
                $("#value_shift").val(2);
            }
        }); 
        
        $('#btn_search').on('click',function(){
            var date = $('#date_sch').val();
            var shift = $("#value_shift").val();
            $('#schdate_').val(date);
            $('#shift_').val(shift);

            $('#Senior').val(0);
            $('#SectionHead').val(0);
            $('#EmpBladder').val(0);
            $('#EmpCuringBack').val(0);
            $('#Auditor').val(0);
            $('#EmpMantain').val(0);
            $('#EmpCuring').val(0);
            $('#EmpCutting').val(0);
            $('#EmpWarehoure').val(0);
            $('#EmpWorking').val(0);
            $('#EmpSummer').val(0);
            $('#EmpSeak').val(0);
            $('#EmpLeave').val(0);
            $('#EmpNoInfo').val(0);
            $('#Remark').val('');
            $('#SumEmpLine').val(0);
            $('#SumEmpOff').val(0);

            gojax('get', '/production/sch/master/reportsch?date='+date+'&shift='+shift)
            .done(function(data) {
                $.each(data, function(index, val) {
                
                    $('#Senior').val(val.Senior);
                    $('#SectionHead').val(val.SectionHead);
                    $('#EmpBladder').val(val.EmpBladder);
                    $('#EmpCuringBack').val(val.EmpCuringBack);
                    $('#Auditor').val(val.Auditor);
                    $('#EmpMantain').val(val.EmpMantain);
                    $('#EmpCuring').val(val.EmpCuring);
                    $('#EmpCutting').val(val.EmpCutting);
                    $('#EmpWarehoure').val(val.EmpWarehoure);
                    $('#EmpWorking').val(val.EmpWorking);
                    $('#EmpSummer').val(val.EmpSummer);
                    $('#EmpSeak').val(val.EmpSeak);
                    $('#EmpLeave').val(val.EmpLeave);
                    $('#EmpNoInfo').val(val.EmpNoInfo);
                    $('#Remark').val(val.Remark);
                    // console.log(val.EmpWorking+"x"+val.EmpSummer+"x"+val.EmpSeak+"x"+val.EmpLeave+"x"+val.EmpNoInfo);

                    $('#SumEmpLine').val(val.Senior+val.SectionHead+val.EmpBladder+val.EmpCuringBack+val.Auditor+val.EmpMantain+val.EmpCuring+val.EmpCutting+val.EmpWarehoure);
                    $('#SumEmpOff').val(val.EmpSummer+val.EmpSeak+val.EmpLeave+val.EmpNoInfo);
                });
            });

            return false;
        });

        $('#form_sch').on('submit', function(event) {
            event.preventDefault();
                var date = $('#date_sch').val();
                var shift = $("#value_shift").val();

                $('#schdate_').val(date);
                $('#shift_').val(shift);

                $.ajax({
                    url : '/production/sch/master/sch',
                    type : 'post',
                    cache : false,
                    dataType : 'json',
                    data : $('form#form_sch').serialize()
                })
                .done(function(data) {
                    // console.log(data);
                    if (data.result === true) {
                        // $('#grid_gate').jqxGrid('updatebounddata');
                        // $('#modal_creat').modal('hide');
                        alert("บักทึกสำเร็จ");
                    }
                });
       
        });

    });
</script>