<?php throw new sfAssetException('should be unused...') ?>
<?php use_helper('JavascriptBase', 'I18N', 'sfAsset') ?>
<p><?php echo button_to_function(__('Back to the list', null, 'sfAsset'), 'history.back()') ?></p>

<script src="/js/tiny_mce/tiny_mce_popup.js"></script>
<script src="/js/tiny_mce/plugins/sfAssetsLibrary/jscripts/sfAssetsLibrary.js"></script>
<form action="" method="post" id="tinyMCE_insert_form">
  <fieldset>
    <?php echo asset_image_tag($sfAsset, 'large', array('class' => 'thumb')) ?>

    <div class="form-row">
      <label><?php echo __('Filename', null, 'sfAsset') ?></label>
      <div class=""><?php echo $sfAsset->getUrl() ?></div>
    </div>

    <?php echo $form ?>

    </fieldset>

    <ul class="sf_admin_actions">
      <li class="sf_admin_action_save">
        <?php echo button_to_function(__('Insert', null, 'sfAsset'),
         "insertAction(
           '".$sfAsset->getUrl()."',
           $('alt".$sfAsset->getId()."').value,
           $('border".$sfAsset->getId()."').checked,
           $('legend".$sfAsset->getId()."').checked,
           $('description".$sfAsset->getId()."').value,
           $('align".$sfAsset->getId()."').value,
           $('thumbnails".$sfAsset->getId()."').selectedIndex,
           $('width".$sfAsset->getId()."').value
          )",'class=sf_admin_action_save') ?>
      </li>
    </ul>
  </fieldset>
</form>
