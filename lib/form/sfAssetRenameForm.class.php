<?php

/**
 * sfAsset rename form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */
class sfAssetRenameForm extends BasesfAssetForm
{
  public function configure()
  {
    // remove unneeded fields
    unset($this['folder_id'], $this['description'], $this['author'], $this['copyright'],
          $this['type'], $this['filesize'], $this['created_at'], $this['updated_at']);

    // avoid id conflict for id
    $this->widgetSchema['id']->setIdFormat('rename_%s');
  }
}
