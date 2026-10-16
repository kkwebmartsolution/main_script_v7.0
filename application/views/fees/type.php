<?php $currency_symbol = $global_config['currency_symbol']; ?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#list" data-toggle="tab"><i class="fas fa-list-ul"></i> <?php echo translate('fees_type') . " " . translate('list'); ?></a>
			</li>
<?php if (get_permission('fees_type', 'is_add')){ ?>
			<li>
				<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('add') . " " . translate('fees_type'); ?></a>
			</li>
<?php } ?>
<?php if (get_permission('fees_type', 'is_delete')): ?>
			<div class="pull-right" style="padding: 5px 10px 0 0;">
				<button class="btn btn-danger btn-circle" id="fees_type_bulk_delete" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
					<i class="fas fa-trash-alt"></i> <?=translate('bulk_delete')?>
				</button>
			</div>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div id="list" class="tab-pane active">
				<div class="mb-md">
					<div class="export_title">Fees Type List</div>
					<table class="table table-bordered table-hover table-condensed table-export">
						<thead>
							<tr>
							<?php if (get_permission('fees_type', 'is_delete')): ?>
								<th width="10" class="no-sort no-export">
									<div class="checkbox-replace">
										<label class="i-checks"><input type="checkbox" id="selectAllchkbox"><i></i></label>
									</div>
								</th>
							<?php endif; ?>
								<th width="50"><?php echo translate('sl'); ?></th>
							<?php if (is_superadmin_loggedin()): ?>
								<th><?=translate('branch')?></th>
							<?php endif; ?>
								<th><?=translate('name')?></th>
								<th><?=translate('fee_code')?></th>
								<th><?=translate('description')?></th>
								<th><?=translate('action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php $count = 1; foreach ($categorylist as $row): ?>
							<tr>
							<?php if (get_permission('fees_type', 'is_delete')): ?>
								<td class="checked-area" width="30px">
									<div class="checkbox-replace">
										<label class="i-checks">
											<input type="checkbox" class="cb_bulkdelete" id="<?=$row['id']?>"><i></i>
										</label>
									</div>
								</td>
							<?php endif; ?>
								<td><?php echo $count++; ?></td>
							<?php if (is_superadmin_loggedin()): ?>
								<td><?php echo get_type_name_by_id('branch', $row['branch_id']);?></td>
							<?php endif; ?>
								<td><?php echo $row['name']; ?></td>
								<td><?php echo $row['fee_code']; ?></td>
								<td><?php echo $row['description']; ?></td>
								<td>
									<?php if (get_permission('fees_type', 'is_edit')): ?>
										<a href="<?php echo base_url('fees/type_edit/' . $row['id']); ?>" class="btn btn-circle btn-default icon"
										data-toggle="tooltip" data-original-title="<?php echo translate('edit'); ?>"> 
											<i class="fas fa-pen-nib"></i>
										</a>
									<?php endif; if (get_permission('fees_type', 'is_delete')): ?>
										<?php echo btn_delete('fees/type_delete/' . $row['id']); ?>
									<?php endif; ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
<?php if (get_permission('fees_type', 'is_add')){ ?>
			<div class="tab-pane" id="create">
				<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered frm-submit')); ?>
				<input type="hidden" name="voucher_type" value="expense">
					<?php if (is_superadmin_loggedin() ): ?>
					<div class="form-group">
						<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('name'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="type_name" value="<?=set_value('type_name')?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('description'); ?></label>
						<div class="col-md-6 mb-md">
							<textarea class="form-control" id="description" name="description" placeholder="" rows="3" ></textarea>
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-2 col-md-offset-3">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
								</button>
							</div>
						</div>	
					</footer>
				<?php echo form_close(); ?>
			</div>
<?php } ?>
		</div>
	</div>
</section>

<?php if (get_permission('fees_type', 'is_delete')): ?>
<script type="text/javascript">
	$(document).ready(function () {
		// Toggle bulk delete button on tab change
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			if ($(e.target).attr('href') === '#list') {
				$('#fees_type_bulk_delete').parent().show();
			} else {
				$('#fees_type_bulk_delete').parent().hide();
			}
		});

		// Header select all checkbox sync across pages
		$('#selectAllchkbox').on('change', function() {
			var isChecked = $(this).is(':checked');
			if ($.fn.DataTable.isDataTable('.table-export')) {
				var table = $('.table-export').DataTable();
				table.$("input[type='checkbox'].cb_bulkdelete").prop('checked', isChecked);
			} else {
				$(".checked-area input[type='checkbox'].cb_bulkdelete").prop('checked', isChecked);
			}
		});

		// Row checkbox change listener to update header selectAllchkbox state
		$(document).on('change', '.cb_bulkdelete', function() {
			var total, checked;
			if ($.fn.DataTable.isDataTable('.table-export')) {
				var table = $('.table-export').DataTable();
				total = table.$("input[type='checkbox'].cb_bulkdelete").length;
				checked = table.$("input[type='checkbox'].cb_bulkdelete:checked").length;
			} else {
				total = $(".checked-area input[type='checkbox'].cb_bulkdelete").length;
				checked = $(".checked-area input[type='checkbox'].cb_bulkdelete:checked").length;
			}
			$('#selectAllchkbox').prop('checked', (total > 0 && total === checked));
		});

		// Bulk delete action handler
		$('#fees_type_bulk_delete').on('click', function() {
			var btn = $(this);
			var arrayID = [];
			if ($.fn.DataTable.isDataTable('.table-export')) {
				var table = $('.table-export').DataTable();
				table.$("input[type='checkbox'].cb_bulkdelete:checked").each(function () {
					arrayID.push($(this).attr('id'));
				});
			} else {
				$("input[type='checkbox'].cb_bulkdelete:checked").each(function () {
					arrayID.push($(this).attr('id'));
				});
			}

			if (arrayID.length === 0) {
				swal({
					title: "<?php echo translate('no_selection')?>",
					text: "<?php echo translate('please_select_at_least_one_item')?>",
					buttonsStyling: false,
					showCloseButton: true,
					focusConfirm: false,
					confirmButtonClass: "btn btn-default swal2-btn-default",
					type: "warning"
				});
				return;
			}

			swal({
				title: "<?php echo translate('are_you_sure')?>",
				text: "<?php echo translate('delete_this_information')?>",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn btn-default swal2-btn-default",
				cancelButtonClass: "btn btn-default swal2-btn-default",
				confirmButtonText: "<?php echo translate('yes_continue')?>",
				cancelButtonText: "<?php echo translate('cancel')?>",
				buttonsStyling: false,
				footer: "<?php echo translate('deleted_note')?>"
			}).then((result) => {
				if (result.value) {
					btn.button('loading');
					$.ajax({
						url: base_url + "fees/type_bulk_delete",
						type: "POST",
						dataType: "json",
						data: { array_id : arrayID },
						success:function(data) {
							swal({
								title: "<?php echo translate('deleted')?>",
								text: data.message,
								buttonsStyling: false,
								showCloseButton: true,
								focusConfirm: false,
								confirmButtonClass: "btn btn-default swal2-btn-default",
								type: data.status
							}).then((result) => {
								if (result.value) {
									location.reload();
								}
							});
						},
						error: function () {
							swal({
								title: "Error",
								text: "Failed to delete selected fee types. Please try again.",
								buttonsStyling: false,
								confirmButtonClass: "btn btn-default swal2-btn-default",
								type: "error"
							});
						},
						complete: function () {
							btn.button('reset');
						}
					});
				}
			});
		});
	});
</script>
<?php endif; ?>