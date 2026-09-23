<style type="text/css">
@page {
	size: A4 landscape;
	margin: 4mm 6mm;
}
@media print {
	html, body {
		width: 100% !important;
		height: 100% !important;
		margin: 0 !important;
		padding: 0 !important;
		background: #fff !important;
		-webkit-print-color-adjust: exact !important;
		print-color-adjust: exact !important;
		overflow: hidden !important;
	}
	.hidden-print {
		display: none !important;
	}
	.invoice-summary ul.amounts li {
		padding: 2px 0 !important;
		border-bottom: #444444 1px solid;
	}
	.invoice table.table > tbody tr > td, 
	.invoice table.table > thead tr > th {
	    border-color: #444444 !important;
	    border-width: 1px !important;
	}
	.receipt-page-container {
		width: 100% !important;
		max-width: 100% !important;
		height: 198mm !important;
		max-height: 198mm !important;
		display: flex !important;
		flex-direction: row !important;
		margin: 0 !important;
		padding: 0 !important;
		page-break-inside: avoid !important;
		break-inside: avoid !important;
	}
	.receipt-slot {
		width: 50% !important;
		max-width: 50% !important;
		min-width: 50% !important;
		flex: 0 0 50% !important;
		float: left !important;
		box-sizing: border-box !important;
		padding: 0 8px !important;
		height: 100% !important;
		page-break-inside: avoid !important;
		break-inside: avoid !important;
	}
	.receipt-slot.empty-slot {
		visibility: hidden !important;
		border: none !important;
	}
	.receipt-has-two #slot_student_copy {
		border-right: 1px dashed #999 !important;
		padding-right: 14px !important;
	}
	.receipt-has-two #slot_office_copy {
		padding-left: 14px !important;
	}
	.invoice {
		min-height: 198mm !important;
		height: 100% !important;
		display: flex !important;
		flex-direction: column !important;
		justify-content: flex-start !important;
		page-break-inside: avoid !important;
		break-inside: avoid !important;
		border: 1px solid #333 !important;
		padding: 10px 14px !important;
		box-sizing: border-box !important;
	}
}

