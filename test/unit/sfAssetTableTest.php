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
  $f = new sfAssetFolder();
  $f->setName(sfConfig::get('app_sfAssetsLibrary_upload_dir'));
  $tree = sfAssetFolderTable::getInstance()->getTree();
  $tree->createRoot($f);
  $f->save();

  $t = new lime_test(5, array('options' => new lime_output_color(), 'error_reporting' => true));
  $t->diag('sfAssetPeer');

  $t->is(sfAssetTable::getInstance()->retrieveFromUrl(sfAssetFolderTable::getInstance()->getRoot()->getRelativePath() . '/filename.jpg'), null, 'sfAssetPeer::retrieveFromUrl() returns null when a URL is not found');

  $t->is(sfAssetTable::getInstance()->exists(sfAssetFolderTable::getInstance()->getRoot()->getId(), 'filename.jpg'), false, 'sfAssetPeer::exists() returns false when an asset is not found');

  $sfAsset = new sfAsset();
  $sfAsset->setFolder(sfAssetFolderTable::getInstance()->getRoot());
  $sfAsset->setFilename('filename.jpg');
  $sfAsset->save();

  $t->is(sfAssetTable::getInstance()->retrieveFromUrl(sfAssetFolderTable::getInstance()->getRoot()->getRelativePath() . '/filename.jpg')->getId(), $sfAsset->getId(), 'sfAssetPeer::retrieveFromUrl() finds an asset from its URL');
  $t->is(sfAssetTable::getInstance()->retrieveFromUrl($sfAsset->getUrl())->getId(), $sfAsset->getId(), 'sfAssetPeer::retrieveFromUrl() finds an asset from the result of `getUrl()`');
  $t->is(sfAssetTable::getInstance()->exists(sfAssetFolderTable::getInstance()->getRoot()->getId(), 'filename.jpg'), true, 'sfAssetPeer::exists() returns true when an asset is found');
}
catch (Exception $e)
{
  echo $e->getMessage();
}

// reset DB
$con->rollBack();
