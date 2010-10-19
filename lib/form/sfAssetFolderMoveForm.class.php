<?php

/**
 * sfAssetFolder move form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */
class sfAssetFolderMoveForm extends BasesfAssetFolderForm
{
  public function configure()
  {
    // remove unneeded fields
    unset($this['name'], $this['lft'], $this['rgt'], $this['level'], $this['relative_path'],
          $this['created_at'], $this['updated_at']);

    // add parent folder select
    $this->widgetSchema['parent_folder'] = new sfWidgetFormDoctrineChoice(array('model' => 'sfAssetFolder',
                                                                              'query' => sfAssetFolderTable::getInstance()->getAllNonDescendantsPathsCriteria($this->getObject())));
    $this->validatorSchema['parent_folder'] = new sfValidatorDoctrineChoice(array('model' => 'sfAssetFolder',
                                                                                'column' => 'id',
                                                                                'required' => true));

    // avoid id conflict for id
    $this->widgetSchema['id']->setIdFormat('move_%s');
  }
}
