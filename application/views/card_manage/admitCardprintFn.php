<style type="text/css">
	@page {
		size: A4 landscape;
		margin: 3mm 4mm;
	}
	* {
		-webkit-print-color-adjust: exact !important;
		print-color-adjust: exact !important;
		box-sizing: border-box;
	}
	html, body {
		margin: 0;
		padding: 0;
		background: #fff;
		font-family: Arial, Helvetica, sans-serif;
	}

	/* Landscape A4 page container: 2 admit cards side-by-side */
	.admit-card-page {
		width: 290mm;
		height: 204mm;
		max-height: 204mm;
		margin: 0 auto;
		background: #fff;
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		align-items: stretch;
		position: relative;
		page-break-after: always;
		break-after: page;
		page-break-inside: avoid;
		break-inside: avoid;
		overflow: hidden;
		box-sizing: border-box;
	}
	.admit-card-page:last-child {
		page-break-after: avoid;
		break-after: avoid;
	}

	/* Middle vertical dashed cut line */
	.admit-card-cut-line {
		position: absolute;
		left: 50%;
		top: 2mm;
		bottom: 2mm;
		width: 0;
		border-left: 1px dashed #aaa;
		z-index: 10;
	}
	.admit-card-cut-line::before {
		content: "✂";
		position: absolute;
		top: -2px;
		left: -5px;
		font-size: 10px;
		color: #777;
		transform: rotate(90deg);
	}

	/* Slot for each admit card (left & right) */
	.admit-card-slot {
		width: calc(50% - 2mm);
		height: 100%;
		max-height: 204mm;
		position: relative;
		overflow: hidden;
		box-sizing: border-box;
		padding: 1mm;
	}
	.admit-card-slot.blank-slot {
		visibility: hidden;
	}

	.certificate {
		width: 100% !important;
		max-width: 100% !important;
		height: 100%;
		<?php if (empty($template['background'])) { ?>
			background: #fff;
			border: 1.5px solid #333;
		<?php } else { ?>
			background-image: url("<?=base_url('uploads/certificate/' . $template['background'])?>") !important;
			background-repeat: no-repeat !important;
			background-size: 100% 100% !important;
		<?php } ?>
		/* Padding tuned to ensure all content stays strictly inside the decorative border frame */
		padding: 42px 34px 34px 34px !important;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 10.5px;
		line-height: 1.3;
		border-radius: 4px;
		box-sizing: border-box !important;
		word-wrap: break-word;
		overflow-wrap: break-word;
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		overflow: hidden;
	}

	.admit-card-inner-content {
		width: 100%;
		display: flex;
		flex-direction: column;
		transform-origin: top center;
		box-sizing: border-box;
	}

	/* Typography normalization for clean layout inside border */
	.certificate h1, .certificate h2, .certificate h3, .certificate h4, .certificate h5, .certificate p {
		margin: 1px 0 !important;
	}
	.certificate div {
		box-sizing: border-box;
	}

	.certificate span[style*="font-size: 28px"],
	.certificate span[style*="font-size:28px"],
	.certificate span[style*="font-size: 24px"],
	.certificate span[style*="font-size:24px"] {
		font-size: 16px !important;
		letter-spacing: 0.5px;
	}
	.certificate span[style*="font-size: 18px"],
	.certificate span[style*="font-size:18px"] {
		font-size: 12.5px !important;
	}

	/* Inner tables formatting - strictly bounded inside border lines */
	.certificate table {
		width: 100% !important;
		max-width: 100% !important;
		table-layout: auto !important;
		margin: 4px 0 !important;
		border-collapse: collapse !important;
	}
	.certificate table td, .certificate table th {
		padding: 3px 5px !important;
		font-size: 10px !important;
		line-height: 1.25 !important;
		word-break: break-word !important;
	}
	.certificate table.table-bordered,
	.certificate table.table-bordered th,
	.certificate table.table-bordered td {
		border: 1px solid #666 !important;
	}

	/* Constrain images within cards */
	.certificate img {
		max-width: 100% !important;
		height: auto !important;
		display: inline-block;
		vertical-align: middle;
	}
	.certificate .student-photo, .certificate img[src*="student"] {
		max-height: 68px !important;
		width: auto !important;
		object-fit: cover !important;
	}
	.certificate .qr-code, .certificate img[src*="qr_code"] {
		max-height: 58px !important;
		max-width: 58px !important;
	}
	.certificate img[src*="uploads/certificate"] {
		max-height: 42px !important;
		width: auto !important;
	}
	.certificate hr {
		height: 0;
		border-bottom: 1px solid #ddd;
		margin: 5px 0 !important;
	}

	@media print {
		html, body {
			background: transparent !important;
		}
		.admit-card-page {
			margin: 0 !important;
			box-shadow: none !important;
		}
	}
</style>

<?php
if (!empty($user_array) && count($user_array)) {
	$chunks = array_chunk($user_array, 2);
	foreach ($chunks as $pair) {
?>
<div class="admit-card-page">
	<div class="admit-card-cut-line"></div>
	
	<!-- LEFT ADMIT CARD -->
	<div class="admit-card-slot">
		<div class="certificate">
			<div class="admit-card-inner-content">
				<?=$this->card_manage_model->admitCardTagsReplace($pair[0], $template, $print_date, $exam_id)?>
			</div>
		</div>
	</div>
	
	<!-- RIGHT ADMIT CARD (OR BLANK PLACEHOLDER) -->
	<?php if (isset($pair[1])) { ?>
	<div class="admit-card-slot">
		<div class="certificate">
			<div class="admit-card-inner-content">
				<?=$this->card_manage_model->admitCardTagsReplace($pair[1], $template, $print_date, $exam_id)?>
			</div>
		</div>
	</div>
	<?php } else { ?>
	<div class="admit-card-slot blank-slot"></div>
	<?php } ?>
</div>
<?php 
	}
} 
?>

<script type="text/javascript">
function autoFitAdmitCards() {
	var slots = document.querySelectorAll('.admit-card-slot:not(.blank-slot)');
	slots.forEach(function(slot) {
		var cert = slot.querySelector('.certificate');
		var content = slot.querySelector('.admit-card-inner-content');
		if (!cert || !content) return;
		
		content.style.zoom = '1';
		content.style.transform = 'none';
		content.style.width = '100%';
		
		var availableHeight = cert.clientHeight;
		var contentHeight = content.scrollHeight;
		
		if (availableHeight <= 0 || contentHeight <= 0) return;
		
		// Only scale down if content exceeds the available inner height of the border frame
		if (contentHeight > availableHeight) {
			var scale = Math.floor((availableHeight / contentHeight) * 98) / 100;
			if (scale < 0.45) scale = 0.45;
			
			if ('zoom' in document.body.style) {
				content.style.zoom = scale;
			} else {
				content.style.transform = 'scale(' + scale + ')';
				content.style.transformOrigin = 'top center';
				content.style.width = (100 / scale) + '%';
			}
		}
	});
}

autoFitAdmitCards();
window.addEventListener('DOMContentLoaded', autoFitAdmitCards);
window.addEventListener('load', autoFitAdmitCards);
window.addEventListener('resize', autoFitAdmitCards);
window.onbeforeprint = autoFitAdmitCards;

var prevOnload = window.onload;
window.onload = function() {
	autoFitAdmitCards();
	setTimeout(function() {
		if (typeof prevOnload === 'function') {
			prevOnload();
		} else {
			window.print();
		}
	}, 100);
};
</script>
