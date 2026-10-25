<?php
$currency_symbol = $global_config['currency_symbol'];
$extINTL = extension_loaded('intl') && class_exists('NumberFormatter');
if ($extINTL == true) {
	$spellout = new NumberFormatter("en", NumberFormatter::SPELLOUT);
}
?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#invoice" data-toggle="tab"><i class="far fa-credit-card"></i> <?=translate('invoice')?></a>
			</li>
<?php if ($invoice['status'] != 'unpaid'): ?>
			<li>
				<a href="#history" data-toggle="tab"><i class="fas fa-dollar-sign"></i> <?=translate('payment_history')?></a>
			</li>
<?php endif; ?>
<?php if (get_permission('collect_fees', 'is_add') && $invoice['status'] != 'total'): ?>
			<li>
				<a href="#collect_fees" data-toggle="tab"><i class="fas fa-hand-holding-usd"></i> <?=translate('collect_fees')?></a>
			</li>
<?php endif; ?>
<?php if (get_permission('collect_fees', 'is_add') && $invoice['status'] != 'total'): ?>
			<li>
				<a href="#fully_paid" data-toggle="tab"><i class="far fa-credit-card"></i> Fully Paid</a>
			</li>
<?php endif; ?>
<?php if (get_permission('collect_fees', 'is_add')): ?>
			<li>
				<a href="#carry_fees" data-toggle="tab"><i class="fas fa-forward"></i> Carry Fees</a>
			</li>
			<li>
				<a href="#signature_upload" data-toggle="tab"><i class="fas fa-signature"></i> Signature Upload</a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
<?php if (get_permission('collect_fees', 'is_add')): ?>
<div id="signature_upload" class="tab-pane hidden-print">
	<h4>Authorised Signature</h4>
	<div class="panel-body">
		<?php echo form_open_multipart('fees/receipt_signature/' . (int) $basic['enroll_id']); ?>
		<?php $receiptSignature = $this->fees_model->getReceiptSignature($basic['branch_id']); ?>
		<?php if ($receiptSignature !== ''): ?>
			<img src="<?=html_escape($receiptSignature)?>" alt="Authorised signature" style="max-width: 180px; max-height: 60px; margin-bottom: 10px;">
		<?php endif; ?>
		<div class="form-group">
			<label for="receipt_signature">Upload / Replace Signature</label>
			<input type="file" name="receipt_signature" id="receipt_signature" accept="image/png,image/jpeg" required>
			<p class="help-block">PNG or JPG, maximum 2 MB and 2000 × 2000 pixels. This signature will appear on all fee receipts for this branch. A transparent PNG is recommended.</p>
		</div>
		<button type="submit" class="btn btn-primary">Save Signature</button>
		<?php echo form_close(); ?>
	</div>
