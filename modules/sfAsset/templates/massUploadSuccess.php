<?php use_helper('I18N') ?>

<div id="sf_asset_container">

  <h1><?php echo __('Mass upload files', null, 'sfAsset') ?></h1>

  <?php include_partial('sfAsset/create_folder_header') ?>


  <form action="<?php echo url_for('@sf_asset_library_mass_upload') ?>" method="post" enctype="multipart/form-data">

    <?php echo $form->renderGlobalErrors() ?>

    <fieldset>
    <?php echo $form ?>
    </fieldset>
    <?php include_partial('edit_actions', array('button' => 'Upload')) ?>

  </form>

  <?php include_partial('sfAsset/create_folder_footer') ?>

</div>