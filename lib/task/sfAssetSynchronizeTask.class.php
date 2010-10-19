<?php

/**
 * Create a root node for the asset library
 *
 * @package    symfony
 * @subpackage task
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class sfAssetSynchronizeTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('dirname', sfCommandArgument::REQUIRED, 'The name of the directory where the media files are located (relative or absolute)'),
    ));
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('verbose', null, sfCommandOption::PARAMETER_OPTIONAL, 'If true, every file or database operation will issue an alert in STDOUT', true),
      new sfCommandOption('removeOrphanAssets', null, sfCommandOption::PARAMETER_OPTIONAL, 'If true, database assets with no associated file are removed', false),
      new sfCommandOption('removeOrphanFolders', null, sfCommandOption::PARAMETER_OPTIONAL, 'If true, database folders with no associated directory are removed', false),
    ));
    $this->namespace = 'asset';
    $this->name = 'synchronize';
    $this->briefDescription = 'Synchronize a physical folder content with the asset library';

    $this->detailedDescription = <<<EOF
The [asset:synchronize|INFO] synchronizes a physical folder content with the asset library:

  [./symfony asset:synchronize ./web/medias|INFO]

The command browses the folder recursively and adds every file found to the sfAssetLibrary tables.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    if (!$rootFolder = sfAssetFolderTable::getInstance()->getRoot())
    {
      $task = new sfAssetCreateRootTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $taskOption = array('--env=' . $options['env'], '--connection=' . $options['connection']);
      if ($options['application'])
      {
        $taskOption []= '--application=' . $options['application'];
      }
      $ret = $task->run(array(), $taskOption);
      $rootFolder = sfAssetFolderTable::getInstance()->getRoot();
    }
    
    $this->logSection('asset', sprintf('Comparing files from %s with assets stored in the database...', $arguments['dirname']), null, 'COMMENT');
    
    try
    {
      $rootFolder->synchronizeWith($arguments['dirname'], $options['verbose'], $options['removeOrphanAssets'], $options['removeOrphanFolders']);
    }
    catch (sfAssetException $e)
    {
      throw new sfException(strtr($e->getMessage(), $e->getMessageParams()));
    }
    
    $this->logSection('asset', 'Synchronization complete', null, 'INFO');
  }
}
