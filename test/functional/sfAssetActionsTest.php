<?php
include dirname(__FILE__).'/../bootstrap/functional.php';

$subdir1 = sfAssetFolderTable::getInstance()->retrieveByPath('TESTsubdir1', '/');
$subdir2 = sfAssetFolderTable::getInstance()->retrieveByPath('TESTsubdir2', '/');


if (!$subdir1) {
  include dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
  $t = new lime_test(1, array('options' => new lime_output_color(), 'error_reporting' => true));
  $t->fail('subdir1 doesn\'t exist.');
  exit();
}
if (!$subdir2) {
  include dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
  $t = new lime_test(1, array('options' => new lime_output_color(), 'error_reporting' => true));
  $t->fail('subdir2 doesn\'t exist.');
  exit();
}

$browser = new sfTestFunctional(new sfBrowser());

$browser->setTester('doctrine', 'sfTesterDoctrine');

$browser->
  info('assets list')->
  get('/sfAsset/dir/media')->
    with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'list')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div#sf_asset_bar p:first-child', '/media/')->
    checkElement('div#sf_asset_bar p:nth-child(2)', '/2 subfolders/')->
    checkElement('div#sf_asset_bar p:nth-child(3)', '/3 files, 3 KiB/')->
    checkElement('div#sf_asset_container div.assetImage', 5)->
    checkElement('div#sf_asset_container div.assetImage img[src$="folder.png"]', 2)->
    checkElement('div#sf_asset_container div.assetImage img[src$="asset1.png"]', 1)->
    checkElement('div#sf_asset_container div.assetImage:nth-child(4)', '/asset1/')->
    checkElement('div#sf_asset_container div.assetImage:nth-child(5)', '/asset2/')->
    checkElement('div#sf_asset_container div.assetImage:nth-child(6)', '/asset3/')->
  end()->

  info('sorting options')->
  click('Sort by date')->
  with('response')->begin()->
    checkElement('div#sf_asset_container div.assetImage:nth-child(4)', '/asset3/')->
    checkElement('div#sf_asset_container div.assetImage:nth-child(5)', '/asset2/')->
    checkElement('div#sf_asset_container div.assetImage:nth-child(6)', '/asset1/')->
  end()->
  click('Sort by name')->
  with('response')->begin()->
    checkElement('div#sf_asset_container div.assetImage:nth-child(4)', '/asset1/')->
    checkElement('div#sf_asset_container div.assetImage:nth-child(5)', '/asset2/')->
    checkElement('div#sf_asset_container div.assetImage:nth-child(6)', '/asset3/')->
  end()->

  info('enter first subfolder')->
  click('a[href$="media/TESTsubdir1"]')->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'list')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div#sf_asset_bar p:first-child', '/TESTsubdir1/')->
    checkElement('div#sf_asset_container div.assetImage', 2)->
    checkElement('div#sf_asset_container div.assetComment', '/\.\./')->
  end()->

  info('return to list')->
  click('a[href$="media"]')->
    with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'list')->
  end()->

  info('add new asset')->
  click('a[href$="media/TESTsubdir1"]')->
  click('input[value="Add"]', array('sf_asset' => array(
    'file' => '',
  )))->
  with('form')->begin()->
    hasErrors(1)->
    isError('file', 'required')->
  end()->
  get('/sfAsset/dir/media/TESTsubdir1')->
  click('input[value="Add"]', array('sf_asset' => array(
    'file' => dirname(__FILE__) . '/../data/demo.png',
  )))->
  with('form')->hasErrors(false)->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'addQuick')->
  end()->
  with('response')->isRedirected()->followRedirect()->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'edit')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('div#sf_asset_container div.content', '/demo\.png/')->
  end()->
  with('doctrine')->check('sfAsset', array(
    'folder_id'   => $subdir1->getId(),
    'filename'    => 'demo.png',
    'description' => 'demo.png',
    'type'        => 'image',
    'filesize'    => 4,
  ))->

  info('edit asset')->
  click('input.sf_admin_action_save', array('sf_asset' => array(
    'description' => 'this is a demo image',
    'author'      => 'Massimiliano Arione',
    'copyright'   => '2010',
  )))->
  with('form')->hasErrors(false)->
  with('response')->isRedirected()->followRedirect()->
  with('response')->begin()->
    checkElement('div.save-ok', '/Your modifications have been saved/')->
  end()->
  with('doctrine')->check('sfAsset', array(
    'folder_id'   => $subdir1->getId(),
    'filename'    => 'demo.png',
    'description' => 'this is a demo image',
    'author'      => 'Massimiliano Arione',
    'type'        => 'image',
    'filesize'    => 4,
  ))->

  info('create a new folder')->
  click('TESTsubdir1')->
  click('input[value="Create"]', array('sf_asset_folder' => array(
    'name' => '',
  )))->
  with('form')->begin()->
    hasErrors(1)->
    isError('name', 'required')->
  end()->
  get('/sfAsset/dir/media/TESTsubdir1')->
  click('input[value="Create"]', array('sf_asset_folder' => array(
    'name' => 'invalid name!',
  )))->
  with('form')->begin()->
    hasErrors(1)->
    isError('name', 'invalid')->
  end()->
  get('/sfAsset/dir/media/TESTsubdir1')->
  click('input[value="Create"]', array('sf_asset_folder' => array(
    'name' => 'thumbnail',
  )))->
  with('form')->begin()->
    hasErrors(1)->
    isError('name', 'invalid')->
  end()->
  get('/sfAsset/dir/media/TESTsubdir1')->
  click('input[value="Create"]', array('sf_asset_folder' => array(
    'name' => 'foobar',
  )))->
  with('form')->hasErrors(false)->
  with('doctrine')->check('sfAssetFolder', array(
    'lft'     => 3,
    'rgt'    => 4,
    'name'          => 'foobar',
    'relative_path' => 'media/TESTsubdir1/foobar',
  ))->

  info('rename folder')->
  with('response')->isRedirected()->followRedirect()->
  click('input[value="Rename"]', array('sf_asset_folder' => array(
    'name' => 'invalid name!',
  )))->
  with('form')->begin()->
    hasErrors(1)->
    isError('name', 'invalid')->
  end()->
  get('/sfAsset/dir/media/TESTsubdir1/foobar')->
  click('input[value="Rename"]', array('sf_asset_folder' => array(
    'name' => 'thumbnail',
  )))->
  with('form')->begin()->
    hasErrors(1)->
    isError('name', 'invalid')->
  end()->
  get('/sfAsset/dir/media/TESTsubdir1/foobar')->
  click('input[value="Rename"]', array('sf_asset_folder' => array(
    'name' => 'barfoo',
  )))->
  with('form')->hasErrors(false)->
  with('doctrine')->check('sfAssetFolder', array(
    'lft'     => 3,
    'rgt'     => 4,
    'name'          => 'barfoo',
    'relative_path' => 'media/TESTsubdir1/barfoo',
  ))->

  info('delete folder')->
  with('response')->isRedirected()->followRedirect()->
  click('Delete folder', array(), array('method' => 'delete', '_with_csrf' => true))->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'deleteFolder')->
  end()->
  with('doctrine')->check('sfAssetFolder', array(
    'lft'     => 3,
    'rgt'     => 4,
    'name'          => 'barfoo',
    'relative_path' => 'media/TESTsubdir1/barfoo',
  ), false)->
  with('response')->isRedirected()->followRedirect()->
  with('response')->begin()->
    checkElement('div.save-ok', '/The folder has been deleted/')->
  end()->

  info('move folder')->
  click('input[value="Move"]', array('sf_asset_folder' => array(
    'parent_folder' => $subdir2->getId(),
  )))->
  with('form')->hasErrors(false)->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'moveFolder')->
  end()->
  // TODO check rgt 6 was 4
  with('doctrine')->check('sfAssetFolder', array(
    'lft'     => 3,
    'rgt'    => 6,
    'name'          => 'TESTsubdir1',
    'relative_path' => 'media/TESTsubdir2/TESTsubdir1',
  ))->
  with('response')->isRedirected()->followRedirect()->
  with('response')->begin()->
    checkElement('div.save-ok', '/The folder has been moved/')->
  end()->

  info('move asset')->
  click('a[title="demo.png"]')->
  click('input[value="Move"]', array('sf_asset' => array(
    'parent_folder' => $subdir2->getId(),
  )))->
  with('form')->hasErrors(false)->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'moveAsset')->
  end()->
  with('response')->isRedirected()->followRedirect()->
  with('response')->begin()->
    checkElement('div.save-ok', '/The file has been moved/')->
  end()->
  with('doctrine')->check('sfAsset', array(
    'folder_id'   => $subdir2->getId(),
    'filename'    => 'demo.png',
    'description' => 'this is a demo image',
    'type'        => 'image',
    'filesize'    => 4,
  ))->

  info('rename asset')->
  click('input[value="Rename"]', array('sf_asset' => array(
    'filename' => 'demorenamed.png',
  )))->
  with('form')->hasErrors(false)->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'renameAsset')->
  end()->
  with('response')->isRedirected()->followRedirect()->
  with('response')->begin()->
    checkElement('div.save-ok', '/The file has been renamed/')->
  end()->
  with('doctrine')->check('sfAsset', array(
    'folder_id'   => $subdir2->getId(),
    'filename'    => 'demorenamed.png',
    'description' => 'this is a demo image',
    'type'        => 'image',
  ))->

  info('replace asset')->
  click('input[value="Replace"]', array('sf_asset' => array(
    'file' => dirname(__FILE__) . '/../data/propel.gif',
  )))->
  with('form')->hasErrors(false)->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'replaceAsset')->
  end()->
  with('response')->isRedirected()->followRedirect()->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'edit')->
  end()->
  with('response')->begin()->
    checkElement('div.save-ok', '/The file has been replaced/')->
  end()->
  with('doctrine')->check('sfAsset', array(
    'folder_id'   => $subdir2->getId(),
    'filename'    => 'demorenamed.png',
    'description' => 'this is a demo image',
    'type'        => 'image',
  ))->

  info('delete asset')->
  click('Delete', array(), array('method' => 'delete', '_with_csrf' => true))->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'deleteAsset')->
  end()->
  with('doctrine')->check('sfAsset', array(
    'folder_id' => $subdir2->getId(),
    'filename'  => 'propel.gif',
    'type'      => 'image',
  ), false)->
  with('response')->isRedirected()->followRedirect()->
  with('response')->begin()->
    checkElement('div.save-ok', '/The file has been deleted/')->
  end()->

  info('mass upload')->
  click('media')->
  click('Mass upload')->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'massUpload')->
  end()->
  with('response')->begin()->
    isValid(true)->
  end()->
  click('Upload', array('sf_asset' => array(
    'folder_id' => $subdir1->getId(),
    'file_1'    => dirname(__FILE__) . '/../data/demo2.png',
    'file_2'    => dirname(__FILE__) . '/../data/propel2.gif',
  )))->
  with('form')->hasErrors(false)->
  with('doctrine')->begin()->
    check('sfAsset', array(
      'folder_id' => $subdir1->getId(),
      'filename'  => 'demo2.png',
      'type'      => 'image',
    ))->
    check('sfAsset', array(
      'folder_id' => $subdir1->getId(),
      'filename'  => 'propel2.gif',
      'type'      => 'image',
    ))->
  end()->

  info('search')->
  get('/sfAsset/dir/media')->
  click('input[value="Search"]')->
  with('form')->begin()->
    hasErrors(false)->
  end()->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'search')->
  end()->
  with('response')->begin()->
    checkElement('div.search_result', 7)->
  end()->
  click('input[value="Search"]', array('sf_asset_filters' => array(
    'filename' => array('text' => 'asset1'),
  )))->
  with('form')->begin()->
    hasErrors(false)->
  end()->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'search')->
  end()->
  with('response')->begin()->
    checkElement('div.search_result', 3)->
  end()->

  info('delete folder with sub folder')->
  get('/sfAsset/dir/media')->
  click('input[value="Create"]', array('sf_asset_folder' => array(
    'name' => 'sub',
  )))->
  with('response')->isRedirected()->followRedirect()->
  click('input[value="Create"]', array('sf_asset_folder' => array(
    'name' => 'sub2',
  )))->
  with('response')->isRedirected()->followRedirect()->
  info('create sub folder')->
  get('/sfAsset/dir/media/sub')->
  
  info('delete folder with sub folder')->
  click('Delete folder', array(), array('method' => 'delete', '_with_csrf' => true))->
  with('request')->begin()->
    isParameter('module', 'sfAsset')->
    isParameter('action', 'deleteFolder')->
  end()->
  with('response')->isRedirected()->followRedirect()->
  with('response')->begin()->
    checkElement('div.save-ok', '/The folder has been deleted/')->
  end();
  
  // TODO more tests...
