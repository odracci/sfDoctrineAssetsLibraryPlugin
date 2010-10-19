<?php

// guess current application
if (!isset($app))
{
  $app = 'frontend';
}

require_once dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php';

$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

// remove all cache
sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));

sfConfig::set('app_sfAssetsLibrary_upload_dir', 'media');

// clear possible fixture directories
$mediaDir = sfAssetsLibraryTools::getMediaDir(true) . sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media');
@unlink($mediaDir . '/TESTsubdir1/thumbnail/large_demo.png');
@unlink($mediaDir . '/TESTsubdir1/thumbnail/small_demo.png');
@unlink($mediaDir . '/TESTsubdir1/demo.png');
@rmdir($mediaDir . '/TESTsubdir1/foobar/thumbnail');
@rmdir($mediaDir . '/TESTsubdir1/foobar/');
//
@rmdir($mediaDir . '/TESTsubdir1/thumbnail/');
@rmdir($mediaDir . '/TESTsubdir1/');
@rmdir($mediaDir . '/TESTsubdir2/thumbnail/');
@rmdir($mediaDir . '/TESTsubdir2/');
@rmdir($mediaDir . '/TESTsubdir3/');

// cp data files - why are they deleted during tests? :-|
copy(dirname(__FILE__) . '/../data/demo1.png', dirname(__FILE__) . '/../data/demo.png');
copy(dirname(__FILE__) . '/../data/propel1.gif', dirname(__FILE__) . '/../data/propel.gif');
copy(dirname(__FILE__) . '/../data/demo1.png', dirname(__FILE__) . '/../data/demo2.png');
copy(dirname(__FILE__) . '/../data/propel1.gif', dirname(__FILE__) . '/../data/propel2.gif');

// load fixtures
Doctrine_Core::loadData(dirname(__FILE__) . '/../data/fixtures/');
