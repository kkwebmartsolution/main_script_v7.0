<style type="text/css">
@media print {
	.invoice-summary ul.amounts li {
		padding: 2px 0 !important;
		border-bottom: #444444 1px solid;
	}
	.invoice table.table > tbody tr > td, 
	.invoice table.table > thead tr > th {
	    border-color: #444444 !important;
	    border-width: 1px !important;
	}
}
.invoice {
    padding: 15px;
    background: #fff;
    color: #333;
}
.invoice header {
    border-bottom: 2px solid #5b69bc;
    padding-bottom: 10px;
    margin-bottom: 15px;
}
.invoice table.table {
    margin-top: 15px;
    margin-bottom: 15px;
    width: 100%;
}
.invoice table.table th {
    background-color: #f8fafc;
    font-weight: 600;
}
.invoice-summary ul.amounts {
    list-style: none;
    margin: 0;
    padding: 0;
}
.invoice-summary ul.amounts li {
    padding: 4px 0;
    font-size: 13px;
}
.auth-signatory {
    border-top: 1px solid #333;
    display: inline-block;
    padding-top: 5px;
    min-width: 160px;
    text-align: center;
}
</style>
<?php
$total_paid = number_format($billdata['paid'], 2, '.', '');
$total_amount = number_format($billdata['total'], 2, '.', '');
$total_discount = number_format($billdata['discount'], 2, '.', '');
$currency = $global_config['currency'];
$currency_symbol = $global_config['currency_symbol'];
$due_amount = number_format($billdata['due'], 2, '.', '');
$status = $billdata['payment_status'];