</div>
<?php endif; ?>

			<div id="invoice" class="tab-pane <?=empty($this->session->flashdata('pay_tab')) ? 'active' : ''; ?>">
				<div id="invoice_print">
					<div class="invoice">
						<header class="clearfix">
							<div class="row">
								<div class="col-xs-6">
									<div class="ib">
										<img src="<?=$this->application_model->getBranchImage($basic['branch_id'], 'printing-logo')?>" alt="RamomCoder Img" />
									</div>
								</div>
								<div class="col-md-6 text-right">
									<h4 class="mt-none mb-none text-dark">Invoice No #<?=$invoice['invoice_no']?></h4>
									<p class="mb-none">
										<span class="text-dark"><?=translate('date')?> : </span>
										<span class="value"><?=_d(date('Y-m-d'))?></span>
									</p>
									<p class="mb-none">
										<span class="text-dark"><?=translate('status')?> : </span><?php
											$labelmode = '';
											if($invoice['status'] == 'unpaid') {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} elseif($invoice['status'] == 'partly') {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											} elseif($invoice['status'] == 'total') {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											}
											echo "<span class='value label " . $labelmode . " '>" . $status . "</span>";
										?>
									</p>
								</div>
							</div>
						</header>
						<div class="bill-info">
							<div class="row">
								<div class="col-xs-6">
									<div class="bill-data">
										<p class="h5 mb-xs text-dark text-weight-semibold">Invoice To :</p>
										<address>
											<?php 
											echo $basic['first_name'] . ' ' . $basic['last_name'] . '<br>';
											echo translate('register_no') . ' : ' . $basic['register_no'] . '<br>';
											echo (empty($basic['student_address']) ? "" : nl2br($basic['student_address']) . '<br>');
											echo translate('class') . ' : ' . $basic['class_name'] . " (" . $basic['section_name'] . ')<br>';
											if (!empty($basic['father_name'])) {
												echo translate('father_name') . ' : ' . $basic['father_name'];
											}
											?>
										</address>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="bill-data text-right">
										<p class="h5 mb-xs text-dark text-weight-semibold">Academic :</p>
										<address>
											<?php 
											echo $basic['school_name'] . "<br/>";
											echo $basic['school_address'] . "<br/>";
											echo $basic['school_mobileno'] . "<br/>";
											echo $basic['school_email'] . "<br/>";
											?>
										</address>
									</div>
								</div>
							</div>
						</div>
					<?php if (get_permission('collect_fees', 'is_add')) { ?>
						<button type="button" class="btn btn-default btn-sm mb-sm hidden-print mr-xs" id="collectFees" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-coins fa-fw"></i> Selected Fees Collect
						</button>
						<button type="button" class="btn btn-default btn-sm mb-sm hidden-print mr-xs" id="collectFeesSave" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-save fa-fw"></i> Save/Paid
						</button>
						<div class="btn-group mb-sm hidden-print mr-xs">
							<button type="button" class="btn btn-default btn-sm payReceiptPrintInv" data-copy-type="both" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
								<i class="fas fa-print"></i> Selected Pay Receipt
							</button>
							<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="inv" data-copy-type="both"><i class="fas fa-columns"></i> Both Copies (Student & Office)</a></li>
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="inv" data-copy-type="student"><i class="fas fa-user-graduate"></i> Student Copy Only</a></li>
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="inv" data-copy-type="office"><i class="fas fa-building"></i> Office Copy Only</a></li>
							</ul>
						</div>
					<?php } ?>
						<div class="table-responsive br-none">
							<table class="table invoice-items table-hover mb-none" id="invoiceSummary">
								<thead>
									<tr class="text-dark">
										<th id="cell-count" class="text-weight-semibold hidden-print">
											<div class="checkbox-replace" >
												<label class="i-checks" data-toggle="tooltip" data-original-title="Print Show / Hidden">
													<input type="checkbox" class="fee-selectAll"><i></i>
												</label>
											</div>
										</th>
										<th id="cell-count" class="text-weight-semibold hidden-print">#</th>
										<th id="cell-item" class="text-weight-semibold"><?=translate("fees_type")?></th>
										<th id="cell-id" class="text-weight-semibold"><?=translate("due_date")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("status")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("amount")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("discount")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("fine")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("paid")?></th>
										<th id="cell-total" class="text-center text-weight-semibold"><?=translate("balance")?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$group = array();
										$count = 1;
										$total_fine = 0;
										$fully_total_fine = 0;
										$total_discount = 0;
										$total_paid = 0;
										$total_balance = 0;
										$total_amount = 0;
										$typeData = array('' => translate('select'));
										$allocations = $this->fees_model->getInvoiceDetails($basic['enroll_id']);
										foreach ($allocations as $row) {
											$deposit = $this->fees_model->getStudentFeeDeposit($row['allocation_id'], $row['fee_type_id']);
											$type_discount = $deposit['total_discount'];
											$type_fine = $deposit['total_fine'];
											$type_amount = $deposit['total_amount'];
											$balance = $row['amount'] - ($type_amount + $type_discount);
											
											$total_discount += $type_discount;
											$total_fine += $type_fine;
											$total_paid += $type_amount;
											$total_balance += $balance;
											$total_amount += $row['amount'];
											if ($balance != 0) {
											 	$typeData[$row['allocation_id'] . "|" . $row['fee_type_id']] = $row['name'];
									            $fine = $this->fees_model->feeFineCalculation($row['allocation_id'], $row['fee_type_id']);
									            $b = $this->fees_model->getBalance($row['allocation_id'], $row['fee_type_id']);
									            $fine = abs($fine - $b['fine']);
									            $fully_total_fine += $fine;
											}
										?>
										<?php if(!in_array($row['group_id'], $group)) { 
											$group[] = $row['group_id'];
											?>
										<tr>
											<td class="group" colspan="10">
												<div class="checkbox-replace hidden-print" style="display: inline-block; vertical-align: middle; margin-right: 5px;">
													<label class="i-checks"><input type="checkbox" class="group-selectAll" data-group-id="<?php echo $row['group_id']; ?>"><i></i></label>
												</div>
												<strong><?php echo get_type_name_by_id('fee_groups', $row['group_id']) ?></strong><img class="group" src="<?php echo base_url('assets/images/arrow.png') ?>">
											</td>
										</tr>
									<?php } ?>
									<tr>
										<td class="hidden-print checked-area">
											<div class="checkbox-replace">
												<label class="i-checks"><input type="checkbox" name="cb_invoice" value="<?php echo $row['amount']; ?>" data-allocation-id="<?php echo $row['allocation_id'] ?>" data-fee-type-id="<?php echo $row['fee_type_id'] ?>" data-fee-type="general" data-transport-fd-id="0" data-group-id="<?php echo $row['group_id']; ?>"><i></i></label>
											</div>
										</td>
										<td class="hidden-print"><?php echo $count++;?></td>
										<td class="text-dark"><?=$row['name']?></td>
										<td><?=_d($row['due_date'])?></td>
										<td><?php 
											$status = 0;
											$labelmode = '';
											if($balance == 0) {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											} elseif($type_amount == 0 && $type_discount == 0) {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} else {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											}
											echo "<span class='label ".$labelmode." '>".$status."</span>";
										?></td>
										<td><?php echo currencyFormat($row['amount']);?></td>
										<td><?php echo currencyFormat($type_discount);?></td>
										<td><?php echo currencyFormat($type_fine);?></td>
										<td><?php echo currencyFormat($type_amount);?></td>
										<td class="text-center"><?php echo currencyFormat($balance);?></td>
									</tr>
<?php }
	if (!empty($transport_fees)) {
		?>
										<tr>
											<td class="group" colspan="10">
												<div class="checkbox-replace hidden-print" style="display: inline-block; vertical-align: middle; margin-right: 5px;">
													<label class="i-checks"><input type="checkbox" class="group-selectAll" data-group-id="transport"><i></i></label>
												</div>
												<strong> <?php echo translate('transport_fees') ?></strong><img class="group" src="<?php echo base_url('assets/images/arrow.png') ?>">
											</td>
										</tr>
<?php
	foreach ($transport_fees as $key => $value) {
		$deposit = $this->fees_model->getStudentTransportFeeDeposit($value->id);
		$type_discount = $deposit['total_discount'];
		$type_fine = $deposit['total_fine'];
		$type_amount = $deposit['total_amount'];

		$balance = $value->route_fare - ($type_amount + $type_discount);
		$month = $this->app_lib->getMonthslist($value->month);
		if ($balance != 0) {
			$fine = $this->fees_model->transportFeeFineCalculation($value->id);
			$fully_total_fine += $fine;
		 	$typeData["transport|" . $value->id . "|" . $value->month] = translate('transport_fees') . " - " . $month;
		}

		$total_discount += $type_discount;
		$total_fine += $type_fine;
		$total_paid += $type_amount;
		$total_balance += $balance;
		$total_amount += $value->route_fare;
?>
										<tr>
											<td class="hidden-print checked-area">
												<div class="checkbox-replace">
													<label class="i-checks"><input type="checkbox" name="cb_invoice" value="<?php echo $value->route_fare; ?>" data-allocation-id="0" data-fee-type-id="0" data-fee-type="transport" data-transport-fd-id="<?php echo $value->id; ?>" data-group-id="transport"><i></i></label>
												</div>
											</td>
											<td class="hidden-print"><?php echo $count++;?></td>
											<td class="text-dark"><?php echo $month ?></td>
											<td><?=_d($value->due_date)?></td>
											<td><?php 
												$status = 0;
												$labelmode = '';
												if($balance == 0) {
													$status = translate('total_paid');
													$labelmode = 'label-success-custom';
												} elseif($type_amount == 0 && $type_discount == 0) {
													$status = translate('unpaid');
													$labelmode = 'label-danger-custom';
												} else {
													$status = translate('partly_paid');
													$labelmode = 'label-info-custom';
												}
												echo "<span class='label ".$labelmode." '>".$status."</span>";
											?></td>
											<td><?php echo currencyFormat($value->route_fare);?></td>
											<td><?php echo currencyFormat($type_discount);?></td>
											<td><?php echo currencyFormat($type_fine);?></td>
											<td><?php echo currencyFormat($type_amount);?></td>
											<td class="text-center"><?php echo currencyFormat($balance);?></td>
										</tr>
<?php } } ?>
								</tbody>
							</table>
						</div>
						<div class="invoice-summary text-right mt-lg hidden-print">
							<div class="row">
								<div class="col-md-5 col-xs-12 pull-right">
									<ul class="amounts">
										<li><strong><?=translate('grand_total')?> :</strong> <?=currencyFormat($total_amount); ?></li>
										<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
										<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
										<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
										<?php if ($total_balance != 0): ?>
										<li><strong><?=translate('total_paid')?> (with fine) :</strong> <?=currencyFormat($total_paid + $total_fine); ?></li>
										<li>
											<strong><?=translate('balance')?> : </strong> 
											<?php
											$numberSPELL = "";
											if ($extINTL == true) {
												$numberSPELL = ' </br>( ' . ucwords($spellout->format(number_format($total_balance, 2, '.', ''))) . ' )';
											}
											echo currencyFormat($total_balance) . $numberSPELL;
											?>
										</li>
										<?php else: 
											$paidWithFine = ($total_paid + $total_fine);
											?>
										<li>
											<strong><?=translate('total_paid')?> (<?=translate('with_fine')?>) : </strong> 
											<?php
											$numberSPELL = "";
											if ($extINTL == true) {
												$numberSPELL = ' </br>( ' . ucwords($spellout->format(number_format($paidWithFine, 2, '.', ''))) . ' )';
											}
											echo currencyFormat($paidWithFine) . $numberSPELL;
											?>
										</li>
										<?php endif; ?>
									</ul>
								</div>
							</div>
						</div>
						<div class="invoice-summary text-right mt-lg visible-print-block" id="invDetailsPrint"></div>
					</div>
					<div class="text-right mr-lg hidden-print">
						<div class="btn-group mr-xs hidden-print">
							<button type="button" class="btn btn-default payReceiptPrintInv" data-copy-type="both" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
								<i class="fas fa-print"></i> Selected Pay Receipt
							</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu text-left">
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="inv" data-copy-type="both"><i class="fas fa-columns"></i> Both Copies (Student & Office)</a></li>
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="inv" data-copy-type="student"><i class="fas fa-user-graduate"></i> Student Copy Only</a></li>
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="inv" data-copy-type="office"><i class="fas fa-building"></i> Office Copy Only</a></li>
							</ul>
						</div>
						<button id="invoicePrint" class="btn btn-default ml-sm" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-print"></i> <?=translate('print')?></button>
					</div>
				</div>
			</div>
			<?php if ($invoice['status'] != 'unpaid'): ?>
			<div class="tab-pane" id="history">
				<div id="payment_print">
					<div class="invoice payment">
						<header class="clearfix">
							<div class="row">
								<div class="col-xs-6">
									<div class="ib">
										<img src="<?=$this->application_model->getBranchImage($basic['branch_id'], 'printing-logo')?>" alt="RamomCoder Img" />
									</div>
								</div>
								<div class="col-md-6 text-right">
									<h4 class="mt-none mb-none text-dark">Invoice No #<?php echo $invoice['invoice_no']?></h4>
									<p class="mb-none">
										<span class="text-dark"><?=translate('date')?> : </span>
										<span class="value"><?php echo _d(date('Y-m-d'));?></span>
									</p>
									<p class="mb-none">
										<span class="text-dark"><?=translate('status')?> : </span>
										<?php
											$labelmode = '';
											if($invoice['status'] == 'unpaid') {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} elseif($invoice['status'] == 'partly') {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											} elseif($invoice['status'] == 'total') {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											}
											echo "<span class='value label ".$labelmode." '>".$status."</span>";
										?>
									</p>
								</div>
							</div>
						</header>
						<div class="bill-info">
							<div class="row">
								<div class="col-xs-6">
									<div class="bill-data">
										<p class="h5 mb-xs text-dark text-weight-semibold">Invoice To :</p>
										<address>
											<?php 
											echo $basic['first_name'] . ' ' . $basic['last_name'] . '<br>';
											echo translate('register_no') . ' : ' . $basic['register_no'] . '<br>';
											echo (empty($basic['student_address']) ? "" : nl2br($basic['student_address']) . '<br>');
											echo translate('class') . ' : ' . $basic['class_name'] . " (" . $basic['section_name'] . ')<br>';
											if (!empty($basic['father_name'])) {
												echo translate('father_name') . ' : ' . $basic['father_name'];
											}
											?>
										</address>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="bill-data text-right">
										<p class="h5 mb-xs text-dark text-weight-semibold">Academic :</p>
										<address>
											<?php 
											echo $basic['school_name'] . "<br/>";
											echo $basic['school_address'] . "<br/>";
											echo $basic['school_mobileno'] . "<br/>";
											echo $basic['school_email'] . "<br/>";
											?>
										</address>
									</div>
								</div>
							</div>
						</div>
					<?php if (get_permission('fees_revert', 'is_delete')): ?>
						<button type="button" class="btn btn-default btn-sm mb-sm hidden-print" id="selected_revert" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-trash-restore-alt"></i> <?php echo translate('selected_revert'); ?>
						</button>
					<?php endif; ?>
						<div class="table-responsive">
							<table class="table invoice-items" id="paymentHistory">
								<thead>
									<tr class="h5 text-dark">
										<th id="cell-count" class="text-weight-semibold hidden-print">
											<div class="checkbox-replace" >
												<label class="i-checks" data-toggle="tooltip" data-original-title="Print Show / Hidden">
													<input type="checkbox" class="fee-selectAll"> <i></i>
												</label>
											</div>
										</th>
										<th id="cell-item" class="text-weight-semibold"><?=translate('fees_type')?></th>
										<th id="cell-item" class="text-weight-semibold"><?=translate('fees_code')?></th>
										<th id="cell-item" class="text-weight-semibold"><?=translate('date')?></th>
										<th id="cell-item" class="text-weight-semibold">Payment Id</th>
										<th id="cell-item" class="text-weight-semibold hidden-print"><?=translate('collect_by')?></th>
										<th id="cell-desc" class="text-weight-semibold"><?=translate('remarks')?></th>
										<th id="cell-qty" class="text-weight-semibold"><?=translate('method')?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate('amount')?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate('discount')?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate('fine')?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate('paid')?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$allocations = $this->db->where(array('student_id' => $basic['enroll_id'], 'session_id' => get_session_id()))->get('fee_allocation')->result_array();
									foreach ($allocations as $allRow) {
										$historys = $this->fees_model->getPaymentHistory($allRow['id'], $allRow['group_id']);
										foreach ($historys as $row) {
									?>
									<tr>
										<td class="hidden-print checked-area">
											<div class="checkbox-replace">
												<label class="i-checks"><input type="checkbox" name="cb_feePay" value="<?php echo $row['id']; ?>"><i></i></label>
											</div>
										</td>
										<td class="text-weight-semibold text-dark"><?php echo $row['name']; ?></td>
										<td><?php echo $row['fee_code']; ?></td>
										<td><?php echo _d($row['date']); ?></td>
										<td><?php echo $row['id']; ?></td>
										<td class="hidden-print">
											<?php
												if ($row['collect_by'] == 'online') {
													echo translate('online');
												}else{
													echo get_type_name_by_id('staff', $row['collect_by']);
												}
											?>
										</td>
										<td><?php echo $row['remarks']; ?></td>
										<td><?php echo $row['payvia']; ?></td>
										<td><?php echo currencyFormat($row['amount'] + $row['discount']); ?></td>
										<td><?php echo currencyFormat($row['discount']); ?></td>
										<td><?php echo currencyFormat($row['fine']); ?></td>
										<td><?php echo currencyFormat($row['amount']); ?></td>
									</tr>
									 <?php } } 
