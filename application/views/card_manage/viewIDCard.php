<style type="text/css">
	.certificate{
		box-sizing: border-box;
	<?php if (empty($template['background'])) { ?>
			background: #fff;
	<?php } else { ?>
		background-image: url("<?=base_url('uploads/certificate/' . $template['background'])?>");
		background-repeat: no-repeat !important;
		background-size: 100% 100% !important;

	<?php } ?>
		padding: <?=$template['top_space'] . 'px ' . $template['right_space'] . 'px ' . $template['bottom_space'] . 'px ' . $template['left_space'] . 'px'?>;
		font-family: Arial;
		width: <?=$template['layout_width']?>mm;
		height: <?=$template['layout_height']?>mm;
		margin: 0 auto;
	}
</style>
<div class="certificate">
	<?php
    $watermark = '';
    if (!empty($template['logo']) && is_file(FCPATH . 'uploads/certificate/' . basename($template['logo']))) {
        $watermark = '<img alt="" aria-hidden="true" src="' . base_url('uploads/certificate/' . basename($template['logo'])) . '" style="display:block;width:100%;height:100%;object-fit:contain;">';
    }
    echo str_replace('{watermark}', $watermark, $template['content']);
    ?>
</div>
