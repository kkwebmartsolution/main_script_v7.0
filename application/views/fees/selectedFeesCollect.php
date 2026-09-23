<thead>
	<th><?= translate('fees_type') ?> <span class="required">*</span></th>
	<th><?= translate('date') ?> <span class="required">*</span></th>
	<th><?= translate('amount') ?> <span class="required">*</span></th>
	<th><?= translate('discount') ?> <span class="required">*</span></th>
	<th><?= translate('fine') ?> <span class="required">*</span></th>
	<th><?= translate('payment_method') ?> <span class="required">*</span></th>
<?php
$colspan = 7;
$links   = $this->fees_model->get('transactions_links', array('branch_id' => $branch_id), true);
if ($links['status'] == 1) {
	$colspan += 1;
	?>
	<th><?= translate('account') ?> <span class="required">*</span></th>
<?php } ?>
	<th><?= translate('remarks') ?></th>
</thead>
<tbody>
	<input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">
	<input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
<?php
$total_fine     = 0;
$total_discount = 0;
$total_paid     = 0;
$total_balance  = 0;
$total_amount   = 0;
$count          = 0;
foreach ($record_array as $key => $value) {
	if ($value->feeType == 'general') {
		$b       = $this->fees_model->getBalance($value->allocationID, $value->feeTypeID);
		$balance = $b['balance'];
		if ($balance != 0) {
			$count++;
			$fine        = $this->fees_model->feeFineCalculation($value->allocationID, $value->feeTypeID);
			$fine        = abs($fine - $b['fine']);
			$typeDetails = $this->db->select('name,fee_code')->where('id', $value->feeTypeID)->get('fees_type')->row();
			?>
	<tr>	
		<input type="hidden" name="collect_fees[<?php echo $key ?>][allocation_id]" value="<?php echo $value->allocationID; ?>">
		<input type="hidden" name="collect_fees[<?php echo $key ?>][type_id]" value="<?php echo $value->feeTypeID; ?>">
		<input type="hidden" name="collect_fees[<?php echo $key ?>][trans_fd_id]" value="0">
		<input type="hidden" name="collect_fees[<?php echo $key ?>][fee_type]" value="<?php echo $value->feeType; ?>">
		<td class="fee-modal">
			<p style="margin-bottom: 2px; margin-left:5px"><?php echo $typeDetails->name; ?></p>
			<span style="color: #606060; margin-left: 8px;">- <?php echo $typeDetails->fee_code; ?></span>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control datepicker" name="collect_fees[<?php echo $key ?>][date]" value="<?= date('Y-m-d') ?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control fee-calc-amount" name="collect_fees[<?php echo $key ?>][amount]" value="<?= number_format($balance, 2, '.', '') ?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control fee-calc-discount" name="collect_fees[<?php echo $key ?>][discount_amount]" value="0" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control fee-calc-fine" name="collect_fees[<?php echo $key ?>][fine_amount]" value="<?php echo number_format($fine, 2, '.', ''); ?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<?php
				$payvia_list = $this->app_lib->getSelectList('payment_types');
				echo form_dropdown("collect_fees[$key][pay_via]", $payvia_list, 1, "class='form-control selectTwo' data-width='100%'
					data-minimum-results-for-search='Infinity' ");
				?>
				<span class="error"></span>
			</div>
		</td>
    <?php if ($links['status'] == 1) { ?>
		<td class="fee-modal">
			<div class="form-group">
	            <?php
				$accounts_list = $this->app_lib->getSelectByBranch('accounts', $branch_id);
				echo form_dropdown("collect_fees[$key][account_id]", $accounts_list, $links['deposit'], "class='form-control selectTwo' data-width='100%'");
				?>
	            <span class="error"></span>
        	</div>
		</td>
    <?php } ?>
		<td class="fee-modal">
			<textarea name="collect_fees[<?php echo $key ?>][remarks]" rows="1" class="form-control" placeholder="<?= translate('write_your_remarks') ?>"></textarea>
		</td>
	</tr>
<?php
		}
	} else {
		$b       = $this->fees_model->getTransportBalance($value->trans_fd_id);
		$balance = $b['balance'];
		if ($balance != 0) {
			$count++;
			$fine = $this->fees_model->transportFeeFineCalculation($value->trans_fd_id);
			$fine = abs($fine - $b['fine']);

			$month = $this->db->select('month')->where('id', $value->trans_fd_id)->get('transport_fee_details')->row()->month;
			$month = $this->app_lib->getMonthslist($month);
?>
	<tr>	
		<input type="hidden" name="collect_fees[<?php echo $key ?>][allocation_id]" value="0">
		<input type="hidden" name="collect_fees[<?php echo $key ?>][type_id]" value="0">
		<input type="hidden" name="collect_fees[<?php echo $key ?>][trans_fd_id]" value="<?php echo $value->trans_fd_id; ?>">
		<input type="hidden" name="collect_fees[<?php echo $key ?>][fee_type]" value="<?php echo $value->feeType; ?>">
		<td class="fee-modal">
			<p style="margin-bottom: 2px; margin-left:5px"><?php echo translate('transport_fees') ?></p>
			<span style="color: #606060; margin-left: 8px;">- <?php echo $month; ?></span>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control datepicker" name="collect_fees[<?php echo $key ?>][date]" value="<?= date('Y-m-d') ?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control fee-calc-amount" name="collect_fees[<?php echo $key ?>][amount]" value="<?= number_format($balance, 2, '.', '') ?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control fee-calc-discount" name="collect_fees[<?php echo $key ?>][discount_amount]" value="0" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control fee-calc-fine" name="collect_fees[<?php echo $key ?>][fine_amount]" value="<?php echo number_format($fine, 2, '.', ''); ?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<?php
				$payvia_list = $this->app_lib->getSelectList('payment_types');
				echo form_dropdown("collect_fees[$key][pay_via]", $payvia_list, 1, "class='form-control selectTwo' data-width='100%' data-minimum-results-for-search='Infinity' ");
				?>
				<span class="error"></span>
			</div>
		</td>
    <?php if ($links['status'] == 1) { ?>
		<td class="fee-modal">
			<div class="form-group">
	            <?php
				$accounts_list = $this->app_lib->getSelectByBranch('accounts', $branch_id);
				echo form_dropdown("collect_fees[$key][account_id]", $accounts_list, $links['deposit'], "class='form-control selectTwo' data-width='100%'");
				?>
	            <span class="error"></span>
        	</div>
		</td>
    <?php } ?>
		<td class="fee-modal">
			<textarea name="collect_fees[<?php echo $key ?>][remarks]" rows="1" class="form-control" placeholder="<?= translate('write_your_remarks') ?>"></textarea>
		</td>
	</tr>
<?php
		}
	}
}
if ($count == 0) {
	echo '<tr><td colspan="' . $colspan . '"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
}
?>
</tbody>
<?php if ($count > 0) { ?>
<tfoot>
	<tr style="background: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1;">
		<td colspan="2" class="text-right" style="vertical-align: middle; font-size: 13px; font-weight: 700;">
			<?= translate('total') ?> (<?php echo $count; ?> <?= translate('items') ?>):
		</td>
		<td style="vertical-align: middle;">
			<span id="popup_sub_total" style="font-size: 14px; color: #0f172a; font-weight: 700;">0.00</span>
		</td>
		<td style="vertical-align: middle;">
			<span id="popup_row_discount" style="font-size: 13px; color: #dc2626; font-weight: 700;">0.00</span>
		</td>
		<td style="vertical-align: middle;">
			<span id="popup_total_fine" style="font-size: 13px; color: #d97706; font-weight: 700;">0.00</span>
		</td>
		<td colspan="<?php echo $colspan - 5; ?>"></td>
	</tr>
	<tr style="background: #f0f9ff; border-top: 1px dashed #bae6fd;">
		<td colspan="3" class="text-right" style="vertical-align: middle; font-size: 13px; font-weight: 700; color: #0369a1;">
			<?= translate('overall_discount') ?>:
		</td>
		<td colspan="2" style="vertical-align: middle;">
			<div class="input-group" style="width: 100%;">
				<span class="input-group-addon" style="background: #e0f2fe; color: #0369a1; font-weight: bold;">₹</span>
				<input type="number" step="any" min="0" class="form-control" id="overall_discount_input" value="0" placeholder="0.00" style="font-weight: bold; color: #0369a1; font-size: 14px;" />
			</div>
		</td>
		<td colspan="<?php echo $colspan - 5; ?>" style="vertical-align: middle;">
			<span class="text-muted" style="font-size: 11px;">(Auto-distributed proportionally across rows)</span>
		</td>
	</tr>
	<tr style="background: #0f172a; color: #ffffff;">
		<td colspan="3" class="text-right" style="vertical-align: middle; font-size: 14px; font-weight: 700;">
			Total Net Payable / Total Collection:
		</td>
		<td colspan="2" style="vertical-align: middle;">
			<span id="popup_net_payable" style="font-size: 17px; color: #38bdf8; font-weight: 800;">0.00</span>
		</td>
		<td colspan="<?php echo $colspan - 5; ?>"></td>
	</tr>
</tfoot>
<?php } ?>

<script type="text/javascript">
	$(function() {
		$(".adatepicker").datepicker({ 
			format: "yyyy-mm-dd",
			autoclose: true,
			orientation: "bottom",
			endDate: "today"
		});

		function calcPopupTotals() {
			var subTotal = 0;
			var totalDiscount = 0;
			var totalFine = 0;

			$(".fee-calc-amount").each(function() {
				var v = parseFloat($(this).val());
				if (!isNaN(v)) subTotal += v;
			});

			$(".fee-calc-discount").each(function() {
				var v = parseFloat($(this).val());
				if (!isNaN(v)) totalDiscount += v;
			});

			$(".fee-calc-fine").each(function() {
				var v = parseFloat($(this).val());
				if (!isNaN(v)) totalFine += v;
			});

			var netPayable = (subTotal + totalFine) - totalDiscount;
			if (netPayable < 0) netPayable = 0;

			$("#popup_sub_total").text(subTotal.toFixed(2));
			$("#popup_row_discount").text(totalDiscount.toFixed(2));
			$("#popup_total_fine").text(totalFine.toFixed(2));
			$("#popup_net_payable").text(netPayable.toFixed(2));
		}

		$(document).on("input change", ".fee-calc-amount, .fee-calc-discount, .fee-calc-fine", function() {
			calcPopupTotals();
		});

		$("#overall_discount_input").on("input change", function() {
			var overallDisc = parseFloat($(this).val());
			if (isNaN(overallDisc) || overallDisc < 0) overallDisc = 0;

			var $rows = $(".fee-calc-amount");
			var totalAmt = 0;
			$rows.each(function() {
				var v = parseFloat($(this).val());
				if (!isNaN(v)) totalAmt += v;
			});

			if (totalAmt > 0) {
				$rows.each(function() {
					var amt = parseFloat($(this).val());
					if (isNaN(amt)) amt = 0;
					var ratio = amt / totalAmt;
					var rowDisc = overallDisc * ratio;
					$(this).closest("tr").find(".fee-calc-discount").val(rowDisc.toFixed(2));
				});
			} else {
				var count = $rows.length;
				if (count > 0) {
					var splitDisc = overallDisc / count;
					$(".fee-calc-discount").val(splitDisc.toFixed(2));
				}
			}

			calcPopupTotals();
		});

		calcPopupTotals();
	});
</script>