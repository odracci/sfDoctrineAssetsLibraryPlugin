<?php

/**
 * sfAssetFolder rename form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */
class sfAssetFolderRenameForm extends BasesfAssetFolderForm
{
  public function configure()
  {
    // remove unneeded fields
    unset($this['lft'], $this['rgt'], $this['level'], $this['relative_path'],
          $this['created_at'], $this['updated_at']);

    // avoid id conflict for id
    $this->widgetSchema['id']->setIdFormat('rename_%s');
  }
}
