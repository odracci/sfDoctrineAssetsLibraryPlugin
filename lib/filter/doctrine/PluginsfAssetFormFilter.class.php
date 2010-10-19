<?php

/**
 * PluginsfAsset form.
 *
 * @package    sfDoctrineAssetsLibraryPlugin
 * @subpackage filter
 * @author     Massimiliano Arione
 * @author     Riccardo Bini
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginsfAssetFormFilter extends BasesfAssetFormFilter
{
  protected function setupInheritance()
  {
    // hide some fields
    unset($this['filesize'], $this['type'], $this['updated_at']);

    // allow empty folder
    $this->widgetSchema['folder_id']->setOption('add_empty', true);

    // created_at as range
    $this->widgetSchema['created_at'] = new sfWidgetFormDateRange(array(
                                                                        'from_date' => new sfWidgetFormDate(),
                                                                        'to_date'   => new sfWidgetFormDate(),
                                                                        'template'  => '%from_date%<br />%to_date%',
                                                                        ));

    // restrict description to simple field, and move it to bottom
    $this->widgetSchema['description'] = new sfWidgetFormInput;
    $this->widgetSchema->moveField('description', sfWidgetFormSchema::LAST);

    // formatter (see sfWidgetFormSchemaFormatterAsset.class.php)
    $this->widgetSchema->setFormFormatterName('asset');
  }
  
}
