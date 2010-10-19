<?php

$app = 'frontend';
include dirname(__FILE__).'/../../../../test/bootstrap/functional.php';
include $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';
$databaseManager = new sfDatabaseManager($configuration);
$con = Doctrine::getConnectionByTableName('sfAsset');

$con->beginTransaction();
try
{
  // prepare test environment
  sfAssetFolderTable::getInstance()->createQuery()->delete()->execute();
  sfAssetTable::getInstance()->createQuery()->delete()->execute();
  sfConfig::set('app_sfAssetsLibrary_upload_dir', 'mediaTEST');
  $root = new sfAssetFolder();
  $root->setName(sfConfig::get('app_sfAssetsLibrary_upload_dir'));
  $tree = sfAssetFolderTable::getInstance()->getTree();
  $tree->createRoot($root);
  $root->save();

  // run the tests
  $t = new lime_test(23, array('options' => new lime_output_color(), 'error_reporting' => true));

  $t->diag('sfAsset');

  $sfAsset = new sfAsset();
  $sfAsset->setFolder($root);
  $t->isa_ok($sfAsset->getFolder(), 'sfAssetFolder', 'sfAsset can have root as folder');
  $sfAsset->setFilename('filename.jpg');

  $t->diag('sfAsset::getRelativePath()');
  $t->is($sfAsset->getRelativePath(), sfConfig::get('app_sfAssetsLibrary_upload_dir') .  DIRECTORY_SEPARATOR . 'filename.jpg', 'getRelativePath() returns the path relative to the media directory');

  $t->diag('sfAsset::getFullPath()');
  $t->is($sfAsset->getFullPath(), sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR . sfConfig::get('app_sfAssetsLibrary_upload_dir') .  DIRECTORY_SEPARATOR . 'filename.jpg', 'getFullPath() returns the complete asset path on the disk');
  $t->is($sfAsset->getFullPath('small'), sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR . sfConfig::get('app_sfAssetsLibrary_upload_dir')  . DIRECTORY_SEPARATOR . 'thumbnail' . DIRECTORY_SEPARATOR . 'small_filename.jpg', 'getFullPath(\'small\') returns the complete small thumbnail path on the disk');
  $t->is($sfAsset->getFullPath('large'), sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR . sfConfig::get('app_sfAssetsLibrary_upload_dir')  . DIRECTORY_SEPARATOR . 'thumbnail' . DIRECTORY_SEPARATOR . 'large_filename.jpg', 'getFullPath(\'large\') returns the complete large thumbnail path on the disk');

  $t->diag('sfAsset::getUrl()');
  $t->is($sfAsset->getUrl(), '/' . sfConfig::get('app_sfAssetsLibrary_upload_dir') . DIRECTORY_SEPARATOR . 'filename.jpg', 'getUrl() returns the asset URL');
  $t->is($sfAsset->getUrl('small'), '/' . sfConfig::get('app_sfAssetsLibrary_upload_dir') . '/thumbnail/small_filename.jpg', 'getUrl(\'small\') returns the small thumbnail url');
  $t->is($sfAsset->getUrl('large'), '/' . sfConfig::get('app_sfAssetsLibrary_upload_dir') . '/thumbnail/large_filename.jpg', 'getUrl(\'large\') returns the large thumbnail url');

  $t->diag('sfAsset::create()');
  $assets_path = dirname(__FILE__)  . DIRECTORY_SEPARATOR .  '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
  $test_asset = $assets_path . 'raikkonen.jpg';
  $folder1 = new sfAssetFolder();
  $folder1->getNode()->insertAsFirstChildOf($root);
  $folder1->setName('Test_Directory');
  $folder1->save();

  $asset1 = new sfAsset();
  $asset1->setFolder($folder1);
  $asset1->create($test_asset, false);
  
  $asset_file_size = (int) (filesize($test_asset) / 1024);

  $t->is($asset1->getFilename(), 'raikkonen.jpg', 'create() sets the object filename according to the given asset');
  $t->is($asset1->getFilesize(), 18, 'create() sets the object filesize according to the given asset');
  $t->ok($asset1->isImage(), 'create() sets the object type according to the given asset');
  $t->is($asset1->getUrl(), $folder1->getUrl() . '/' . $asset1->getFilename(), 'create() sets the object url according to the given asset and folder object');
  $t->ok(is_file($asset1->getFullPath()), 'create() physically copies asset');
  $t->ok(is_file($asset1->getFullPath('large')), 'create() physically creates thumbnail');

  $t->diag('sfAsset::move()');
  $old_path = $asset1->getFullPath();
  $old_thumb_path = $asset1->getFullPath('large');
  $asset1->move($root, 'raikkonen2.jpg');
  $t->is($asset1->getFilename(), 'raikkonen2.jpg', 'move() changes filename');
  $t->is($asset1->getUrl(), $root->getUrl() . '/' . $asset1->getFilename(), 'move() changes the object url according to the new folder');
  $t->ok(is_file($asset1->getFullPath()), 'move() physically moves asset to the new location');
  $t->ok(!is_file($old_path), 'move() physically removes asset from the previous location');
  $t->ok(is_file($asset1->getFullPath('large')), 'move() physically moves thumbnail to the new location');
  $t->ok(!is_file($old_thumb_path), 'move() physically removes thumbnail from the previous location');

  $t->diag('sfAsset::delete()');
  $old_path = $asset1->getFullPath();
  $old_thumb_path = $asset1->getFullPath('large');
  $old_id = $asset1->getId();
  $asset1->delete();
  $t->ok(! is_file($old_path),'delete() physically removes asset');
  $t->ok(! is_file($old_thumb_path),'delete() physically removes thumbnail');
  $null = sfAssetTable::getInstance()->find($old_id);
  $t->ok(! $null,'delete() removes asset from DB');
}
catch (Exception $e)
{
  echo $e->getMessage() . PHP_EOL;
  echo $e->getTraceAsString() . PHP_EOL;
}

// reset DB
$con->rollBack();
