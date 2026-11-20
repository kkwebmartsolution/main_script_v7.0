<?php
$guardianFields = [
    'father_name' => ['father_name', 'father_name'],
    'grd_mobileno' => ['mobileno', 'mobile_no'],
    'grd_name' => ['name', 'name'],
    'grd_relation' => ['relation', 'relation'],
    'mother_name' => ['mother_name', 'mother_name'],
    'grd_occupation' => ['occupation', 'occupation'],
    'grd_income' => ['income', 'income'],
    'grd_education' => ['education', 'education'],
    'grd_email' => ['email', 'email'],
    'grd_city' => ['city', 'city'],
    'grd_state' => ['state', 'state'],
    'grd_address' => ['address', 'address'],
];
?>
<fieldset <?=$can_manage_guardian ? '' : 'disabled'?>>
    <div class="row">
        <?php foreach ($guardianFields as $field => $details):
            $required = in_array($field, ['father_name', 'grd_mobileno'], true);
            $value = set_value($field, isset($guardian[$details[0]]) ? $guardian[$details[0]] : '', false);
        ?>
        <div class="col-md-6 mb-md">
            <div class="form-group">
                <label class="control-label" for="profile-<?=$field?>"><?=translate($details[1])?> <?=$required ? '<span class="required">*</span>' : ''?></label>
                <input id="profile-<?=$field?>" name="<?=$field?>" type="<?=$field === 'grd_email' ? 'email' : ($field === 'grd_mobileno' ? 'tel' : 'text')?>" class="form-control" value="<?=html_escape($value)?>" <?=$required ? 'required' : ''?>>
                <span class="error"><?=form_error($field)?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="col-md-6 mb-md">
            <label class="control-label"><?=translate('guardian_picture')?></label>
            <input type="file" name="guardian_photo" class="dropify" data-default-file="<?=get_image_url('parent', !empty($guardian['photo']) ? $guardian['photo'] : 'defualt.png')?>">
            <span class="error"><?=form_error('guardian_photo')?></span>
        </div>
    </div>
</fieldset>
