<?php

/**
 * PluginsfAssetFolder form.
 *
 * @package    sfDoctrineAssetsLibraryPlugin
 * @subpackage form
 * @author     Massimiliano Arione
 * @author     Riccardo Bini
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginsfAssetFolderForm extends BasesfAssetFolderForm
{
  protected function setupInheritance()
  {
    // hide some fields
    unset($this['lft'], $this['rgt'], $this['level'], $this['relative_path'],
          $this['created_at'], $this['updated_at']);

    // add hidden parent folder
    $this->widgetSchema['parent_folder'] = new sfWidgetFormInputHidden();
    if (!empty($this->options['parent_id']))
    {
      $this->setDefault('parent_folder', $this->options['parent_id']);
    }
    $this->validatorSchema['parent_folder'] = new sfValidatorDoctrineChoice(array(
        'model' => 'sfAssetFolder',
      ));

    // avoid id conflict for name and parent_folder
    $this->widgetSchema['name']->setIdFormat('create_%s');
    $this->widgetSchema['parent_folder']->setIdFormat('create_%s');

    // check for: correct name, name not equal to "thumbnail"
    $this->validatorSchema['name'] = new sfValidatorAnd(array(
      new sfValidatorRegex(array('pattern' => '/^[a-zA-Z0-9\-\_\.]+$/')),
      new sfValidatorRegex(array(
        'pattern'    => '/^' . sfConfig::get('app_sfAssetsLibrary_thumbnail_dir', 'thumbnail') . '$/',
        'must_match' => false,
      )),
    ));
  }

  /**
   * save
   * @param Doctrine_connection $con
   */
  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }
    $this->updateObject();
    $parent = sfAssetFolderTable::getInstance()->find($this->getValue('parent_folder'));
    $this->getObject()->getNode()->insertAsLastChildOf($parent);
    $this->getObject()->save($con);
    // embedded forms
    $this->saveEmbeddedForms($con);
  }
}
