<?php

/**
 * Create a root node for the asset library
 *
 * @package    symfony
 * @subpackage task
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class sfAssetCreateRootTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
    ));

    $this->namespace = 'asset';
    $this->name = 'create-root';
    $this->briefDescription = 'Create a root node for the asset library';

    $this->detailedDescription = <<<EOF
The [asset:create-root|INFO] task creates a root node for the asset library:

  [./symfony asset:create-root|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    if (sfAssetFolderTable::getInstance()->getRoot())
    {
      throw new sfException('The asset library already has a root');
    }

    $this->logSection('asset', sprintf('Creating root node at %s...', sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media')), null, 'COMMENT');

    $folder = new sfAssetFolder();
    $folder->setName(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media'));
    
    $tree = sfAssetFolderTable::getInstance()->getTree()->createRoot($folder);
    $folder->save();

    $this->logSection('asset', 'Root Node Created', null, 'INFO');
  }
}
