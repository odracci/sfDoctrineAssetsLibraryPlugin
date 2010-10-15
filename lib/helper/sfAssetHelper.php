<?php

use_helper('Url', 'JavascriptBase');

function auto_wrap_text($text)
{
  return preg_replace('/([_\-\.])/', '<span class="wrap_space"> </span>$1<span class="wrap_space"> </span>', $text);
  //return wordwrap($text, 2, '<span class="wrap_space"> </span>', true);
}

/**
 * Gives an image tag for an asset
 *
 * @param  sfAsset $asset
 * @param  string  $thumbType
 * @param  array   $options
 * @param  string  $relativePath
 * @return string
 */
function asset_image_tag($asset, $thumbType = 'full', $options = array(), $relativePath = null)
{
  $options = array_merge(array(
    'alt'   => $asset->getDescription() . ' ' . $asset->getCopyright(),
    'title' => $asset->getDescription() . ' ' . $asset->getCopyright()
  ), $options);

  if ($asset->isImage() || $asset->isPdf())
  {
    $src = $asset->getUrl($thumbType, $relativePath, $asset->isPdf());
    if ($asset->isPdf() && !is_readable(sfConfig::get('sf_web_dir') . $src))
    {
      $src = '/sfDoctrineAssetsLibraryPlugin/images/pdf.png';
    }
  }
  elseif ($thumbType == 'full')
  {
    throw new sfAssetException('Impossible to render a non-image asset in an image tag');
  }
  else
  {
    switch ($asset->getType())
    {
      case 'txt':
        $src = '/sfDoctrineAssetsLibraryPlugin/images/txt.png';
        break;
      case 'xls':
        $src = '/sfDoctrineAssetsLibraryPlugin/images/xls.png';
        break;
      case 'doc':
        $src = '/sfDoctrineAssetsLibraryPlugin/images/doc.png';
        break;
      case 'pdf':
        $src = '/sfDoctrineAssetsLibraryPlugin/images/pdf.png';
        break;
      case 'html':
        $src = '/sfDoctrineAssetsLibraryPlugin/images/html.png';
        break;
      case 'archive':
        $src = '/sfDoctrineAssetsLibraryPlugin/images/archive.png';
        break;
      case 'bin':
        $src = '/sfDoctrineAssetsLibraryPlugin/images/bin.png';
        break;
      default:
        $src = '/sfDoctrineAssetsLibraryPlugin/images/unknown.png';
    }
  }

  return image_tag($src, $options);
}

function link_to_asset($text, $path, $options = array())
{
  return str_replace('%2F', '/', link_to($text, $path, $options));
}

/**
 * @param  string  $text
 * @param  sfAsset $asset
 * @return string
 */
function link_to_asset_action($text, $asset)
{
  $user = sfContext::getInstance()->getUser();
  if ($user->hasAttribute('popup', 'sf_admin/sf_asset/navigation'))
  {
    switch($user->getAttribute('popup', null, 'sf_admin/sf_asset/navigation'))
    {
      case 1:
        // popup called from a Rich Text Editor (ex: TinyMCE)
        #return link_to($text, '@sf_asset_library_tiny_config?id=' . $asset->getId(), 'title=' . $asset->getFilename());
        throw new sfAssetException('this option should be unused...');
      case 2:
        // popup called from a simple form input (or via input_sf_asset_tag)
        return link_to_function($text, "setImageField('" . $asset->getUrl() . "','" . $asset->getUrl('small') . "'," . $asset->getId() . ')');
      case 3:
        // popup called from a Rich Text Editor (ex: CK Editor)
        return link_to_function($text, "window.opener.CKEDITOR.tools.callFunction('".$user->getAttribute('func', 1, 'sf_admin/sf_asset/navigation')."', '" . $asset->getUrl() . "'); window.close()");
    }
  }
  else
  {
    // case : sf view (i.e. module sfAsset, view list)
    return link_to($text, '@sf_asset_library_edit?id=' . $asset->getId(), 'title=' . $asset->getFilename());
  }
}

/**
 * init asset library for use with TinyMCE
 */
function init_asset_library()
{
  sfContext::getInstance()->getEventDispatcher()->connect('response.filter_content', 'insert_asset_popup_url');
  use_javascript('/sfDoctrineAssetsLibraryPlugin/js/main', 'last');
}

/**
 * Enter description here...
 *
 * @see http://docs.cksource.com/CKEditor_3.x/Developers_Guide/File_Browser_%28Uploader%29/Custom_File_Browser
 * @param mixet $type  String (Files or Images) or null
 * @return string
 */
function get_asset_url($type = null) {
	$add = '';
	if (!is_null($type)) {
		$add = '&type='.$type;
	}
	return sfContext::getInstance()->getController()->genUrl('@sf_asset_list?popup=3'.$add);
}

/**
 * called just before content is sent
 * @see init_asset_library()
 * @param  sfEvent $event
 * @return string
 */
function insert_asset_popup_url(sfEvent $event)
{
  $div = '<div id="sf_asset_js_url" style="display:none">' . url_for('@sf_asset_library_list?popup=2') . '</div>';
  $content = $event->getSubject()->getContent();
  $body = strpos($content, '</body>');
  if (false !== $body)
  {
    $content = substr($content, 0, $body) . PHP_EOL . $div . PHP_EOL . substr($content, $body);
  }
  // this trick is need to get the web debug toolbar. Dunno why :-|
  if (sfConfig::get('sf_web_debug'))
  {
    $debug = current(sfContext::getInstance()->getLogger()->getLoggers());
    $content = $debug->filterResponseContent($event, $content);
  }

  return $content;
}

/**
 * get breadcrumbs
 * @param  string  $path
 * @param  boolean $linkLast
 * @param  string  $action
 * @return string
 */
function assets_library_breadcrumb($path, $linkLast = false, $action = '')
{
  $action = $action ? $action : sfContext::getInstance()->getRequest()->getParameter('action');
  if ($action == 'edit' || $action == 'update')
  {
    $action = 'list';
  }
  $html = '';
  $breadcrumb = explode('/', $path);
  $nb_dirs = count($breadcrumb);
  $current_dir = '';
  $i = 0;
  foreach ($breadcrumb as $dir)
  {
    if (!$linkLast && ($i == $nb_dirs - 1))
    {
      $html .= $dir;
    }
    else
    {
      $current_dir .= $i ? '/' . $dir : $dir;
      $html .= link_to_asset($dir, '@sf_asset_library_' . $action . '?dir=' . $current_dir) . '<span class="crumb">/</span>';
    }
    $i ++;
  }

  return $html;
}

/**
 * get input form field
 * @param  string  $name
 * @param  integer $value   possible value of asset id
 * @param  array   $options
 * @return string
 */
function input_sf_asset_image_tag($name, $value = null, $options = array())
{
  $url = str_replace('&', '&amp;', url_for('@sf_asset_library_list?dir=' . sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media') . '&popup=2'));
  $asset = empty($value) ? new sfAsset : sfAssetTable::getInstance()->find($value);
  
  return '<a id="sf_asset_input_image" href="#" rel="{url: \'' . $url . '\', name: \'' . $name . '\', type: \'' . $options['type'] . '\'}">' .
    image_tag('/sfDoctrineAssetsLibraryPlugin/images/folder_open', array('alt' => 'Insert Image', 'title' => __('Insert Image', null, 'sfAsset'))) . '</a> ' .
    asset_image_tag($asset, 'small', array('id' => $options['id'] . '_img'));
}