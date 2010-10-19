<?php

/**
 * Base actions for the sfDoctrineAssetsLibraryPlugin sfAsset module.
 * 
 * @package     sfDoctrineAssetsLibraryPlugin
 * @subpackage  sfAsset
 * @author      Your name here
 * @version     SVN: $Id: BaseActions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
abstract class BasesfAssetActions extends sfActions
{
  /**
   * redirect to list
   * @param sfWebRequest $request
   */
  public function executeIndex()
  {
    $this->getUser()->getAttributeHolder()->remove('popup', null, 'sf_admin/sf_asset/navigation');
    $this->redirect('@sf_asset_library_list');
  }
  
  /**
   * list folders and assets
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    $folder = sfAssetFolderTable::getInstance()->retrieveByPath($request->getParameter('dir'));
    if (!$folder)
    {
      if ($this->getUser()->getFlash('sfAsset_folder_not_found'))
      {
        throw new sfException('You must create a root folder. Use the `php symfony asset:create-root` command for that.');
      }
      else
      {
        if ($popup = $request->getParameter('popup'))
        {
          $this->getUser()->setAttribute('popup', $popup, 'sf_admin/sf_asset/navigation');
        }
        $this->getUser()->setFlash('sfAsset_folder_not_found', true);
        $this->redirect('@sf_asset_library_list');
      }
    }
    $this->filterform = new sfAssetFormFilter();
    $this->folderform = new sfAssetFolderForm(null, array('parent_id' => $folder->getId()));
    $this->fileform = new sfAssetForm(null, array('parent_id' => $folder->getId()));
    $this->renameform = new sfAssetFolderRenameForm($folder);
    $this->moveform = new sfAssetFolderMoveForm($folder);
    $childs = $folder->getNode()->getDescendants();
    $this->dirs = $childs ? $childs : array();
    $this->files = $folder->getSortedFiles($this->dirs, $this->processSort($request));
//    var_dump($folder->getSortedFiles($this->dirs, $this->processSort($request)));
    $this->files = array();
    $this->nbFiles = count($this->files);
    $this->totalSize = sfAssetFolderTable::countFilesSize($this->files);
    $this->nbDirs = count($this->dirs);
    $this->folder = $folder;

    $this->removeLayoutIfPopup($request);
  }

  /**
   * create new asset
   * @param sfWebRequest $request
   */
  public function executeCreate()
  {
    return $this->forward('sfAsset', 'edit');
  }
  
  /**
   * create folder
   * @param sfWebRequest $request
   */
  public function executeCreateFolder(sfWebRequest $request)
  {
    $this->form = new sfAssetFolderForm();
    if ($this->form->bindAndSave($request->getParameter($this->form->getName())))
    {
      $this->redirectToPath('@sf_asset_library_dir?dir=' . $this->form->getObject()->getRelativePath());
    }
  }
  
  /**
   * @param string  $path
   * @param integer $string
   */
  protected function redirectToPath($path, $statusCode = 302)
  {
    $url = $this->getController()->genUrl($path, true);
    $url = str_replace('%2F', '/', $url);

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfAction} redirect to "' . $url . '"');
    }

    $this->getController()->redirect($url, 0, $statusCode);

    throw new sfStopException();
  }

  /**
   * @param  sfWebRequest $request
   * @return string
   */
  protected function processSort(sfWebRequest $request)
  {
    if ($request->getParameter('sort'))
    {
      $this->getUser()->setAttribute('sort', $request->getParameter('sort'), 'sf_admin/sf_asset/sort');
    }

    return $this->getUser()->getAttribute('sort', 'name', 'sf_admin/sf_asset/sort');
  }

  /**
   * @param sfWebRequest $request
   */
  protected function removeLayoutIfPopup(sfWebRequest $request)
  {
    if ($popup = $request->getParameter('popup'))
    {
      $this->getUser()->setAttribute('popup', $popup, 'sf_admin/sf_asset/navigation');
    }
    if  ($this->getUser()->hasAttribute('popup', 'sf_admin/sf_asset/navigation'))
    {
      $this->setLayout($this->getContext()->getConfiguration()->getTemplateDir('sfAsset', 'popupLayout.php') . DIRECTORY_SEPARATOR . 'popupLayout');
      $this->popup = true;
      // tinyMCE?
      if ($request->getParameter('tiny') != null)
      {
        $this->getResponse()->addJavascript('tiny_mce/tiny_mce_popup');
      }
    }
    else
    {
      $this->popup = false;
    }
  }
  
}
