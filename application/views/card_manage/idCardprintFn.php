<style type="text/css">
		@page {
			size: A4;
			margin: 8mm;
		}
		.certificate{
			box-sizing: border-box;
			width: <?=(float) $template['layout_width']?>mm;
			height: <?=(float) $template['layout_height']?>mm;
			break-inside: avoid;
			page-break-inside: avoid;
			<?php if (empty($template['background'])) { ?>
				background: #fff;
			<?php } else { ?>
				background-image: url("<?=base_url('uploads/certificate/' . $template['background'])?>");
				background-repeat: no-repeat !important;
				background-size: 100% 100% !important;
			<?php } ?>
			padding: <?=$template['top_space'] . 'px ' . $template['right_space'] . 'px ' . $template['bottom_space'] . 'px ' . $template['left_space'] . 'px'?>;
			font-family: Arial;
			float: left;
			margin: 3mm;
		}
		@media print {
			.certificate {
				width: <?=$template['layout_width']?>mm;
				height: <?=$template['layout_height']?>mm;
			<?php if (empty($template['background'])) { ?>
				background: #fff;
			<?php } else { ?>
				background-image: url("<?=base_url('uploads/certificate/' . $template['background'])?>") !important;
				background-repeat: no-repeat !important;
				background-size: 100% 100% !important;
			<?php } ?>
				-webkit-print-color-adjust: exact !important; 
				color-adjust: exact !important;
			}
			.certificate hr{
			    height: 0;
			    border-bottom: 1px solid #ddd;
			    margin: 10px 0 10px 0;
			}
		}
</style>
<?php
if (count($user_array)) {
	foreach ($user_array as $sc => $userID) {
	?>
<div class="certificate">
	<?=$this->card_manage_model->tagsReplace($user_type, $userID, $template, $print_date, $expiry_date)?>
</div>
<?php } } ?>
