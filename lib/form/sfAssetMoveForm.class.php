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
          $this['type'], $this['filesize'], $this['created_at'], $this['updated_at']);

    // add parent folder select
    $this->widgetSchema['parent_folder'] = new sfWidgetFormDoctrineChoice(array('model' => 'sfAssetFolder',
                                                                              'query' => sfAssetFolderTable::getInstance()->getAllPathsButOneCriteria($this->getObject()->getFolder())));
    $this->validatorSchema['parent_folder'] = new sfValidatorDoctrineChoice(array('model' => 'sfAssetFolder',
                                                                                'column' => 'id',
                                                                                'required' => true));
    // avoid id conflict for id
    $this->widgetSchema['id']->setIdFormat('move_%s');
  }
}
