<?php use_stylesheet('/sf/sf_admin/css/main') ?>
<?php use_helper('I18N')?>

<h1><?php echo __('Asset Library', null, 'sfAsset') ?></h1>

<?php if (!$popup) : ?>
  <?php include_partial('list_header', array('folder' => $folder)) ?>
<?php endif ?>

<div id="sf_asset_bar">
  <p><?php echo $folder->getName() ?></p>
  <?php if ($nbDirs > 0): ?>
    <p><?php echo format_number_choice('[1]One subfolder|(1,+Inf]%nb% subfolders', array('%nb%' => $nbDirs), $nbDirs, 'sfAsset') ?></p>
  <?php endif ?>
  <?php if ($nbFiles > 0): ?>
    <p><?php echo format_number_choice('[1]One file, %weight% KiB|(1,+Inf]%nb% files, %weight% KiB', array('%nb%' => $nbFiles, '%weight%' => $totalSize), $nbFiles, 'sfAsset') ?></p>
  <?php endif ?>
  <?php include_partial('sfAsset/sidebar_sort') ?>
  <?php include_partial('sfAsset/sidebar_search', array('form' => $filterform)) ?>
  <?php include_partial('sfAsset/sidebar_list', array('folder' => $folder, 'fileform' => $fileform, 'folderform' => $folderform, 'renameform' => $renameform, 'moveform' => $moveform)) ?>
</div>

<div id="sf_asset_container">
  <?php include_partial('sfAsset/messages') ?>
  <?php include_partial('sfAsset/list', array(
    'folder' => $folder,
    'dirs'   => $dirs,
    'files'  => $files
  )) ?>
</div>

<?php if (!$popup) : ?>
  <?php include_partial('sfAsset/list_footer', array('folder' => $folder)) ?>
<?php endif ?>