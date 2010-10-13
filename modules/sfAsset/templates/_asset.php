<?php use_helper('sfAsset') ?>
<div class="assetImage">
  <div class="thumbnails">
    <?php echo link_to_asset_action(asset_image_tag($sf_asset, 'small', array(), isset($folder) ? $folder->getRelativePath() : null), $sf_asset) ?>
  </div>

  <div class="assetComment">
    <?php echo auto_wrap_text($sf_asset->getFilename()) ?>
    <div class="details">
      <?php echo __('%weight% KiB', array('%weight%' => $sf_asset->getFilesize()), 'sfAsset') ?>
      <?php if (!$sf_user->hasAttribute('popup', 'sf_admin/sf_asset/navigation')): ?>
        <?php echo link_to(image_tag('/sfDoctrineAssetsLibraryPlugin/images/delete.png', 'alt=delete class=deleteImage align=top'), '@sf_asset_library_delete_asset?id='.$sf_asset->getId(), array('title' => __('Delete', null, 'sfAsset'), 'method' => 'delete', 'confirm' => __('Are you sure?', null, 'sfAsset'))) ?>
      <?php endif ?>
    </div>
  </div>
</div>