.receipt-page-container {
	width: 100%;
	max-width: 100%;
	min-height: 720px;
	display: flex;
	flex-direction: row;
	margin: 0;
	box-sizing: border-box;
}
.receipt-slot {
	width: 50%;
	max-width: 50%;
	min-width: 50%;
	flex: 0 0 50%;
	float: left;
	box-sizing: border-box;
	padding: 0 8px;
}
.receipt-slot.empty-slot {
	visibility: hidden;
}
.receipt-has-two #slot_student_copy {
	border-right: 1px dashed #bbb;
	padding-right: 14px;
}
.receipt-has-two #slot_office_copy {
	padding-left: 14px;
}
.receipt-action-bar {
	background: #f1f5f9;
	border: 1px solid #cbd5e1;
	border-radius: 6px;
	padding: 8px 14px;
	margin-bottom: 12px;
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.receipt-action-bar .btn-group .btn {
	padding: 4px 12px;
	font-size: 12px;
}
.invoice {
	min-height: 720px;
	height: 100%;
	display: flex;
	flex-direction: column;
	justify-content: flex-start;
	transform-origin: top center;
	box-sizing: border-box;
	padding: 10px 14px;
	background: #fff;
	border: 1px solid #cbd5e1;
	border-radius: 4px;
}
.invoice-body {
	flex: 0 0 auto;
}
.receipt-bottom-bar {
	margin-top: auto;
	padding-top: 24px;
}
.receipt-signatures {
	display: flex;
	justify-content: space-between;
	align-items: flex-end;
}
</style>
<?php
$record_array    = json_decode($record);
$currency_symbol = $global_config['currency_symbol'];
$basic           = $this->fees_model->getInvoiceBasic($studentID);

if (!isset($copyType) || empty($copyType)) {
	$copyType = 'both';
}

$count          = 1;
$total_fine     = 0;
$total_discount = 0;
$total_paid     = 0;
$total_balance  = 0;
$total_amount   = 0;
$payment_ids = array_filter(array_column($record_array, 'payment_id'));
if (!empty($payment_ids)) {
	$this->db->select('*');
	$this->db->where_in('id', $payment_ids);
	$this->db->from('fee_payment_history');
	$paymentHistory = $this->db->get()->result();
} else {
	$paymentHistory = array();
	foreach ($record_array as $rec) {
		$this->db->select('*');
		$this->db->from('fee_payment_history');
		if (isset($rec->feeType) && $rec->feeType == 'transport' && !empty($rec->trans_fd_id)) {
			$this->db->where('transport_fee_details_id', $rec->trans_fd_id);
		} elseif (!empty($rec->allocationID) && !empty($rec->feeTypeID)) {
			$this->db->where('allocation_id', $rec->allocationID);
			$this->db->where('type_id', $rec->feeTypeID);
		} else {
			continue;
		}
		$res = $this->db->get()->result();
		if (!empty($res)) {
			$paymentHistory = array_merge($paymentHistory, $res);
		}
	}
}
foreach ($paymentHistory as $row) {
	$total_paid     += $row->amount;
	$total_discount += $row->discount;
	$total_fine     += $row->fine;
}
$grand_paid = currencyFormat($total_paid + $total_fine);

$renderCopy = function($copy_title) use ($basic, $paymentHistory, $total_paid, $total_discount, $total_fine, $grand_paid) {
?>
	<div class="invoice">
		<div class="invoice-body">
			<!-- Header & Title -->
			<h4 class='text-center mb-none' style="font-weight: bold; font-size: 15px; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo $copy_title; ?></h4>
			
			<!-- School and Student Info -->
			<div class="bill-info" style="margin-top: 4px;">
				<div class="row">
					<div class="col-xs-12">
						<div class="bill-data" style="padding: 2px 0;">
							<div class="text-center mt-xs">
								<img src="<?=$this->application_model->getBranchImage($basic['branch_id'], 'printing-logo')?>" style="max-height: 52px;" alt="Logo" />
							</div>
							<address style="text-align: center; margin-top: 4px; margin-bottom: 4px; font-size: 11px; line-height: 1.35;">
								<?php
								echo '<strong style="font-size: 15px; display: block; margin-bottom: 2px;">' . $basic['school_name'] . '</strong>';
								echo $basic['school_address'] . '<br/>';
								echo $basic['school_mobileno'] . ' | ' . $basic['school_email'] . '<br/>';
								?>
							</address>
							<div class="row">
								<div class="invoice-summary text-left mt-xs" style="margin-left: 2px; margin-right: 2px;">
									<ul class="amounts" style="margin-bottom: 0;">
										<li><?php echo '<strong>' . translate('date') . ' :</strong> ' . _d(date('Y-m-d')); ?></li>
										<li><?php echo '<strong>' . translate('student_name') . ' :</strong> ' . $basic['first_name'] . ' ' . $basic['last_name'] ?></li>
										<li><?php echo '<strong>' . translate('register_no') . ' :</strong> ' . $basic['register_no'] ?></li>
										<li><?php echo '<strong>' . translate('class') . ' :</strong> ' . $basic['class_name'] . ' (' . $basic['section_name'] . ')'; ?></li>
										<li><?php echo '<strong>' . translate('father_name') . ' :</strong> ' . $basic['father_name'] ?></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Fees Type Items Table -->
			<div class="table-responsive br-none" style="margin-top: 8px;">
				<table class="table invoice-items table-hover mb-none" style="font-size: 11px;">
					<thead>
						<tr class="text-dark">
							<th id="cell-id" class="text-weight-semibold" style="padding: 5px 6px;">#</th>
							<th id="cell-item" class="text-weight-semibold" style="padding: 5px 6px;"><?= translate('fees_type') ?></th>
							<th id="cell-price" class="text-weight-semibold" style="padding: 5px 6px; text-align: right;"><?= translate('amount') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$item_count = 1;
						foreach ($paymentHistory as $key => $row) {
							$paid = $row->amount;
						?>
						<tr>
							<td style="padding: 4px 6px;"><?php echo $item_count++; ?></td>
							<td class="text-weight-semibold text-dark" style="padding: 4px 6px;"><?php
							if (empty($row->transport_fee_details_id)) {
								echo get_type_name_by_id('fees_type', $row->type_id);
							} else {
								$month = get_type_name_by_id('transport_fee_details', $row->transport_fee_details_id, 'month');
								$month = $this->app_lib->getMonthslist($month);
								echo translate('transport_fees') . " - $month";
							}
							?></td>
							<td style="padding: 4px 6px; text-align: right;"><?php echo currencyFormat($paid); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>

			<!-- Grand Total & Summary: DIRECTLY UNDER THE FEES TYPE TABLE -->
			<div class="invoice-summary text-right" style="margin-top: 6px;">
				<div class="row">
					<div class="col-xs-12">
						<ul class="amounts" style="float: right; min-width: 220px; font-size: 11px; margin-bottom: 0;">
							<li><strong><?= translate('sub_total') ?> :</strong> <?= currencyFormat($total_paid + $total_discount); ?></li>
							<li><strong><?= translate('discount') ?> :</strong> <?= currencyFormat($total_discount); ?></li>
							<li><strong><?= translate('paid') ?> :</strong> <?= currencyFormat($total_paid); ?></li>
							<li><strong><?= translate('fine') ?> :</strong> <?= currencyFormat($total_fine); ?></li>
							<li style="border-top: 1px solid #444; font-weight: bold; font-size: 12px; background: #f8fafc; padding: 4px 2px;">
								<strong><?= translate('total_paid') ?> (<?= translate('with_fine') ?>) : </strong> 
								<?php echo $grand_paid; ?>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Signatures and footer at the bottom of the card -->
		<div class="receipt-bottom-bar" style="margin-top: auto; padding-top: 24px;">
			<div class="receipt-signatures" style="display: flex; justify-content: space-between; font-size: 11px;">
				<div style="text-align: center; width: 140px; border-top: 1px dashed #666; padding-top: 4px;">
					<strong><?=translate('student')?> / <?=translate('parent')?></strong>
				</div>
				<div style="text-align: center; width: 140px; border-top: 1px dashed #666; padding-top: 4px;">
					<strong><?=translate('authorized_signatory')?></strong>
				</div>
			</div>

			<div class="text-center mt-xs" style="font-size: 10px; color: #666; margin-top: 8px;">
				Generated at <?php echo _d(date('Y-m-d')) . ', ' . date('h:i A'); ?> | Powered by KKWEBMART
			</div>
		</div>
	</div>
<?php }; ?>

<div class="receipt-action-bar hidden-print">
	<div>
		<strong style="margin-right: 8px; font-size: 12px; color: #1e293b;"><i class="fas fa-copy"></i> Print Copies:</strong>
		<div class="btn-group btn-group-sm">
			<button type="button" id="btnCopyBoth" class="btn btn-default <?=($copyType == 'both' ? 'active btn-primary' : '')?>" onclick="toggleReceiptCopy('both')">
				<i class="fas fa-columns"></i> Both Copies (Student & Office)
			</button>
			<button type="button" id="btnCopyStudent" class="btn btn-default <?=($copyType == 'student' ? 'active btn-primary' : '')?>" onclick="toggleReceiptCopy('student')">
				<i class="fas fa-user-graduate"></i> Student Copy Only
			</button>
			<button type="button" id="btnCopyOffice" class="btn btn-default <?=($copyType == 'office' ? 'active btn-primary' : '')?>" onclick="toggleReceiptCopy('office')">
				<i class="fas fa-building"></i> Office Copy Only
			</button>
		</div>
	</div>
	<div>
		<button type="button" class="btn btn-success btn-sm" onclick="window.print();" style="font-weight: 600;">
			<i class="fas fa-print"></i> Print Now
		</button>
	</div>
</div>

<div class="receipt-page-container row <?= ($copyType == 'both' ? 'receipt-has-two' : '') ?>" id="receiptContainer">
	<!-- Left Slot: Student Copy -->
	<div class="col-xs-6 receipt-slot" id="slot_student_copy" style="<?= ($copyType == 'office' ? 'display: none;' : '') ?>">
		<?php $renderCopy('Student Copy'); ?>
	</div>

	<!-- Right Slot: Office Copy -->
	<div class="col-xs-6 receipt-slot" id="slot_office_copy" style="<?= ($copyType == 'student' ? 'display: none;' : '') ?>">
		<?php $renderCopy('Office Copy'); ?>
	</div>

	<!-- Empty Blank Spacer Slot: Ensures the single copy stays exactly 50% width and never takes full page -->
	<div class="col-xs-6 receipt-slot empty-slot" id="slot_empty" style="<?= ($copyType == 'both' ? 'display: none;' : '') ?>">
		&nbsp;
	</div>
</div>

<script type="text/javascript">
function autoFitReceipt() {
	var cards = document.querySelectorAll('.receipt-slot:not(.empty-slot) .invoice');
	if (!cards || cards.length === 0) return;

	// Target height to match full A4 landscape page height (~725px)
	var targetA4Height = 725;

	// 1. Reset zoom and height first to accurately measure natural content height
	cards.forEach(function(card) {
		card.style.zoom = 1;
		card.style.transform = 'none';
		card.style.minHeight = 'auto';
		card.style.height = 'auto';
	});

	// 2. Measure max content height across active cards
	var maxContentHeight = 0;
	cards.forEach(function(card) {
		var h = card.scrollHeight || card.offsetHeight;
		if (h > maxContentHeight) {
			maxContentHeight = h;
		}
	});

	// 3. If content is longer than A4 height, scale down so it fits strictly on 1 page
	if (maxContentHeight > targetA4Height) {
		var scale = Math.floor((targetA4Height / maxContentHeight) * 98) / 100;
		if (scale < 0.45) scale = 0.45;

		cards.forEach(function(card) {
			if ('zoom' in document.body.style) {
				card.style.zoom = scale;
				card.style.minHeight = Math.floor(targetA4Height / scale) + 'px';
				card.style.height = '100%';
			} else {
				card.style.transform = 'scale(' + scale + ')';
				card.style.transformOrigin = 'top center';
				card.style.width = (100 / scale) + '%';
				card.style.minHeight = Math.floor(targetA4Height / scale) + 'px';
				card.style.height = '100%';
			}
		});
	} else {
		// Content is normal: match full A4 height so it doesn't look cut in half
		cards.forEach(function(card) {
			card.style.zoom = 1;
			card.style.transform = 'none';
			card.style.minHeight = targetA4Height + 'px';
			card.style.height = '100%';
		});
	}
}

function toggleReceiptCopy(type) {
	var container = document.getElementById('receiptContainer');
	var slotStudent = document.getElementById('slot_student_copy');
	var slotOffice = document.getElementById('slot_office_copy');
	var slotEmpty = document.getElementById('slot_empty');
	var btnBoth = document.getElementById('btnCopyBoth');
	var btnStudent = document.getElementById('btnCopyStudent');
	var btnOffice = document.getElementById('btnCopyOffice');

	if (!slotStudent || !slotOffice) return;

	if (btnBoth) btnBoth.className = 'btn btn-default';
	if (btnStudent) btnStudent.className = 'btn btn-default';
	if (btnOffice) btnOffice.className = 'btn btn-default';

	if (type === 'both') {
		if (btnBoth) btnBoth.className = 'btn btn-default active btn-primary';
		slotStudent.style.display = 'block';
		slotOffice.style.display = 'block';
		if (slotEmpty) slotEmpty.style.display = 'none';
		container.className = 'receipt-page-container row receipt-has-two';
	} else if (type === 'student') {
		if (btnStudent) btnStudent.className = 'btn btn-default active btn-primary';
		slotStudent.style.display = 'block';
		slotOffice.style.display = 'none';
		if (slotEmpty) slotEmpty.style.display = 'block';
		container.className = 'receipt-page-container row';
	} else if (type === 'office') {
		if (btnOffice) btnOffice.className = 'btn btn-default active btn-primary';
		slotStudent.style.display = 'none';
		slotOffice.style.display = 'block';
		if (slotEmpty) slotEmpty.style.display = 'block';
		container.className = 'receipt-page-container row';
	}

	setTimeout(autoFitReceipt, 50);
}

// Auto fit on load and before printing
autoFitReceipt();
window.addEventListener('DOMContentLoaded', autoFitReceipt);
window.addEventListener('load', autoFitReceipt);
window.addEventListener('resize', autoFitReceipt);
window.onbeforeprint = autoFitReceipt;
</script>
