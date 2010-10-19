<?php use_helper('I18N') ?>

<h1><?php echo __('Create a new folder', null, 'sfAsset') ?></h1>

<?php include_partial('sfAsset/create_folder_header') ?>

<form action="<?php echo url_for('@sf_asset_library_create_folder') ?>" method="post">
  <fieldset>
    <?php echo $form ?>
  </fieldset>

  <?php include_partial('edit_actions') ?>

</form>

<?php include_partial('sfAsset/create_folder_footer') ?>