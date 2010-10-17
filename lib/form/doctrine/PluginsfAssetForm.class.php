<?php

/**
 * PluginsfAsset form.
 *
 * @package    sfDoctrineAssetsLibraryPlugin
 * @subpackage form
 * @author     Massimiliano Arione
 * @author     Riccardo Bini
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginsfAssetForm extends BasesfAssetForm
{
  protected function setupInheritance()
  {
      // hide some fields
    unset($this['created_at'], $this['updated_at'], $this['filesize']);

    // filename not required (since it's extracted from file)
    $this->validatorSchema['filename']->setOption('required', false);

    if ($this->getObject()->isNew())  // new asset (create)
    {
      // add hidden parent folder
      $this->widgetSchema['folder_id'] = new sfWidgetFormInputHidden();
      if (!empty($this->options['parent_id']))
      {
        $this->setDefault('folder_id', $this->options['parent_id']);
      }

      // add file input
      $this->widgetSchema['file'] = new sfWidgetFormInputFile();
      $this->validatorSchema['file'] = new sfValidatorFile();
    }
    else  // old asset (edit)
    {
      // hide other fields
      unset($this['folder_id'], $this['filename']);

      // types
      $types = sfConfig::get('app_sfAssetsLibrary_types', array(
        'image'   => 'image',
        'txt'     => 'txt',
        'archive' => 'archive',
        'pdf'     => 'pdf',
        'xls'     => 'xls',
        'doc'     => 'doc',
        'ppt'     => 'ppt',
      ));
      $this->widgetSchema['type'] = new sfWidgetFormChoice(array('choices' => $types));
      $this->validatorSchema['type'] = new sfValidatorChoice(array(
        'choices' => array_keys($types),
      ));

      // formatter (see sfWidgetFormSchemaFormatterAsset.class.php)
      $this->widgetSchema->setFormFormatterName('assets');
    }
  }

  /**
   * save physical file when adding new asset
   * @param  array   $values
   * @return sfAsset
   */
  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);
    if ($object->isNew())
    {
      $file = $this->getValue('file');
      $object->setAuthor($this->getOption('author'));
      $object->setFilename($file->getOriginalName());
      if ($this->getValue('description') == '')
      {
        $object->setDescription($file->getOriginalName());
      }
      $object->create($file->getTempName());
    }

    return $object;
  }
  
}
