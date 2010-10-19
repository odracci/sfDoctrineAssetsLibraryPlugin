<?php

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Francois Zaninotto <FRANCOIS.ZANINOTTO@symfony-project.com>
 */
class sfAssetRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();

    // prepend our routes

    $idActions = array(
     #'tiny_config'   => 'tinyConfigMedia',
      'delete_folder' => 'deleteFolder',
      'delete_asset'  => 'deleteAsset',
      'edit'          => 'edit',
    );

    $actions = array(
      'move_folder'   => 'moveFolder',
      'rename_folder' => 'renameFolder',
      'mass_upload'   => 'massUpload',
      'create_folder' => 'createFolder',
      'add_quick'     => 'addQuick',
      'rename_asset'  => 'renameAsset',
      'replace_asset' => 'replaceAsset',
      'move_asset'    => 'moveAsset',
      'update'        => 'update',
      'search'        => 'search',
      'list'          => 'list',
    );

    foreach ($actions as $route => $action)
    {
      $r->prependRoute('sf_asset_library_' . $route, new sfRoute('/sfAsset/' . $action, array(
        'module' => 'sfAsset', 'action' => $action,
      )));
    }

    foreach ($idActions as $route => $action)
    {
      $r->prependRoute('sf_asset_library_' . $route, new sfRoute('/sfAsset/' . $action . '/:id', array(
        'module' => 'sfAsset',
        'action' => $action,
      )));
    }

    $r->prependRoute('sf_asset_library_dir', new sfRoute('/sfAsset/dir/:dir', array(
        'module'  => 'sfAsset',
        'action'  => 'list',
        'dir'     => sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media')
      ),
      array('dir' => '.*?'))
    );

    $r->prependRoute('sf_asset_library_root', new sfRoute('/sfAsset', array(
      'module' => 'sfAsset',
      'action' => 'index',
      'dir'    => sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media')
    )));
  }
}
