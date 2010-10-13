<?php use_stylesheet('/sf/sf_admin/css/main') ?>
<?php use_helper('I18N') ?>

<h1><?php echo __('Move folder', null, 'sfAsset') ?></h1>

<form action="<?php echo url_for('@sf_asset_library_move_folder') ?>" method="post">

  <fieldset id="sf_fieldset_none">

    <?php echo $form ?>

  </fieldset>

  <ul class="sf_admin_actions">
    <li class="sf_admin_action_save">
      <input type="submit" name="ok" class="sf_admin_action_ok" value="<?php echo __('Ok', null, 'sfAsset') ?>" />
    </li>
  </ul>

</form>
