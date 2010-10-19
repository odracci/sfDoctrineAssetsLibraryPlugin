<?php use_helper('sfAsset') ?>
<div id="sf_asset_breadcrumbs">
   <?php echo assets_library_breadcrumb($folder->getRelativePath(ESC_RAW)) ?>
</div>

<?php if (!$folder->isRoot()): ?>
  <div class="assetImage">
    <div class="thumbnails">
      <?php echo link_to_asset(image_tag('/sfDoctrineAssetsLibraryPlugin/images/up', 'size=64x64 alt=up title='.__('Parent directory', null, 'sfAsset')), '@sf_asset_library_dir?dir='. $folder->getParentPath()) ?>
    </div>
    <div class="assetComment" id="ajax_dir_0">..</div>
  </div>
<?php endif ?>

<?php foreach ($dirs as $dir): ?>
  <div class="assetImage">
    <div class="thumbnails">
      <?php echo link_to_asset(image_tag('/sfDoctrineAssetsLibraryPlugin/images/folder', 'size=64x64 alt=folder title='.$dir->getName()), '@sf_asset_library_dir?dir=' . $dir->getRelativePath()) ?>
    </div>
    <div class="assetComment"><?php echo auto_wrap_text($dir->getName()) ?>
    </div>
  </div>
<?php endforeach ?>

<?php foreach ($files as $sf_asset): ?>
  <?php include_partial('sfAsset/asset', array('sf_asset' => $sf_asset, 'folder' => $folder)) ?>
<?php endforeach ?>