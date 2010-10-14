<?php

/**
 * sfAsset replace form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */
class sfAssetReplaceForm extends BasesfAssetForm
{
  public function configure()
  {
    // remove unneeded fields
    $this->useFields(array('id'));

    // new file
    $this->widgetSchema['file'] = new sfWidgetFormInputFile();
    $this->validatorSchema['file'] = new sfValidatorFile();

    // avoid id conflict for id
    $this->widgetSchema['id']->setIdFormat('replace_%s');
  }
}
