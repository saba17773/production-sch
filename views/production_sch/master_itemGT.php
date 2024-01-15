<?php
$this->layout("layouts/base", ['title' => 'Production-Scheduler']);
?>
<button class="btn btn-info" id="btn_generate">
	<span class="glyphicon glyphicon-list-alt"></span> Edit
</button>
<h4>Item Master Greentire</h4>

<div id="grid_item"></div>
<div class="modal" id="modal_generate" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-remove"></span> close</button>
				<h5 class="modal-title">โปรดเลือกวันที่ต้องการข้อมูล</h5>
			</div>

			<div class="modal-body">
				<form id="form_generate">
					<table align="center">
						<tr>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 150px;">
									ITEM:<input type="text" id="itemGT" name="itemGT" class="form-control" required autocomplete="off" disabled />

								</div>
							</td>
						</tr>
						<tr>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 150px;">
									เวลาอบ:<input type="text" id="Timcure" name="Timcure" class="form-control" required autocomplete="off" />

								</div>
							</td>
						</tr>

						<tr>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 150px;">
									GROUP:<input type="text" id="GroupItem" name="GroupItem" class="form-control" required autocomplete="off" />

								</div>
							</td>
						</tr>
						<tr>
							<td style="padding: 5px;" align="center">
								<div class="input-group" style="width: 150px;">
									จำนวนพิมพ์ร่วม:<input type="text" id="counprint" name="counprint" class="form-control" required autocomplete="off" />
									<input type="hidden" id="FGItem" name="FGItem" class="form-control" required autocomplete="off" />
									<input type="hidden" id="Grouppass" name="Grouppass" class="form-control" required autocomplete="off" />

								</div>
							</td>
						</tr>

						<tr>
							<td>
								<hr>
							</td>
						</tr>

						<tr>
							<td style="padding: 10px;" align="center">
								<button class="btn btn-success btn-sm" id="btn_generate_all">
									<span class="glyphicon glyphicon-ok"></span> Save
								</button>
								<!-- <button class="btn btn-primary btn-sm" id="btn_generate_item">
									<span class="glyphicon glyphicon-ok"></span> Generate New
								</button> -->
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		loadgriditem();



		$('#btn_generate').on('click', function() {
			var rowdata = row_selected('#grid_item');

			if (typeof rowdata !== 'undefined') {
				$('input[name=itemGT]').val(rowdata.ItemGT);
				$('input[name=Timcure]').val(rowdata.Time);
				$('input[name=GroupItem]').val(rowdata.GroupId);
				$('input[name=counprint]').val(rowdata.Total);
				$('input[name=FGItem]').val(rowdata.ItemFG);
				$('input[name=Grouppass]').val(rowdata.GroupId);

				$('#modal_generate').modal({
					backdrop: 'static'
				});
				$('.modal-title').text(rowdata.ItemGT);
				grid_line(rowdata.InventJournalID);
			} else {
				alert("กรุณาเลือกรายการ");
			}
		});

		$('#btn_generate_all').on('click', function(event) {

			var itemGT = $('input[name=itemGT]').val();
			var Timcure = $('input[name=Timcure]').val();
			var GroupItem = $('input[name=GroupItem]').val();
			var counprint = $('input[name=counprint]').val();
			var FGItem = $('input[name=FGItem]').val();
			var Grouppass = $('input[name=Grouppass]').val();
			//alert(itemGT);
			saveitem(itemGT, Timcure, GroupItem, counprint, FGItem, Grouppass);
			//return false;
		});



	});

	function loadgriditem(boiler) {

		var dataAdapter = new $.jqx.dataAdapter({
			datatype: "json",
			updaterow: function(rowid, rowdata, commit) {
				gojax('post', '/production/sch/update/itemGT', {
					ID: rowdata.Id,
					Color: rowdata.ColorAll
					// Color2  : rowdata.Color2,
					// Color3	: rowdata.Color3,
					// Color4  : rowdata.Color4,
					// Color5	: rowdata.Color5
				}).done(function(data) {
					if (data.result === 200) {
						$('#grid_item').jqxGrid('updatebounddata');
						commit(true);
					} else {
						$('#grid_item').jqxGrid('updatebounddata');
					}
				}).fail(function() {
					commit(false);
				});

			},
			datafields: [{
					name: "Id",
					type: "string"
				},
				{
					name: "ItemFG",
					type: "string"
				},
				{
					name: "ItemGT",
					type: "string"
				},
				{
					name: "ItemGTName",
					type: "string"
				},
				{
					name: "IsBOMActive",
					type: "int"
				},
				{
					name: "PR",
					type: "string"
				},
				{
					name: "Pattern",
					type: "string"
				},
				{
					name: "ColorAll",
					type: "string"
				},
				{
					name: "Size",
					type: "string"
				},
				{
					name: "TypeTires",
					type: "string"
				},
				{
					name: "TypeTiresByRim",
					type: "string"
				},
				// { name: "Color4", type: "string" },
				// { name: "Color5", type: "string" },
				{
					name: "TT",
					type: "string"
				},
				{
					name: "Weight",
					type: "int"
				},
				{
					name: "Time",
					type: "int"
				},
				{
					name: "Total",
					type: "int"
				},
				{
					name: "GroupId",
					type: "int"
				}
			],
			url: '/production/sch/load/itemGT?boiler=' + boiler
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
			editable: true,
			columns: [{
					text: "Item FG",
					datafield: "ItemFG",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Item GT",
					datafield: "ItemGT",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Name TH",
					datafield: "ItemGTName",
					align: 'center',
					width: '320',
					editable: false
				},
				{
					text: "Active",
					datafield: "IsBOMActive",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "PR",
					datafield: "PR",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Pattern",
					datafield: "Pattern",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "TT",
					datafield: "TT",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Weight",
					datafield: "Weight",
					align: 'center',
					width: '100',
					editable: false
				},
				{
					text: "Color",
					datafield: "ColorAll",
					align: 'center',
					width: '200'
				},
				{
					text: "Size",
					datafield: "Size",
					align: 'center',
					width: '100',
					cellsformat: 'F1'
				},
				{
					text: "Type Tires",
					datafield: "TypeTires",
					align: 'center',
					width: '100'
				},
				{
					text: "Type Tires By Rim",
					datafield: "TypeTiresByRim",
					align: 'center',
					width: '100'
				},
				{
					text: "Cur/Shift",
					datafield: "Time",
					align: 'center',
					width: '100'
				},
				{
					text: "จำนวนพิมพ์ทั้งหมด",
					datafield: "Total",
					align: 'center',
					width: '100'
				},
				{
					text: "GROUP",
					datafield: "GroupId",
					align: 'center',
					width: '100'
				}
				// { text:"Color5", datafield: "Color5", align: 'center',width: '100'}
			]
		});

	}

	function saveitem(itemGT, Timcure, GroupItem, counprint, FGItem, Grouppass) {
		gojax('post', '/productionRecive/sch/gen/itemedit', {
			itemGT: itemGT,
			Timcure: Timcure,
			GroupItem: GroupItem,
			counprint: counprint,
			FGItem: FGItem,
			Grouppass: Grouppass

		}).done(function(data) {
			console.log(data);
			if (data.result == 200) {

				// loadgridsch(date_sch, shift);
				// $('#message_checkdata').hide();
				alert(data.message);
				$('#modal_generate').modal('hide');
				// $("#grid_sch").show();
				$('#grid_item').jqxGrid('updatebounddata');
				// $('#btn_generate_all').attr('disabled', false);
				// $('#btn_generate_item').attr('disabled', false);
			} else {
				alert(data.message);
				$('#modal_generate').modal('hide');
				// $('#modal_generate').modal('hide');
				// $('#grid_sch').jqxGrid('updatebounddata');
				// $('#btn_generate_all').attr('disabled', false);
				// $('#btn_generate_item').attr('disabled', false);
			}
		});
	}
</script>