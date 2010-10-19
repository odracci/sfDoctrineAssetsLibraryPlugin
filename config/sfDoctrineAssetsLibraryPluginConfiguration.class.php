<?php

/**
 * sfDoctrineAssetsLibraryPlugin configuration.
 * 
 * @package     sfDoctrineAssetsLibraryPlugin
 * @subpackage  config
 * @author      Your name here
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class sfDoctrineAssetsLibraryPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '0.9.6';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    if (sfConfig::get('app_sfAssetsLibraryplugin_routes_register', true) && in_array('sfAsset', sfConfig::get('sf_enabled_modules', array())))
    {
      $this->dispatcher->connect('routing.load_configuration', array('sfAssetRouting', 'listenToRoutingLoadConfigurationEvent'));
    }
  }
}