$branchLogo = $this->application_model->getBranchImage($billdata['branch_id'], 'printing-logo');
if (empty($branchLogo)) {
    $branchLogo = base_url('uploads/app_image/printing-logo.png');
}
?>
<div class="invoice">
	<header class="clearfix">
		<div class="row">
			<div class="col-xs-6">
				<div class="ib">
					<img src="<?php echo $branchLogo; ?>" style="max-height: 60px;" alt="Logo" />
				</div>
			</div>
			<div class="col-xs-6 text-right">
				<h4 class="mt-none mb-none text-dark" style="font-weight:700;">Bill No #<?php echo html_escape($billdata['bill_no']); ?></h4>
				<p class="mb-none">
					<span class="text-dark"><?php echo translate('payment') . " " . translate('status'); ?> : </span>
					<strong>
					<?php 
						$payment_a = array(
							'1' => translate('unpaid'),
							'2' => translate('partly_paid'),
							'3' => translate('total_paid')
						);
						echo isset($payment_a[$status]) ? $payment_a[$status] : translate('unpaid');
					?>
					</strong>
				</p>
				<p class="mb-none">
					<span class="text-dark"><?php echo translate('date'); ?> : </span>
					<span class="value"><?php echo _d($billdata['date']); ?></span>
				</p>
			</div>
		</div>
	</header>

	<div class="bill-info">
		<div class="row">
			<div class="col-xs-6">
				<div class="bill-data">
					<p class="h5 mb-xs text-dark text-weight-semibold" style="font-weight:700; margin-bottom:4px;"><?php echo translate('sale_to'); ?> :</p>
					<address style="line-height:1.6;">
						<?php
						if (!empty($billdata['customer_name'])) {
							$custName = $billdata['customer_name'];
							$custRole = !empty($billdata['role_name']) ? $billdata['role_name'] : translate('customer');
							$custPhone = !empty($billdata['customer_phone']) ? $billdata['customer_phone'] : '';
							$custEmail = '';
						} else {
							$stuDetails = $this->application_model->getUserNameByRoleID($billdata['role_id'], $billdata['user_id']);
							$custName = !empty($stuDetails['name']) ? $stuDetails['name'] : 'Walk-in Customer';
							$custRole = !empty($billdata['role_name']) ? $billdata['role_name'] : translate('customer');
							$custPhone = !empty($stuDetails['mobileno']) ? $stuDetails['mobileno'] : '';
							$custEmail = !empty($stuDetails['email']) ? $stuDetails['email'] : '';
						}
						echo '<strong style="font-size:14px;">' . html_escape($custName) . '</strong><br>';
						if (!empty($billdata['role_id']) && $billdata['role_id'] != 0) {
							echo translate('roles') . " : " . html_escape($custRole) . '<br>';
						}
						if (!empty($custPhone)) {
							echo translate('mobile_no') . " : " . html_escape($custPhone) . '<br>';
						}
						if (!empty($custEmail)) {
							echo translate('email') . " : " . html_escape($custEmail) . '<br>';
						}
						?>
					</address>
				</div>
			</div>
			<div class="col-xs-6 text-right">
				<div class="bill-data">
					<p class="h5 mb-xs text-dark text-weight-semibold" style="font-weight:700; margin-bottom:4px;">From :</p>
					<address style="line-height:1.6;">
						<?php 
						echo '<strong>' . html_escape($global_config['institute_name']) . "</strong><br/>";
						echo html_escape($global_config['address']) . "<br/>";
						if (!empty($global_config['mobileno'])) {
							echo translate('mobile_no') . " : " . html_escape($global_config['mobileno']) . "<br/>";
						}
						if (!empty($global_config['institute_email'])) {
							echo translate('email') . " : " . html_escape($global_config['institute_email']) . "<br/>";
						}
						?>
					</address>
				</div>
			</div>
		</div>
	</div>

	<div class="table-responsive">
		<table class="table invoice-items table-hover mb-none" style="margin-top:10px;">
			<thead>
				<tr class="text-dark">
					<th style="width: 5%;">#</th>
					<th style="width: 45%;"><?php echo translate("product"); ?></th>
					<th style="width: 15%; text-align:right;"><?php echo translate("unit_price"); ?></th>
					<th style="width: 10%; text-align:center;"><?php echo translate("quantity"); ?></th>
					<th style="width: 10%; text-align:right;"><?php echo translate("discount"); ?></th>
					<th style="width: 15%; text-align:right;"><?php echo translate("sub_total"); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$count = 1;
					if (empty($productlist)) {
						$productlist = $this->inventory_model->get('sales_bill_details', array('sales_bill_id' => $billdata['id']));
					}
					foreach ($productlist as $product) {
						$sub_total = $product['sub_total'];
						$discount = $product['discount'];
				?>
				<tr>
					<td><?php echo $count++; ?></td>
					<td class="text-dark" style="font-weight:600;"><?php echo get_type_name_by_id('product', $product['product_id']); ?></td>
					<td style="text-align:right;"><?php echo currencyFormat($product['unit_price']); ?></td>
					<td style="text-align:center;"><?php echo html_escape($product['quantity']); ?></td>
					<td style="text-align:right;"><?php echo currencyFormat($discount); ?></td>
					<td style="text-align:right; font-weight:600;"><?php echo currencyFormat($sub_total - $discount); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<div class="invoice-summary text-right mt-md">
		<div class="row">
			<div class="col-xs-6 pull-right">
				<ul class="amounts">
					<li><?php echo translate("sub_total"); ?> : <?php echo currencyFormat($total_amount); ?></li>
					<?php if ($total_discount > 0): ?>
					<li><?php echo translate('discount'); ?> : <?php echo currencyFormat($total_discount); ?></li>
					<?php endif; ?>
					<?php 
					$net_amount = $total_amount - $total_discount;
					if ($status == 3): ?>
					<li>
						<strong><?php echo translate('grand_total'); ?> : </strong> 
						<strong><?php echo currencyFormat($net_amount); ?></strong>
					</li>
					<?php else: ?>
					<li>
						<strong><?php echo translate('grand_total'); ?> : </strong> 
						<strong><?php echo currencyFormat($net_amount); ?></strong>
					</li>
					<li><?php echo translate('paid_amount'); ?> : <?php echo currencyFormat($total_paid); ?></li>
					<li>
						<strong style="color:#dc2626;"><?php echo translate('due'); ?> : </strong> 
						<strong style="color:#dc2626;"><?php echo currencyFormat($due_amount); ?></strong>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="row" style="margin-top: 40px;">
		<div class="col-xs-6">
			<div class="text-left" style="font-size:12px; color:#64748b;">
				<?php echo translate('prepared_by') . " : " . (!empty($billdata['biller_name']) ? html_escape($billdata['biller_name']) : 'Admin'); ?>
			</div>
		</div>
		<div class="col-xs-6 text-right">
			<div class="auth-signatory" style="font-size:12px;">
				<?php echo translate('authorised_by'); ?>
			</div>
		</div>
	</div>
</div>
