<?php

$app = 'frontend';
include dirname(__FILE__).'/../../../../test/bootstrap/functional.php';
include $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';
$databaseManager = new sfDatabaseManager($configuration);
$con = Doctrine::getConnectionByTableName('sfAssetFolder');

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

  // run the test
  $t = new lime_test(13, array('options' => new lime_output_color(), 'error_reporting' => true));
  $t->diag('sfAssetFolderTable');

  $sfAssetFolder = sfAssetFolderTable::getInstance()->retrieveByPath(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'mediaTEST'));
  $t->ok($sfAssetFolder->isRoot(), 'retrieveByPath() retrieves root from app_sfAssetsLibrary_upload_dir string: '.sfConfig::get('app_sfAssetsLibrary_upload_dir', 'mediaTEST'));

  $sfAssetFolder = sfAssetFolderTable::getInstance()->retrieveByPath();
  $t->ok($sfAssetFolder->isRoot(), 'retrieveByPath() retrieves root from empty string');

  $sfAssetFolder = sfAssetFolderTable::getInstance()->createFromPath(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'mediaTEST') . DIRECTORY_SEPARATOR . 'simple');
  $t->isa_ok($sfAssetFolder, 'sfAssetFolder', 'createFromPath() creates a sfAssetFolder from simple string');
  $t->isa_ok($sfAssetFolder->getParent(), 'sfAssetFolder', 'createFromPath() from simple string has a parent');
  $t->ok($sfAssetFolder->getParent()->isRoot(), 'createFromPath() creates a root child from simple string');

  $sfAssetFolder2 = sfAssetFolderTable::getInstance()->createFromPath(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'mediaTEST') . DIRECTORY_SEPARATOR . 'simple' . DIRECTORY_SEPARATOR . 'subfolder');
  $t->isa_ok($sfAssetFolder2, 'sfAssetFolder', 'createFromPath() creates a sfAssetFolder from simple string');
  $t->is($sfAssetFolder2->getParent()->getId(), $sfAssetFolder->getId(), 'createFromPath() from simple string parent is correct');

  $sfAssetFolder = sfAssetFolderTable::getInstance()->createFromPath(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'mediaTEST')  . DIRECTORY_SEPARATOR . 'second' . DIRECTORY_SEPARATOR . 'subfolder');
  $t->ok($sfAssetFolder instanceof sfAssetFolder, 'createFromPath() creates a sfAssetFolder from simple string');
  $t->ok($sfAssetFolder->getParent() instanceof sfAssetFolder, 'createFromPath() from composed string has a parent');
  $t->ok($sfAssetFolder->getParent()->getParent()->isRoot(), 'createFromPath() creates a root child from composed string');

  $sfAssetFolder = sfAssetFolderTable::getInstance()->createFromPath('third' . DIRECTORY_SEPARATOR . 'subfolder');
  $t->ok($sfAssetFolder instanceof sfAssetFolder, 'createFromPath() creates a sfAssetFolder from simple string');
  $t->ok($sfAssetFolder->getParent() instanceof sfAssetFolder, 'createFromPath() from composed string has a parent');
  $t->ok($sfAssetFolder->getParent()->getParent()->isRoot(), 'createFromPath() creates a root child from composed string');
}
catch (Exception $e)
{
  // do nothing
  echo $e->getMessage() . PHP_EOL;
}

// reset DB
$con->rollBack();