if (moduleIsEnabled('transport')) {
									$this->db->select('transport_fee_details.id,transport_fee_fine.month');
									$this->db->from('transport_fee_details');
									$this->db->join('transport_fee_fine', 'transport_fee_fine.id = transport_fee_details.transport_fee_fine_id', 'inner');
									$this->db->where('transport_fee_details.enroll_id', $basic['enroll_id']);
									$this->db->order_by('transport_fee_details.id', 'asc');
									$transport_fee_details =  $this->db->get()->result();
									foreach ($transport_fee_details as $trans) {
										$month = $this->app_lib->getMonthslist($trans->month);
										$historys = $this->fees_model->getTransportPaymentHistory($trans->id);
										foreach ($historys as $row) {
									?>
									<tr>
										<td class="hidden-print checked-area">
											<div class="checkbox-replace">
												<label class="i-checks"><input type="checkbox" name="cb_feePay" value="<?php echo $row['id']; ?>"><i></i></label>
											</div>
										</td>
										<td class="text-weight-semibold text-dark"><?php echo translate('transport_fees') ?></td>
										<td><?php echo strtolower($month); ?></td>
										<td><?php echo _d($row['date']); ?></td>
										<td><?php echo $row['id']; ?></td>
										<td class="hidden-print">
											<?php
												if ($row['collect_by'] == 'online') {
													echo translate('online');
												}else{
													echo get_type_name_by_id('staff', $row['collect_by']);
												}
											?>
										</td>
										<td><?php echo $row['remarks']; ?></td>
										<td><?php echo $row['payvia']; ?></td>
										<td><?php echo currencyFormat($row['amount'] + $row['discount']); ?></td>
										<td><?php echo currencyFormat($row['discount']); ?></td>
										<td><?php echo currencyFormat($row['fine']); ?></td>
										<td><?php echo currencyFormat($row['amount']); ?></td>
									</tr>
									 <?php } } ?>
<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="invoice-summary text-right mt-lg hidden-print">
							<div class="row">
								<div class="col-md-5 col-xs-12 pull-right">
									<ul class="amounts">
										<li><strong><?=translate('sub_total')?> :</strong> <?=currencyFormat($total_paid + $total_discount); ?></li>
										<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
										<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
										<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
										<li>
											<strong><?=translate('total_paid')?> (<?=translate('with_fine')?>) : </strong> 
											<?php
											$numberSPELL = "";
											$grand_paid = number_format($total_paid + $total_fine, 2, '.', '');
											if ($extINTL == true) {
												$numberSPELL = ' </br>( ' . ucwords($spellout->format($grand_paid)) . ' )';
											}
											echo currencyFormat($total_paid + $total_fine) . $numberSPELL;
											?>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="invoice-summary text-right mt-lg visible-print-block" id="invPaymentHistory"></div>
					</div>
					<div class="text-right mr-lg hidden-print">
						<div class="btn-group mr-xs hidden-print">
							<button id="payReceiptPrint" class="btn btn-default" data-copy-type="both" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
								<i class="fas fa-print"></i> Selected Pay Receipt
							</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu text-left">
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="history" data-copy-type="both"><i class="fas fa-columns"></i> Both Copies (Student & Office)</a></li>
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="history" data-copy-type="student"><i class="fas fa-user-graduate"></i> Student Copy Only</a></li>
								<li><a href="javascript:void(0);" class="btnReceiptPrintOption" data-target="history" data-copy-type="office"><i class="fas fa-building"></i> Office Copy Only</a></li>
							</ul>
						</div>
						<button id="paymentPrint" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-print"></i> <?=translate('print')?></button>
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- add fees form -->
			<?php if($invoice['status'] != 'total'): ?>
				<div id="collect_fees" class="tab-pane">
					<?php echo form_open('fees/fee_add', array('class' => 'form-horizontal frm-submit' )); ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('fees_type')?> <span class="required">*</span></label>
							<div class="col-md-6">
							<?php
								echo form_dropdown("fees_type", $typeData, set_value('fees_type'), "class='form-control' id='fees_type'
								data-plugin-selectTwo data-width='100%' ");
							?>
							<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" data-plugin-datepicker
								data-plugin-options='{"todayHighlight" : true, "endDate": "today"}' name="date" value="<?=date('Y-m-d')?>" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('amount')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="amount" id="feeAmount" value="" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('discount')?></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="discount_amount" value="0" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('fine')?></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="fine_amount" id="fineAmount" value="0" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('payment_method')?> <span class="required">*</span></label>
							<div class="col-md-6">
	    						<?php
	    							$payvia_list = $this->app_lib->getSelectList('payment_types');
	    							echo form_dropdown("pay_via", $payvia_list, set_value('pay_via', 1), "class='form-control' data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
	    						?>
								<span class="error"></span>
							</div>
						</div>
                        <?php
                        $links = $this->fees_model->get('transactions_links', array('branch_id' => $basic['branch_id']), true);
                        if ($links['status'] == 1) {
                        ?>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php echo translate('account'); ?> <span class="required">*</span></label>
                               	<div class="col-md-6">
                                <?php
                                    $accounts_list = $this->app_lib->getSelectByBranch('accounts', $basic['branch_id']);
                                    echo form_dropdown("account_id", $accounts_list, $links['deposit'], "class='form-control' id='account_id' required data-plugin-selectTwo data-width='100%'");
                                ?>
                            	</div>
                            </div>
                        <?php } ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('remarks')?></label>
							<div class="col-md-6 mb-md">
								<textarea name="remarks" rows="2" class="form-control" placeholder="<?=translate('write_your_remarks')?>"></textarea>
								<div class="checkbox-replace mt-lg">
									<label class="i-checks">
										<input type="checkbox" name="guardian_sms" checked> <i></i> Guardian Confirmation Sms
									</label>
								</div>
							</div>
						</div>
						<input type="hidden" name="branch_id" value="<?=$basic['branch_id']?>">
						<input type="hidden" name="student_id" value="<?=$basic['enroll_id']?>">
						<footer class="panel-footer">
							<div class="row">
								<div class="col-md-offset-3 col-md-3">
									<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
										<?=translate('fee_payment')?>
									</button>
								</div>
							</div>
						</footer>
					<?php echo form_close();?>
				</div>
			<?php endif; ?>
			<!--fully paid form-->
			<?php if($invoice['status'] != 'total'): ?>
				<div id="fully_paid" class="tab-pane">
					<?php echo form_open('fees/fee_fully_paid', array('class' => 'form-horizontal frm-submit' )); ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" data-plugin-datepicker
								data-plugin-options='{"todayHighlight" : true, "endDate":"today"}' name="date" value="<?=date('Y-m-d')?>" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('amount')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="amount" id="feeAmount" value="<?=number_format($total_balance, 2, '.', '')?>" autocomplete="off" disabled />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('fine')?></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="fine_amount" id="fineAmount" value="<?=number_format($fully_total_fine, 2, '.', '')?>" autocomplete="off" disabled />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('payment_method')?> <span class="required">*</span></label>
							<div class="col-md-6">
	    						<?php
	    							$payvia_list = $this->app_lib->getSelectList('payment_types');
	    							echo form_dropdown("pay_via", $payvia_list, set_value('pay_via'), "class='form-control' data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
	    						?>
								<span class="error"></span>
							</div>
						</div>
                        <?php
                        $links = $this->fees_model->get('transactions_links', array('branch_id' => $basic['branch_id']), true);
                        if ($links['status'] == 1) {
                        ?>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php echo translate('account'); ?> <span class="required">*</span></label>
                               	<div class="col-md-6">
                                <?php
                                    $accounts_list = $this->app_lib->getSelectByBranch('accounts', $basic['branch_id']);
                                    echo form_dropdown("account_id", $accounts_list, $links['deposit'], "class='form-control' id='account_id' required data-plugin-selectTwo data-width='100%'");
                                ?>
                            	</div>
                            </div>
                        <?php } ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('remarks')?></label>
							<div class="col-md-6 mb-md">
								<textarea name="remarks" rows="2" class="form-control" placeholder="<?=translate('write_your_remarks')?>"></textarea>
								<div class="checkbox-replace mt-lg">
									<label class="i-checks">
										<input type="checkbox" name="guardian_sms" checked> <i></i> Guardian Confirmation Sms
									</label>
								</div>
							</div>
						</div>
						<input type="hidden" name="invoice_id" value="<?php echo $basic['enroll_id']; ?>">
						<input type="hidden" name="branch_id" value="<?=$basic['branch_id']?>">
						<input type="hidden" name="student_id" value="<?=$basic['enroll_id']?>">
						<footer class="panel-footer">
							<div class="row">
								<div class="col-md-offset-3 col-md-3">
									<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
										<?=translate('fee_payment')?>
									</button>
								</div>
							</div>
						</footer>
					<?php echo form_close();?>
				</div>
			<?php endif; ?>
			<!-- Carry Fees Tab Pane -->
			<?php if (get_permission('collect_fees', 'is_add')): ?>
				<div id="carry_fees" class="tab-pane">
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<?php if (!empty($carry_allocation)): ?>
								<div class="alert alert-info">
									<strong><i class="fas fa-info-circle"></i> Current Carry Fees Status:</strong><br>
									<span>Total Allocated: <strong><?=currencyFormat($carry_allocation['prev_due'])?></strong></span> | 
									<span>Paid: <strong><?=currencyFormat($carry_allocation['paid'])?></strong></span> | 
									<span>Remaining Balance: <strong><?=currencyFormat($carry_allocation['balance'])?></strong></span><br>
									<small class="text-muted">Saving a new amount will update the allocated Carry Fees for this student.</small>
								</div>
							<?php endif; ?>

							<div class="panel panel-default mb-md" style="border: 1px solid #e2e8f0; border-radius: 4px;">
								<div class="panel-body">
									<h4 class="text-dark mt-none mb-sm"><i class="fas fa-history text-primary"></i> Previous Session(s) Due Summary</h4>
									<?php 
									$prev_total = isset($previous_due_info['total_due']) ? $previous_due_info['total_due'] : 0;
									?>
									<p class="mb-sm">
										<strong>Total Outstanding Dues from Previous Sessions:</strong> 
										<span class="text-danger text-weight-bold" style="font-size: 16px;"><?=currencyFormat($prev_total)?></span>
									</p>

									<?php if (!empty($previous_due_info['sessions'])): ?>
										<div class="table-responsive mt-sm mb-sm">
											<table class="table table-bordered table-condensed text-dark">
												<thead>
													<tr class="bg-primary text-white">
														<th>Session / School Year</th>
														<th>Class</th>
														<th>Section</th>
														<th class="text-right">Due Balance</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($previous_due_info['sessions'] as $sess_due): ?>
														<tr>
															<td><?=$sess_due['school_year']?></td>
															<td><?=$sess_due['class_name']?></td>
															<td><?=$sess_due['section_name']?></td>
															<td class="text-right"><?=currencyFormat($sess_due['balance'])?></td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									<?php else: ?>
										<p class="text-muted mb-xs"><small><i class="fas fa-info-circle"></i> No past session enrollment records found in the database. You can manually enter the Carry Fees amount below.</small></p>
									<?php endif; ?>

									<?php if ($prev_total > 0): ?>
										<button type="button" class="btn btn-default btn-xs" id="btnUsePrevDue" data-amount="<?=$prev_total?>">
											<i class="fas fa-copy"></i> Use Calculated Due (<?=currencyFormat($prev_total)?>)
										</button>
									<?php endif; ?>
								</div>
							</div>

							<?php echo form_open('fees/carry_fees_save', array('class' => 'form-horizontal frm-submit')); ?>
								<input type="hidden" name="student_id" value="<?=$basic['enroll_id']?>">

								<div class="form-group">
									<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
									<div class="col-md-8">
										<input type="text" class="form-control" data-plugin-datepicker
										data-plugin-options='{"todayHighlight" : true}' name="date" value="<?=empty($carry_allocation['due_date']) ? date('Y-m-d') : $carry_allocation['due_date']?>" autocomplete="off" />
										<span class="error"></span>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label"><?=translate('amount')?> <span class="required">*</span></label>
									<div class="col-md-8">
										<div class="input-group">
											<span class="input-group-addon"><?=$currency_symbol?></span>
											<input type="number" step="0.01" min="0.01" class="form-control" name="amount" id="carryFeeAmount" value="<?=!empty($carry_allocation['prev_due']) ? $carry_allocation['prev_due'] : ($prev_total > 0 ? $prev_total : '')?>" placeholder="Enter Carry Fees amount" autocomplete="off" />
										</div>
										<span class="error"></span>
										<small class="text-muted">This amount will be added specifically as 'Carry Fees' for <strong><?=$basic['first_name'] . ' ' . $basic['last_name']?></strong> only.</small>
									</div>
								</div>

								<footer class="panel-footer mt-lg">
									<div class="row">
										<div class="col-md-offset-3 col-md-8">
											<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
												<i class="fas fa-save"></i> Save Carry Fees
											</button>
										</div>
									</div>
								</footer>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<div class="zoom-anim-dialog modal-block mfp-hide modal-block-full" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-coins fa-fw"></i> <?=translate('collect_fees')?>
				<button type="button" class="close modal-dismiss" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</h4>
		</header>
		<?php echo form_open('fees/selectedFeesPay', array('class' => 'frm-submit' )); ?>
		<input type="hidden" name="print_now" id="print_now_input" value="0">
		<div class="panel-body">
			<div id="printResult" class="pt-sm pb-sm">
				<div class="table-responsive">						
					<table class="table table-bordered table-condensed text-dark" id="feeCollect">

					</table>
				</div>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-6 text-left" style="padding-top: 5px;">
					<span style="font-size: 12px; font-weight: bold; color: #475569; margin-right: 8px;"><i class="fas fa-copy"></i> Receipt:</span>
					<label class="radio-inline" style="font-size: 12px; font-weight: 600; cursor: pointer;">
						<input type="radio" name="modal_receipt_copy" value="both" checked> Both Copies
					</label>
					<label class="radio-inline" style="font-size: 12px; font-weight: 600; cursor: pointer;">
						<input type="radio" name="modal_receipt_copy" value="student"> Student Only
					</label>
					<label class="radio-inline" style="font-size: 12px; font-weight: 600; cursor: pointer;">
						<input type="radio" name="modal_receipt_copy" value="office"> Office Only
					</label>
				</div>
				<div class="col-md-6 text-right">
					<button type="submit" id="btnSubmitSavePaid" class="btn btn-default mr-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-save"></i> Save/Paid</button>
					<button type="submit" id="btnSubmitSavePrint" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-print"></i> Fee Payment</button>
				</div>
			</div>
		</footer>
		<?php echo form_close();?>
	</section>
</div>

<!-- Fee Revert SMS OTP Verification Modal -->
<div class="modal fade" id="revertOtpModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-sm" style="max-width: 440px; margin: 60px auto;">
		<div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
			<div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 22px;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="font-size: 26px; line-height: 1;">&times;</button>
				<h4 class="modal-title" style="font-weight: 700; color: #0f172a; display: flex; align-items: center; margin: 0; font-size: 17px;">
					<i class="fas fa-sms text-success" style="font-size: 24px; margin-right: 10px;"></i>
					SMS OTP Verification
				</h4>
			</div>
			<div class="modal-body" style="padding: 24px 22px; text-align: center;">
				<div class="alert alert-info text-left" style="font-size: 13px; line-height: 1.5; margin-bottom: 16px; border-radius: 8px; border-left: 4px solid #0284c7;">
					<i class="fas fa-shield-alt text-info" style="margin-right: 4px;"></i>
					A 6-digit verification OTP has been sent via SMS to Admin mobile: <strong id="otp_target_mobile" class="text-dark"></strong>. Please enter the OTP to authorize fee revert.
				</div>


				<div class="form-group" style="margin: 20px 0 10px 0;">
					<label class="control-label text-weight-bold" style="font-size: 13px; color: #334155; margin-bottom: 8px; display: block;">Enter 6-Digit Verification OTP</label>
					<input type="text" id="revert_otp_input" class="form-control text-center" maxlength="6" placeholder="• • • • • •" style="font-size: 26px; font-weight: 700; letter-spacing: 8px; height: 52px; border-radius: 8px; border: 2px solid #cbd5e1; box-shadow: inset 0 2px 4px rgba(0,0,0,0.03); max-width: 250px; margin: 0 auto;" autocomplete="off" inputmode="numeric">
					<div id="otp_error_msg" class="text-danger font-weight-semibold" style="display: none; font-size: 12px; margin-top: 8px;"></div>
				</div>

				<div style="font-size: 12px; color: #64748b; margin-top: 18px;">
					<span id="otp_timer_wrap">Didn't receive code? Resend in <strong id="otp_countdown" class="text-dark">60</strong>s</span>
					<a href="javascript:void(0);" id="btn_resend_otp" style="display: none; font-weight: 700;" class="text-primary"><i class="fas fa-redo-alt"></i> Resend OTP</a>
				</div>
			</div>
			<div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 22px;">
				<button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; font-weight: 600;"><?php echo translate('cancel'); ?></button>
				<button type="button" id="btn_verify_revert" class="btn btn-danger" style="border-radius: 6px; font-weight: 600; min-width: 140px;">
					<i class="fas fa-trash-restore-alt"></i> Verify & Revert
				</button>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	var branchID = "<?php echo $basic['branch_id']; ?>";
	var studentID = "<?php echo $basic['enroll_id']; ?>";
	$(".fee-selectAll").on("change", function(ev)
	{
		var $chcks = $(this).parents("table").find("tbody input[type='checkbox']");
		if($(this).is(':checked'))
		{
			$chcks.prop('checked', true).trigger('change');
		} else {
			$chcks.prop('checked', false).trigger('change');
		}
	});

	$(document).on("change", ".group-selectAll", function(ev)
	{
		var groupID = $(this).data("group-id");
		var $chcks = $(this).closest("table").find("tbody input[name='cb_invoice'][data-group-id='" + groupID + "']");
		if($(this).is(':checked'))
		{
			$chcks.prop('checked', true).trigger('change');
		} else {
			$chcks.prop('checked', false).trigger('change');
		}
	});

	$(document).on("change", "input[name='cb_invoice']", function()
	{
		var groupID = $(this).data("group-id");
		if (groupID) {
			var $table = $(this).closest("table");
			var $groupChcks = $table.find("tbody input[name='cb_invoice'][data-group-id='" + groupID + "']");
			var $groupSelectAll = $table.find(".group-selectAll[data-group-id='" + groupID + "']");
			var allGroupChecked = $groupChcks.length > 0 && $groupChcks.filter(':checked').length === $groupChcks.length;
			$groupSelectAll.prop('checked', allGroupChecked);
		}

		var $allInvoiceChcks = $(this).closest("table").find("tbody input[name='cb_invoice']");
		var $mainSelectAll = $(this).closest("table").find(".fee-selectAll");
		var allMainChecked = $allInvoiceChcks.length > 0 && $allInvoiceChcks.filter(':checked').length === $allInvoiceChcks.length;
		$mainSelectAll.prop('checked', allMainChecked);
	});

	$('#collectFees').on('click', function(e) {
		var $btn = $(this);
		$btn.button('loading');
		var arrayData = [];
		$("#invoiceSummary tbody input[name='cb_invoice']:checked").each(function() {
			var allocationID = $(this).data("allocation-id");
			var feeTypeID = $(this).data("fee-type-id");
			var feeAmount = $(this).val();
			var trans_fd_id = $(this).data("transport-fd-id");
			var feeType = $(this).data("fee-type");
            array = {};
            array ["feeAmount"] = feeAmount;
            array ["allocationID"] = allocationID;
            array ["feeTypeID"] = feeTypeID;
            array ["trans_fd_id"] = trans_fd_id;
            array ["feeType"] = feeType;
            arrayData.push(array);
		});
        if (arrayData.length === 0) {
            alert("No Rows Selected.");
            $btn.button('reset');
        } else {
            $.ajax({
                url: base_url + "fees/selectedFeesCollect",
                type: 'POST',
                data: {
                	'data': JSON.stringify(arrayData),
                	'branch_id': branchID,
                	'student_id' : studentID,
                },
                dataType: "html",
                cache: false,
                success: function (response) {
                    $("#feeCollect").html(response);
                    $("#dont_print_input").val(0);
                },
                complete: function () {
					$(".selectTwo").each(function() {
						var $this = $(this);
						$this.themePluginSelect2({});
					});
					$(".datepicker").each(function() {
						var $this = $(this);
						$this.themePluginDatePicker({
							"todayHighlight" : true,
							"endDate" : "today"
						});
					});
                	mfp_modal('#modal');
                	$btn.button('reset');
                }
            });
        }
	});

	$('#collectFeesSave').on('click', function(e) {
		var $btn = $(this);
		$btn.button('loading');
		var arrayData = [];
		$("#invoiceSummary tbody input[name='cb_invoice']:checked").each(function() {
			var allocationID = $(this).data("allocation-id");
			var feeTypeID = $(this).data("fee-type-id");
			var feeAmount = $(this).val();
			var trans_fd_id = $(this).data("transport-fd-id");
			var feeType = $(this).data("fee-type");
            array = {};
            array ["feeAmount"] = feeAmount;
            array ["allocationID"] = allocationID;
            array ["feeTypeID"] = feeTypeID;
            array ["trans_fd_id"] = trans_fd_id;
            array ["feeType"] = feeType;
            arrayData.push(array);
		});
        if (arrayData.length === 0) {
            alert("No Rows Selected.");
            $btn.button('reset');
        } else {
            $.ajax({
                url: base_url + "fees/selectedFeesCollect",
                type: 'POST',
                data: {
                	'data': JSON.stringify(arrayData),
                	'branch_id': branchID,
                	'student_id' : studentID,
                },
                dataType: "html",
                cache: false,
                success: function (response) {
                    $("#feeCollect").html(response);
                    $("#print_now_input").val(0);
                },
                complete: function () {
					$(".selectTwo").each(function() {
						var $this = $(this);
						$this.themePluginSelect2({});
					});
					$(".datepicker").each(function() {
						var $this = $(this);
						$this.themePluginDatePicker({
							"todayHighlight" : true,
							"endDate" : "today"
						});
					});
                	mfp_modal('#modal');
                	$btn.button('reset');
                }
            });
        }
	});

	$(document).on('click', '#btnSubmitSavePaid', function() {
		$("#print_now_input").val(0);
	});

	$(document).on('click', '#btnSubmitSavePrint', function() {
		$("#print_now_input").val(1);
	});

	$('#invoicePrint').on('click', function(e) {
		var $btn = $(this);
		$btn.button('loading');
		var arrayData = [];
		$("#invoiceSummary tbody input[name='cb_invoice']").each(function() {
			if($(this).is(':checked')) {
				var allocationID = $(this).data("allocation-id");
				var feeTypeID = $(this).data("fee-type-id");
				var feeAmount = $(this).val();
				var trans_fd_id = $(this).data("transport-fd-id");
				var feeType = $(this).data("fee-type");
	            array = {};
	            array ["feeAmount"] = feeAmount;
	            array ["allocationID"] = allocationID;
	            array ["feeTypeID"] = feeTypeID;
	            array ["trans_fd_id"] = trans_fd_id;
	            array ["feeType"] = feeType;
	            arrayData.push(array);
	            $(this).parents('tr').removeClass("hidden-print");
        	} else {
        		$(this).parents('tr').addClass("hidden-print");
        	}
		});
        if (arrayData.length === 0) {
            popupMsg("<?php echo translate('no_row_are_selected') ?>", "error");
            $btn.button('reset');
        } else {
        	$("#invDetailsPrint").html("");
            $.ajax({
                url: base_url + "fees/printFeesInvoice",
                type: 'POST',
                data: {'data': JSON.stringify(arrayData)},
                dataType: "html",
                cache: false,
                success: function (response) {
                    $("#invDetailsPrint").html(response);
                },
                complete: function () {
                	fn_printElem('invoice_print');
                	$btn.button('reset');
                }
            });
        }
	});

	$('#paymentPrint').on('click', function(e) {
		var $btn = $(this);
		$btn.button('loading');
		var arrayData = [];
		$("#paymentHistory tbody input[name='cb_feePay']").each(function() {
			if($(this).is(':checked')) {
				var paymentID = $(this).val();
	            array = {};
	            array ["payment_id"] = paymentID;
	            arrayData.push(array);
	            $(this).parents('tr').removeClass("hidden-print");
        	} else {
        		$(this).parents('tr').addClass("hidden-print");
        	}
		});
        if (arrayData.length === 0) {
            popupMsg("<?php echo translate('no_row_are_selected') ?>", "error");
            $btn.button('reset');
        } else {
        	$("#invPaymentHistory").html("");
            $.ajax({
                url: base_url + "fees/printFeesPaymentHistory",
                type: 'POST',
                data: {'data': JSON.stringify(arrayData)},
                dataType: "html",
                cache: false,
                success: function (response) {
                    $("#invPaymentHistory").html(response);
                },
                complete: function () {
                	fn_printElem('payment_print');
                	$btn.button('reset');
                }
            });
        }
	});

	function executePayReceiptPrint(source, copyType, $btn) {
		if ($btn) $btn.button('loading');
		var arrayData = [];
		if (source === 'history') {
			$("#paymentHistory tbody input[name='cb_feePay']:checked").each(function() {
				var allocationID = $(this).data("allocation-id");
				var feeTypeID = $(this).data("fee-type-id");
				var paymentID = $(this).val();
				var array = {};
				array["payment_id"] = paymentID;
				array["allocationID"] = allocationID;
				array["feeTypeID"] = feeTypeID;
				arrayData.push(array);
			});
		} else {
			$("#invoiceSummary tbody input[name='cb_invoice']:checked").each(function() {
				var allocationID = $(this).data("allocation-id");
				var feeTypeID = $(this).data("fee-type-id");
				var feeAmount = $(this).val();
				var trans_fd_id = $(this).data("transport-fd-id");
				var feeType = $(this).data("fee-type");
				var array = {};
				array["feeAmount"] = feeAmount;
				array["allocationID"] = allocationID;
				array["feeTypeID"] = feeTypeID;
				array["trans_fd_id"] = trans_fd_id;
				array["feeType"] = feeType;
				arrayData.push(array);
			});
		}

		if (arrayData.length === 0) {
			alert("No Rows Selected.");
			if ($btn) $btn.button('reset');
			return;
		}

		$("#invDetailsPrint").html("");
		$.ajax({
			url: base_url + "fees/payReceiptPrint",
			type: 'POST',
			data: {
				'student_id' : studentID,
				'data': JSON.stringify(arrayData),
				'copy_type': copyType || 'both'
			},
			dataType: "html",
			cache: false,
			success: function (response) {
				fn_printElem(response, true);
			},
			complete: function () {
				if ($btn) $btn.button('reset');
			}
		});
	}

	$('#payReceiptPrint').on('click', function(e) {
		var copyType = $(this).attr('data-copy-type') || $(this).data('copy-type') || $(this).data('copyType') || 'both';
		executePayReceiptPrint('history', copyType, $(this));
	});

	$('.payReceiptPrintInv').on('click', function(e) {
		var copyType = $(this).attr('data-copy-type') || $(this).data('copy-type') || $(this).data('copyType') || 'both';
		executePayReceiptPrint('inv', copyType, $(this));
	});

	$(document).on('click', '.btnReceiptPrintOption', function(e) {
		e.preventDefault();
		var target = $(this).attr('data-target') || $(this).data('target');
		var copyType = $(this).attr('data-copy-type') || $(this).data('copy-type') || $(this).data('copyType') || 'both';
		var $btn = target === 'history' ? $('#payReceiptPrint') : $(this).closest('.btn-group').find('.payReceiptPrintInv');
		if ($btn && $btn.length) {
			$btn.attr('data-copy-type', copyType);
		}
		executePayReceiptPrint(target, copyType, $btn);
	});

	var pendingRevertPaymentIDs = [];
	var otpCountdownInterval = null;

	function startOtpTimer(seconds) {
		clearInterval(otpCountdownInterval);
		var timeLeft = seconds || 60;
		$('#otp_timer_wrap').show();
		$('#btn_resend_otp').hide();
		$('#otp_countdown').text(timeLeft);

		otpCountdownInterval = setInterval(function() {
			timeLeft--;
			$('#otp_countdown').text(timeLeft);
			if (timeLeft <= 0) {
				clearInterval(otpCountdownInterval);
				$('#otp_timer_wrap').hide();
				$('#btn_resend_otp').show();
			}
		}, 1000);
	}

	function requestRevertOtp($btn) {
		if ($btn) {
			$btn.button('loading');
		}
		$('#otp_error_msg').hide().text('');

		$.ajax({
			url: base_url + 'fees/send_revert_otp',
			type: "POST",
			data: {
				'enroll_id': studentID,
				'payment_ids': pendingRevertPaymentIDs
			},
			dataType: "JSON",
			success: function(data) {
				if ($btn) {
					$btn.button('reset');
				}
				if (data.status === 'success') {
					$('#otp_target_mobile').text(data.masked_mobile ? data.masked_mobile : 'Admin');

					$('#revert_otp_input').val('');
					startOtpTimer(60);
					$('#revertOtpModal').modal('show');
					setTimeout(function() {
						$('#revert_otp_input').focus();
					}, 500);
				} else {
					swal({
						title: "SMS OTP Error",
						text: data.message || "Failed to send SMS verification OTP.",
						type: "error",
						confirmButtonClass: "btn btn-default swal2-btn-default",
						buttonsStyling: false
					});
				}
			},
			error: function(xhr, status, error) {
				if ($btn) {
					$btn.button('reset');
				}
				swal({
					title: "Request Error",
					text: "Failed to communicate with server: " + (error || status),
					type: "error",
					confirmButtonClass: "btn btn-default swal2-btn-default",
					buttonsStyling: false
				});
			}
		});
	}

	$('#selected_revert').on('click', function(e) {
		var $this = $(this);
		pendingRevertPaymentIDs = [];
		$("#paymentHistory tbody input[name='cb_feePay']:checked").each(function() {
			pendingRevertPaymentIDs.push($(this).val());
		});

		if (pendingRevertPaymentIDs.length === 0) {
			swal({
				title: "No Payments Selected",
				text: "Please select at least one fee payment record from the payment history table to revert.",
				type: "warning",
				confirmButtonClass: "btn btn-default swal2-btn-default",
				buttonsStyling: false
			});
			return;
		}

		swal({
			title: "<?php echo translate('are_you_sure')?>",
			text: "An SMS OTP will be sent to the School Admin mobile to authorize reverting " + pendingRevertPaymentIDs.length + " payment record(s).",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn btn-danger swal2-btn-default",
			cancelButtonClass: "btn btn-default swal2-btn-default",
			confirmButtonText: "<i class='fas fa-sms'></i> Send SMS OTP",
			cancelButtonText: "<?php echo translate('cancel')?>",
			buttonsStyling: false,
			footer: "<?php echo translate('deleted_note')?>"
		}).then((result) => {
			if (result.value) {
				requestRevertOtp($this);
			}
		});
	});

	$('#btn_resend_otp').on('click', function(e) {
		e.preventDefault();
		var $btn = $(this);
		$btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...');
		$.ajax({
			url: base_url + 'fees/send_revert_otp',
			type: "POST",
			data: {
				'enroll_id': studentID,
				'payment_ids': pendingRevertPaymentIDs
			},
			dataType: "JSON",
			success: function(data) {
				$btn.html('<i class="fas fa-redo-alt"></i> Resend OTP');
				if (data.status === 'success') {
					if (data.masked_mobile) {
						$('#otp_target_mobile').text(data.masked_mobile);
					}

					$('#otp_error_msg').hide().text('');
					$('#revert_otp_input').val('').focus();
					startOtpTimer(60);
				} else {
					$('#otp_error_msg').text(data.message || 'Failed to resend OTP.').show();
				}
			},
			error: function() {
				$btn.html('<i class="fas fa-redo-alt"></i> Resend OTP');
				$('#otp_error_msg').text('Server communication error. Please try again.').show();
			}
		});
	});

	$('#btn_verify_revert').on('click', function(e) {
		var otpVal = $('#revert_otp_input').val().trim();
		if (!otpVal || otpVal.length !== 6 || !/^\d+$/.test(otpVal)) {
			$('#otp_error_msg').text('Please enter a valid 6-digit numeric OTP code.').show();
			$('#revert_otp_input').focus();
			return;
		}

		$('#otp_error_msg').hide().text('');
		var $btn = $(this);
		var originalText = $btn.html();
		$btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verifying...');

		$.ajax({
			url: base_url + 'fees/paymentRevert',
			type: "POST",
			data: {
				'id': pendingRevertPaymentIDs,
				'enroll_id': studentID,
				'otp': otpVal
			},
			dataType: "JSON",
			success: function(data) {
				$btn.prop('disabled', false).html(originalText);
				if (data.status === 'success') {
					$('#revertOtpModal').modal('hide');
					clearInterval(otpCountdownInterval);
					swal({
						title: "<?php echo translate('deleted')?>",
						text: data.message || "Fee payment(s) successfully reverted after OTP verification.",
						buttonsStyling: false,
						showCloseButton: true,
						focusConfirm: false,
						confirmButtonClass: "btn btn-default swal2-btn-default",
						type: "success"
					}).then((result) => {
						location.reload();
					});
				} else {
					$('#otp_error_msg').text(data.message || 'Invalid or expired OTP.').show();
					$('#revert_otp_input').select().focus();
				}
			},
			error: function(xhr, status, error) {
				$btn.prop('disabled', false).html(originalText);
				$('#otp_error_msg').text('Server communication error: ' + (error || status)).show();
			}
		});
	});

	$('#revert_otp_input').on('keypress', function(e) {
		if (e.which === 13) {
			$('#btn_verify_revert').trigger('click');
		}
	});



    $('#fees_type').on("change", function(){
        var typeID = $(this).val();
	    $.ajax({
	        url: base_url + 'fees/getBalanceByType',
	        type: 'POST',
	        data: {
	        	'typeID': typeID
	        },
	        dataType: "json",
	        success: function (data) {
	            $('#feeAmount').val(data.balance.toFixed(2));
	            $('#fineAmount').val(data.fine.toFixed(2));
	        }
	    });
    });

	$('#btnUsePrevDue').on('click', function() {
		var amt = $(this).data('amount');
		if (amt > 0) {
			$('#carryFeeAmount').val(amt);
		}
	});
</script>
