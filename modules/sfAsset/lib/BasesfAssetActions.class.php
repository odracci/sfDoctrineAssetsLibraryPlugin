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
    $this->dirs = $this->getChildren($folder);
    $this->files = $folder->getSortedFiles($this->dirs, $this->processSort($request));
    $this->nbFiles = count($this->files);
    $this->totalSize = sfAssetFolderTable::countFilesSize($this->files);
    $this->nbDirs = count($this->dirs);
    $this->folder = $folder;

    $this->removeLayoutIfPopup($request);
  }
  
  protected function getChildren($folder) {
    $children = $folder->getNode()->getChildren();
    if ($children === false) {
      return array();
    }
    return $children;
  }

  /**
   * search asset
   * @param sfWebRequest $request
   */
  public function executeSearch(sfWebRequest $request)
  {
    $this->form = new sfAssetFormFilter();
    $this->form->bind($request->getParameter($this->form->getName()));
    $this->filterform = new sfAssetFormFilter();

    // We keep the search params in the session for easier pagination
    if ($request->hasParameter('search_params'))
    {
      $searchParams = $request->getParameter('search_params');
      if (isset($searchParams['created_at']['from']) && $searchParams['created_at']['from'] !== '')
      {
        $searchParams['created_at']['from'] = sfI18N::getTimestampForCulture($searchParams['created_at']['from'], $this->getUser()->getCulture());
      }
      if (isset($searchParams['created_at']['to']) && $searchParams['created_at']['to'] !== '')
      {
        $searchParams['created_at']['to'] = sfI18N::getTimestampForCulture($searchParams['created_at']['to'], $this->getUser()->getCulture());
      }

      $this->getUser()->getAttributeHolder()->removeNamespace('sf_admin/sf_asset/search_params');
      $this->getUser()->getAttributeHolder()->add($searchParams, 'sf_admin/sf_asset/search_params');
    }

    $this->search_params = $this->getUser()->getAttributeHolder()->getAll('sf_admin/sf_asset/search_params');

    $sort = $this->processSort($request);
    $params = $this->form->isValid() ? $this->form->getValues() : array();
    $this->pager = sfAssetTable::getInstance()->getPager($params, $sort, $request->getParameter('page', 1), sfConfig::get('app_sfAssetsLibrary_search_pager_size', 20));

    $this->removeLayoutIfPopup($request);
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
   * move folder
   * @param sfWebRequest $request
   */
  public function executeMoveFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->getMethod() == sfRequest::POST, 'method not allowed');
    $sf_asset_folder = $request->getParameter('sf_asset_folder');
    $folder = sfAssetFolderTable::getInstance()->find($sf_asset_folder['id']);
    $this->forward404Unless($folder, 'folder not found');
    $this->form = new sfAssetFolderMoveForm($folder);
    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        $targetFolder = sfAssetFolderTable::getInstance()->find($this->form->getValue('parent_folder'));
        $folder->move($targetFolder);
        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $folder)));
        $this->getUser()->setFlash('notice', 'The folder has been moved');
      }
      catch (sfAssetException $e)
      {
        $this->getUser()->setFlash('warning_message', $e->getMessage());
        $this->getUser()->setFlash('warning_params', $e->getMessageParams());
      }

      return $this->redirectToPath('@sf_asset_library_dir?dir=' . $folder->getRelativePath());
    }
  }
  
  /**
   * rename folder
   * @param sfWebRequest $request
   */
  public function executeRenameFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->getMethod() == sfRequest::POST, 'method not allowed');
    $sfAssetFolder = $request->getParameter('sf_asset_folder');
    $folder = sfAssetFolderTable::getInstance()->find($sfAssetFolder['id']);
    $this->forward404Unless($folder, 'folder not found');
    $this->form = new sfAssetFolderRenameForm($folder);
    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid())
    {
      try
      {
        $folder->rename($this->form->getValue('name'));
        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $folder)));
        $this->getUser()->setFlash('notice', 'The folder has been renamed');
      }
      catch (sfAssetException $e)
      {
        $this->getUser()->setFlash('warning_message', $e->getMessage());
        $this->getUser()->setFlash('warning_params', $e->getMessageParams());
      }

      return $this->redirectToPath('@sf_asset_library_dir?dir=' . $folder->getRelativePath());
    }
  }
  
  /**
   * delete folder
   * @param sfWebRequest $request
   */
  public function executeDeleteFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::DELETE), 'method not allowed');
    $folder = sfAssetFolderTable::getInstance()->find($request->getParameter('id'));
    $this->forward404Unless($folder);
    try
    {
      $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $folder)));
      $folder->delete();
      $this->getUser()->setFlash('notice', 'The folder has been deleted');
    }
    catch (sfAssetException $e)
    {
      $this->getUser()->setFlash('warning_message', $e->getMessage());
      $this->getUser()->setFlash('warning_params', $e->getMessageParams());
    }

    return $this->redirectToPath('@sf_asset_library_dir?dir=' . $folder->getParentPath());
  }
  
  /**
   * upload asset
   * @param sfWebRequest $request
   */
  public function executeAddQuick(sfWebRequest $request)
  {
    try
    {
      $this->form = new sfAssetForm(null, array('author' => $this->getUser()->getUsername()));
    }
    catch (Exception $e)
    {
      $this->form = new sfAssetForm();
    }
    $this->form->bind($request->getParameter($this->form->getName()),
                      $request->getFiles($this->form->getName()));
    if (!$this->form->isValid())
    {
      $this->sfAsset = new sfAsset();
      return sfView::SUCCESS;
    }
    try
    {
      $asset = $this->form->save();
      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $asset)));
    }
    catch (sfAssetException $e)
    {
      $this->getUser()->setFlash('warning_message', $e->getMessage());
      $this->getUser()->setFlash('warning_params', $e->getMessageParams());
      $folder = sfAssetFolderTable::getInstance()->find($this->form->getValue('folder_id'));
      $this->redirectToPath('@sf_asset_library_dir?dir=' . $folder->getRelativePath());
    }
    if ($this->getUser()->hasAttribute('popup', 'sf_admin/sf_asset/navigation'))
    {
      if ($this->getUser()->getAttribute('popup', null, 'sf_admin/sf_asset/navigation') == 1)
      {
        $this->redirect('@sf_asset_library_tiny_config?id=' . $asset->getId());
      }
      else
      {
        $this->redirectToPath('@sf_asset_library_dir?dir=' . $asset->getFolderPath());
      }
    }
    $this->redirect('@sf_asset_library_edit?id=' . $asset->getId());
  }
  
  /**
   * upload many assets
   * @param sfWebRequest $request
   */
  public function executeMassUpload(sfWebRequest $request)
  {
    $options = array('size' => sfConfig::get('app_sfAssetsLibrary_mass_upload_size', 5));
    try
    {
      $options['author'] = $this->getUser()->getUsername();
    }
    catch (Exception $e)
    {
    }
    $this->form = new sfAssetsForm(null, $options);
    if ($request->getMethod() == sfRequest::POST)
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        try
        {
          $this->form->save();
          foreach ($this->form->assets as $asset)
          {
            $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $asset)));
          }
        }
        catch (sfAssetException $e)
        {
          $this->getUser()->setFlash('warning_message', $e->getMessage());
          $this->getUser()->setFlash('warning_params', $e->getMessageParams());
          $folder = sfAssetFolderTable::getInstance()->find($this->form->getValue('folder_id'));
          $this->redirectToPath('@sf_asset_library_dir?dir=' . $folder->getRelativePath());
        }
        $this->getUser()->setFlash('notice', 'Files successfully uploaded');
        $this->redirectToPath('@sf_asset_library_dir?dir=' . $asset->getFolderPath());
      }
    }
  }
  
  /**
   * delete asset
   * @param sfWebRequest $request
   */
  public function executeDeleteAsset(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::DELETE), 'method not allowed');
    $asset = sfAssetTable::getInstance()->find($request->getParameter('id'));
    $this->forward404Unless($asset, 'asset not found');
    try
    {
      $asset->delete();
      $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $asset)));
      $this->getUser()->setFlash('notice', 'The file has been deleted');
    }
    catch (PropelException $e)
    {
      $request->setError('delete', 'Impossible to delete asset, probably due to related records');
      return $this->forward('sfAsset', 'edit');
    }

    return $this->redirectToPath('@sf_asset_library_dir?dir=' . $asset->getFolderPath());
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
   * edit asset
   * @param sfWebRequest $request
   */
  public function executeEdit(sfWebRequest $request)
  {
    $this->sfAsset = sfAssetTable::getInstance()->find($request->getParameter('id'));
    $this->forward404Unless($this->sfAsset, 'asset not found');
    $this->form = new sfAssetForm($this->sfAsset);
    $this->renameform = new sfAssetRenameForm($this->sfAsset);
    $this->moveform = new sfAssetMoveForm($this->sfAsset);
    $this->replaceform = new sfAssetReplaceForm($this->sfAsset);
  }
  
  /**
   * update asset
   * @param sfWebRequest $request
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $sfAsset = $request->getParameter('sf_asset');
    $this->sfAsset = sfAssetTable::getInstance()->find($sfAsset['id']);
    $this->forward404Unless($this->sfAsset, 'asset not found');
    $this->form = new sfAssetForm($this->sfAsset);
    $this->renameform = new sfAssetRenameForm($this->sfAsset);
    $this->moveform = new sfAssetMoveForm($this->sfAsset);
    $this->replaceform = new sfAssetReplaceForm($this->sfAsset);
    if ($this->form->bindAndSave($request->getParameter($this->form->getName())))
    {
      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $this->sfAsset)));
      $this->getUser()->setFlash('notice', 'Your modifications have been saved');
      return $this->redirect('@sf_asset_library_edit?id=' . $this->sfAsset->getId());
    }
    else
    {
      $this->getUser()->setFlash('notice', 'Error: ' . $this->form->getErrorSchema());
    }
    $this->setTemplate('edit');
  }

  /**
   * move asset
   * @param sfWebRequest $request
   */
  public function executeMoveAsset(sfWebRequest $request)
  {
    $this->forward404Unless($request->getMethod() == sfRequest::POST, 'method not allowed');
    $sfAsset = $request->getParameter('sf_asset');
    $asset = sfAssetTable::getInstance()->find($sfAsset['id']);
    $this->forward404Unless($asset, 'asset not found');
    $destFolder = sfAssetFolderTable::getInstance()->find($sfAsset['parent_folder']);
    $this->forward404Unless($destFolder, 'destination folder not found');
    $this->form = new sfAssetMoveForm($asset);
    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid() && $destFolder->getId() != $asset->getFolderId())
    {
      try
      {
        $asset->move($destFolder);
        $asset->save();
        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $asset)));
        $this->getUser()->setFlash('notice', 'The file has been moved');
      }
      catch(sfAssetException $e)
      {
        $this->getUser()->setFlash('warning_message', $e->getMessage());
        $this->getUser()->setFlash('warning_params', $e->getMessageParams());
      }
    }
    else
    {
      $this->getUser()->setFlash('warning', 'The asset has not been moved.');
    }

    return $this->redirect('@sf_asset_library_edit?id=' . $asset->getId());
  }
  
  /**
   * rename asset
   * @param sfWebRequest $request
   */
  public function executeRenameAsset(sfWebRequest $request)
  {
    $this->forward404Unless($request->getMethod() == sfRequest::POST, 'method not allowed');
    $sfAsset = $request->getParameter('sf_asset');
    $asset = sfAssetTable::getInstance()->find($sfAsset['id']);
    $this->forward404Unless($asset, 'asset not found');
    $this->form = new sfAssetRenameForm($asset);
    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid() && $asset->getFilename() != $this->form->getValue('filename'))
    {
      try
      {
        $asset->move($asset->getFolder(), $this->form->getValue('filename'));
        $asset->save();
        $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $asset)));
        $this->getUser()->setFlash('notice', 'The file has been renamed');
      }
      catch(sfAssetException $e)
      {
        $this->getUser()->setFlash('warning_message', $e->getMessage());
        $this->getUser()->setFlash('warning_params', $e->getMessageParams());
      }
    }
    else
    {
      $this->getUser()->setFlash('notice', 'The asset has not been renamed.');
    }

    return $this->redirect('@sf_asset_library_edit?id=' . $asset->getId());
  }
  
  /**
   * replace asset
   * @param sfWebRequest $request
   */
  public function executeReplaceAsset(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST), 'method not allowed');
    $sfAsset = $request->getParameter('sf_asset');
    $asset = sfAssetTable::getInstance()->find($sfAsset['id']);
    $this->forward404Unless($asset, 'asset not found');
    $this->form = new sfAssetReplaceForm($asset);
    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      // physically replace asset
      $file = $this->form->getValue('file');
      $asset->destroy();
      $asset->create($file->getTempName(), false, false);
      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $asset)));
      $this->getUser()->setFlash('notice', 'The file has been replaced');
    }

    return $this->redirect('@sf_asset_library_edit?id=' . $asset->getId());
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
