<?php
/**
 * @author MaGénération
 */

include dirname(__FILE__).'/../../../../test/bootstrap/unit.php';
$pluginPaths = $configuration->getAllPluginPaths();
include $pluginPaths['sfDoctrineAssetsLibraryPlugin'] . '/lib/sfAssetsLibraryTools.class.php';

// run the test
$t = new lime_test(5, array('options' => new lime_output_color(), 'error_reporting' => true));
$t->diag('assets tools test');

list ($base, $name) = sfAssetsLibraryTools::splitPath('simple');
$t->is($name, 'simple', 'splitPath() gives same name on simple strings');
$t->is($base, '',  'splitPath() gives empty base on simple strings');

list ($base, $name) = sfAssetsLibraryTools::splitPath('root' . DIRECTORY_SEPARATOR . 'file');
$t->is($name ,'file', 'splitPath() splits by / gives good name');
$t->is($base ,'root', 'splitPath() splits by / gives good simple base');

list ($base, $name) = sfAssetsLibraryTools::splitPath(DIRECTORY_SEPARATOR . 'Articles' . DIRECTORY_SEPARATOR . 'People' . DIRECTORY_SEPARATOR . 'Sarkozy');
$t->is($base , DIRECTORY_SEPARATOR . 'Articles' . DIRECTORY_SEPARATOR . 'People', 'splitPath() splits by DIRECTORY_SEPARATOR gives good composed base');
