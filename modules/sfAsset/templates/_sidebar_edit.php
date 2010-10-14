<?php use_javascript('/sfDoctrineAssetsLibraryPlugin/js/util') ?>
<?php use_helper('sfAsset') ?>

<?php if (!$sf_asset->isNew()): ?>

<div id="thumbnail">
  <a href="<?php echo $sf_asset->getUrl('full') ?>"><?php echo asset_image_tag($sf_asset, 'large', array('title' => __('See full-size version', null, 'sfAsset')), null) ?></a>
</div>
<p><?php echo auto_wrap_text($sf_asset->getFilename()) ?></p>
<p><?php echo __('%weight% KiB', array('%weight%' => $sf_asset->getFilesize()), 'sfAsset') ?></p>
<p><?php echo __('Created on %date%', array('%date%' => format_date($sf_asset->getCreatedAt('U'))), 'sfAsset') ?></p>

<form action="<?php echo url_for('@sf_asset_library_rename_asset') ?>" method="post">
  <div class="form-row">
    <label for="new_name">
      <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/page_edit.png', 'align=top alt="edit') ?>
      <?php echo link_to(__('Rename', null, 'sfAsset'), '@sf_asset_library_edit?id=' . $sf_asset->getId(), array('class' => 'toggle', 'rel' => '{ div: \'input_new_name\'}')) ?>
    </label>
    <div class="content" id="input_new_name" style="display:none">
      <?php echo $renameform['filename'] ?>
      <input type="submit" value="<?php echo __('Rename', null, 'sfAsset') ?>" />
    </div>
  </div>
  <?php echo $renameform->renderHiddenFields() ?>
</form>

<form action="<?php echo url_for('@sf_asset_library_move_asset') ?>" method="post">
  <div class="form-row">
    <label for="move_folder">
      <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/page_go.png', 'alt=go align=top') ?>
      <?php echo link_to(__('Move', null, 'sfAsset'), '@sf_asset_library_edit?id=' . $sf_asset->getId(), array('class' => 'toggle', 'rel' => '{ div: \'input_move_folder\'}')) ?>
    </label>
    <div class="content" id="input_move_folder" style="display:none">
      <?php echo $moveform['parent_folder'] ?>
     <input type="submit" value="<?php echo __('Move', null, 'sfAsset') ?>" />
    </div>
  </div>
  <?php echo $moveform->renderHiddenFields() ?>
</form>

<form action="<?php echo url_for('@sf_asset_library_replace_asset') ?>" method="post" enctype="multipart/form-data">
  <div class="form-row">
    <label for="new_file">
      <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/page_refresh.png', 'alt=refresh align=top') ?>
      <?php echo link_to(__('Replace', null, 'sfAsset'), '@sf_asset_library_edit?id=' . $sf_asset->getId(), array('class' => 'toggle', 'rel' => '{ div: \'input_new_file\'}')) ?>
    </label>
    <div class="content" id="input_new_file" style="display:none">
      <?php echo $replaceform['file']->render(array('size' => 10)) ?>
      <input type="submit" value="<?php echo __('Replace', null, 'sfAsset') ?>" />
    </div>
  </div>
  <?php echo $replaceform->renderHiddenFields() ?>
</form>

<div class="form-row">
  <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/page_delete.png', 'alt=delete align=top') ?>
  <?php echo link_to(__('Delete', null, 'sfAsset'), '@sf_asset_library_delete_asset?id=' . $sf_asset->getId(), array(
    'method'  => 'delete',
    'confirm' => __('Are you sure?', null, 'sfAsset'),
  )) ?>
</div>

<?php endif ?>
