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
  $root = new sfAssetFolder();
  $root->setName(sfConfig::get('app_sfAssetsLibrary_upload_dir'));
  $tree = sfAssetFolderTable::getInstance()->getTree();
  $tree->createRoot($root);
//  sfAssetFolderPeer::createRoot($root);
  $root->save();
  $rootId = $root->getId();

  // run the test
  $t = new lime_test(10, array('options' => new lime_output_color(), 'error_reporting' => true));

  $t->diag('sfAssetFolder::getName()');

  # $sfAssetFolder is /root/Test_Directory
  $sfAssetFolder = new sfAssetFolder();
  $sfAssetFolder->setName('Test_Directory');
  $sfAssetFolder->getNode()->insertAsFirstChildOf($root);
  $sfAssetFolder->save();
  $t->is($sfAssetFolder->getName(), 'Test_Directory', 'getName() returns the folder name');
  
  $t->diag('sfAssetFolder::getRelativePath()');
  $t->is($sfAssetFolder->getRelativePath(), $root->getRelativePath() . '/' . $sfAssetFolder->getName(), 'getRelativePath() returns the folder relative path, including its own name');

  # $sfAssetFolder2 is /root/Test_Directory/Test_Sub-directory
  $sfAssetFolder2 = new sfAssetFolder();
  $sfAssetFolder2->setName('Test_Sub-directory');
  $sfAssetFolder2->getNode()->insertAsFirstChildOf($sfAssetFolder);
  $sfAssetFolder2->save();
  $t->is($sfAssetFolder2->getRelativePath(), $sfAssetFolder->getRelativePath() . '/' . $sfAssetFolder2->getName(), 'getRelativePath() returns the folder relative path, including its parent name');
  $id2 = $sfAssetFolder2->getId();
  
  # $sfAssetFolder3 is /root/Test_Directory/Test_Sub-directory/Test_Sub-sub-directory
  $sfAssetFolder3 = new sfAssetFolder();
  $sfAssetFolder3->getNode()->insertAsFirstChildOf($sfAssetFolder2);
  $sfAssetFolder3->setName('Test_Sub-sub-directory');
  $sfAssetFolder3->save();
  $t->is($sfAssetFolder3->getRelativePath(), $sfAssetFolder2->getRelativePath() . '/' . $sfAssetFolder3->getName(), 'getRelativePath() returns the folder relative path, including its ancestors names');
  $id3 = $sfAssetFolder3->getId();
  
  # $sfAsset is /root/Test_Directory/Test_Sub-directory/raikkonen.jpg
  $assets_path = dirname(__FILE__).'/../assets/';
  $test_asset = $assets_path . 'raikkonen.jpg';
  $sfAsset = new sfAsset();
  $sfAsset->setFolder($sfAssetFolder2);
  $sfAsset->create($test_asset, false);
  $sfAsset->save();
  $sf_asset_id = $sfAsset->getId();
  
  # $sfAsset2 is /root/Test_Directory/Test_Sub-directory/Test_Sub-sub-directory/toto
  $sfAsset2 = new sfAsset();
  $sfAsset2->setFolder($sfAssetFolder3);
  $sfAsset2->setFilename('toto');
  $sfAsset2->create($test_asset, false);
  $sfAsset2->save();
  $sf_asset2_id = $sfAsset2->getId();

  // TODO: it dosen't work w/o
  Doctrine_Core::getTable('sfAssetFolder')->getTree()->fetchTree();
  
  # So now we have:
  # root/
  #   Test_Directory/               sfAssetFolder
  #     Test_Sub-directory/         sfAssetFolder2
  #       Test_Sub-sub-directory/   sfAssetFolder3
  #         toto
  #       raikkonen.jpg

  $t->diag('sfAssetFolder::move()');

  # move $sfAssetFolder2 from /root/Test_Directory/Test_Sub-directory to /root/Test_Sub-directory
  $sfAssetFolder2->move($root);
  $sfAssetFolder2->save();

  # So now we have:
  # root/
  #   Test_Directory/             sfAssetFolder
  #   Test_Sub-directory/         sfAssetFolder2
  #     Test_Sub-sub-directory/   sfAssetFolder3
  #       toto
  #     raikkonen.jpg

  // Bug in Propel instance pooling + NestedSets...
//  sfAssetFolderPeer::clearInstancePool(); clear()
  $root = sfAssetFolderTable::getInstance()->find($rootId);
  $sfAssetFolder2 = sfAssetFolderTable::getInstance()->find($id2);
  $sfAssetFolder3 = sfAssetFolderTable::getInstance()->find($id3);

  $t->is($sfAssetFolder2->getParent()->getId(), $root->getId(), 'move() gives the correct parent');
  $t->is($sfAssetFolder3->getParent()->getParent()->getId(), $root->getId(), 'move() also moves children');
  $t->is($sfAssetFolder2->getRelativePath(), $root->getRelativePath() . '/' . $sfAssetFolder2->getName(), 'move() changes descendants relative paths');
  $t->is($sfAssetFolder3->getRelativePath(), $sfAssetFolder2->getRelativePath() . '/' . $sfAssetFolder3->getName(), 'move() changes descendants relative paths');
  
//  sfAssetPeer::clearInstancePool();
  $sfAsset = sfAssetTable::getInstance()->find($sf_asset_id);
  $sfAsset2 = sfAssetTable::getInstance()->find($sf_asset2_id);
  $t->ok(is_file($sfAsset->getFullPath()), 'move() also moves assets under the moved folder');
  $t->ok(is_file($sfAsset2->getFullPath()), 'move() also moves assets under the moved folder');
}
catch (Exception $e)
{
  echo 'errore: ' . $e->getMessage() . PHP_EOL;
  echo $e->getTraceAsString() . PHP_EOL;
}

// reset DB
$con->rollBack();

function debugTree()
{
  $treeObject = Doctrine_Core::getTable('sfAssetFolder')->getTree()->fetchTree();
  $tree = $treeObject->fetchTree();
//  
//  foreach ($tree as $folder) {
//      echo str_repeat('    ', $folder->getLevel()) . $folder->getName() . "\n";
//  }
}
