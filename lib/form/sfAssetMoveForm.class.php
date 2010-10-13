<?php

/**
 * sfAsset move form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */
class sfAssetMoveForm extends BasesfAssetForm
{
  public function configure()
  {
    // remove unneeded fields
    unset($this['folder_id'], $this['filename'], $this['description'], $this['author'], $this['copyright'],
          $this['type'], $this['filesize'], $this['created_at']);

    // add parent folder select
    $this->widgetSchema['parent_folder'] = new sfWidgetFormPropelChoice(array('model' => 'sfAssetFolder',
                                                                              'criteria' => sfAssetFolderPeer::getAllPathsButOneCriteria($this->getObject()->getFolder())));
    $this->validatorSchema['parent_folder'] = new sfValidatorPropelChoice(array('model' => 'sfAssetFolder',
                                                                                'column' => 'id',
                                                                                'required' => true));
    // avoid id conflict for id
    $this->widgetSchema['id']->setIdFormat('move_%s');
  }
}
