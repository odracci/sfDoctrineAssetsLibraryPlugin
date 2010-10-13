<?php use_javascript('/sfDoctrineAssetsLibraryPlugin/js/util') ?>
<?php use_helper('sfAsset') ?>

<?php if ($folder->isRoot()): ?>
<div class="form-row">
  <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/images.png', 'alt=images align=top') ?>
  <?php echo link_to(__('Mass upload', null, 'sfAsset'), '@sf_asset_library_mass_upload') ?>
</div>
<?php endif ?>

<form action="<?php echo url_for('@sf_asset_library_add_quick') ?>" method="post" enctype="multipart/form-data">
  <div class="form-row">
    <label for="new_file">
      <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/image_add.png', 'alt=add align=top') ?>
      <?php echo link_to(__('Upload a file here', null, 'sfAsset'), '@sf_asset_library_add_quick', array('class' => 'toggle', 'rel' => '{ div: \'input_new_file\'}')) ?>
    </label>
    <div class="content" id="input_new_file" style="display:none">
      <?php echo $fileform['file'] ?>
      <input type="submit" value="<?php echo __('Add', null, 'sfAsset') ?>" />
    </div>
  </div>
  <?php echo $fileform->renderHiddenFields() ?>
</form>

<form action="<?php echo url_for('@sf_asset_library_create_folder') ?>" method="post">
  <div class="form-row">
    <label for="new_directory">
      <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/folder_add.png', 'alt=add align=top') ?>
      <?php echo link_to(__('Add a subfolder', null, 'sfAsset'), '@sf_asset_library_create_folder', array('class' => 'toggle', 'rel' => '{ div: \'input_new_directory\'}')) ?>
    </label>
    <div class="content" id="input_new_directory" style="display:none">
      <?php echo $folderform['name'] ?>
      <input type="submit" value="<?php echo __('Create', null, 'sfAsset') ?>" />
    </div>
  </div>
  <?php echo $folderform->renderHiddenFields() ?>
</form>

<?php if (!$folder->isRoot()): ?>
<form action="<?php echo url_for('@sf_asset_library_rename_folder') ?>" method="post">
  <div class="form-row">
    <label for="new_folder">
      <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/folder_edit.png', 'alt=edit align=top') ?>
      <?php echo link_to(__('Rename folder', null, 'sfAsset'), '@sf_asset_library_rename_folder', array('class' => 'toggle', 'rel' => '{ div: \'input_new_name\'}')) ?>
    </label>
    <div class="content" id="input_new_name" style="display:none">
      <?php echo $renameform['name'] ?>
      <input type="submit" value="<?php echo __('Rename', null, 'sfAsset') ?>" />
    </div>
  </div>
  <?php echo $renameform->renderHiddenFields() ?>
</form>

<form action="<?php echo url_for('@sf_asset_library_move_folder') ?>" method="post">
  <div class="form-row">
    <label for="move_folder">
      <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/folder_go.png', 'alt=go align=top') ?>
      <?php echo link_to(__('Move folder', null, 'sfAsset'), '@sf_asset_library_move_folder', array('class' => 'toggle', 'rel' => '{ div: \'input_move_folder\'}')) ?>
    </label>
    <div class="content" id="input_move_folder" style="display:none">
      <?php echo $moveform['parent_folder'] ?>
      <input type="submit" value="<?php echo __('Move', null, 'sfAsset') ?>" />
    </div>
  </div>
  <?php echo $moveform->renderHiddenFields() ?>
</form>

<div class="form-row">
  <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/folder_delete.png', 'alt=delete align=top') ?>
  <?php echo link_to(__('Delete folder', null, 'sfAsset'), '@sf_asset_library_delete_folder?id=' . $folder->getId(), array(
    'method'  => 'delete',
    'confirm' => __('Are you sure?', null, 'sfAsset'),
  )) ?>
</div>
<?php endif ?>
