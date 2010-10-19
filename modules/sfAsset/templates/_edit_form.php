<form action="<?php echo url_for('@sf_asset_library_update') ?>" method="post">

<fieldset id="sf_fieldset_none" class="">

  <div class="form-row">
    <label for="sf_asset_filepath"><?php echo __('Path:', null, 'sfAsset') ?></label>
    <div class="content">
    <?php if (!$sf_asset->isNew()): ?>
      <?php echo assets_library_breadcrumb($sf_asset->getRelativePath(), 0) ?>
    <?php endif ?>
    </div>
  </div>

</fieldset>

<fieldset id="sf_fieldset_meta" class="">
  <h2><?php echo __('Metadata', null, 'sfAsset') ?></h2>
  <?php echo $form ?>
</fieldset>

<?php include_partial('edit_actions', array('sf_asset' => $sf_asset)) ?>

</form>