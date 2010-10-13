<?php use_stylesheet('/sf/sf_admin/css/main') ?>
<?php use_helper('I18N') ?>

<h1><?php echo __('Rename folder', null, 'sfAsset') ?></h1>

<form action="<?php echo url_for('@sf_asset_library_rename_folder') ?>" method="post">

  <fieldset id="sf_fieldset_none">

    <?php echo $form ?>

  </fieldset>

  <?php include_partial('edit_actions') ?>

</form>