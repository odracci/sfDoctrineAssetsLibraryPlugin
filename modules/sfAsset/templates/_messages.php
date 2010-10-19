<?php if ($sf_user->hasFlash('notice')): ?>
<div class="save-ok">
<h2><?php echo __($sf_user->getFlash('notice'), null, 'sfAsset') ?></h2>
</div>
<?php elseif ($sf_user->hasFlash('warning')): ?>
<div class="warning">
<h2><?php echo __($sf_user->getFlash('warning'), null, 'sfAsset') ?></h2>
</div>
<?php elseif ($sf_user->hasFlash('warning_message') && $sf_user->hasFlash('warning_params')): ?>
<div class="warning">
<?php $params =  $sf_user->getFlash('warning_params'); $params = $params instanceof sfOutputEscaper ? $params->getRawValue() : $params ?>
<h2><?php echo __($sf_user->getFlash('warning_message', null, ESC_RAW), $params, 'sfAsset') ?></h2>
</div>
<?php endif ?>
