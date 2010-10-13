<?php

/**
 * sfAsset multiple form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */
class sfAssetsForm extends BasesfAssetForm
{
  public $assets = array();

  public function configure()
  {
    // add file inputs
    for ($i = 1; $i <= $this->getOption('size'); $i ++)
    {
      $this->widgetSchema['file_' . $i] = new sfWidgetFormInputFile();
      $this->validatorSchema['file_' . $i] = new sfValidatorFile(array('required' => false));
    }

    // remove unneeded fields
    unset($this['filename'], $this['description'], $this['author'], $this['copyright'],
          $this['type'], $this['filesize'], $this['created_at']);

    // formatter (see sfWidgetFormSchemaFormatterAsset.class.php)
    $this->widgetSchema->setFormFormatterName('assets');
  }

  /**
   * save all assets
   * @param PropelPDO $con
   */
  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }
    for ($i = 1; $i <= $this->getOption('size'); $i ++)
    {
      if ($file = $this->getValue('file_' . $i))
      {
        $asset = new sfAsset();
        $asset->setFolderId($this->getValue('folder_id'));
        $asset->setDescription($file->getOriginalName());
        $asset->setAuthor($this->getOption('author'));
        $asset->setFilename($file->getOriginalName());
        $asset->create($file->getTempName());
        $asset->save();
        $this->assets[] = $asset;
      }
    }
  }

}
