<?php
/*
 * This file is part of the sfAssetsLibrary package.
 *
 * (c) 2007 William Garcia <wgarcia@clever-age.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfAssetsLibraryToolkit toolkit class
 *
 * @author William Garcia
 */
class sfAssetsLibraryTools
{

  /**
   * @param  string $txt
   * @return string
   */
  public static function dot2slash($txt)
  {
    return preg_replace('#[\+\s]+#', '/', $txt);
  }

  /**
   * @param  string  $filepath
   * @return string
   */
  public static function getType($filepath)
  {
    $suffix = pathinfo($filepath, PATHINFO_EXTENSION);

    if (self::isImage($suffix))
    {
      return 'image';
    }
    else if (self::isText($suffix))
    {
      return 'txt';
    }
    else if (self::isArchive($suffix))
    {
      return 'archive';
    }
    else
    {
      return $suffix;
    }
  }

  /**
   * @param  string  $ext
   * @return boolean
   */
  public static function isImage($ext)
  {
    return in_array(strtolower($ext), array('png', 'jpg', 'jpeg', 'gif'));
  }

  /**
   * @param  string  $ext
   * @return boolean
   */
  public static function isText($ext)
  {
    return in_array(strtolower($ext), array('txt', 'php', 'markdown'));
  }

  /**
   * @param  string  $ext
   * @return boolean
   */
  public static function isArchive($ext)
  {
    return in_array(strtolower($ext), array('zip', 'gz', 'tgz', 'rar'));
  }

  /**
   * @param  string $dir
   * @param  string $filename
   * @return array
   */
  public static function getInfo($dir, $filename)
  {
    $info = array();
    $info['ext']  = substr($filename, strpos($filename, '.') - strlen($filename) + 1);
    $stats = stat($dir.'/'.$filename);
    $info['size'] = $stats['size'];
    $info['thumbnail'] = true;
    if (self::isImage($info['ext']))
    {
      if (is_readable($dir.'/thumbnail/small_'.$filename))
      {
        $info['icon'] = $dir.'/thumbnail/small_'.$filename;
      }
      else
      {
        $info['icon'] = $dir.'/'.$filename;
        $info['thumbnail'] = false;
      }
    }
    else
    {
      $info['icon'] = '/sfDoctrineAssetsLibraryPlugin/images/unknown.png';
      if (is_readable(sfConfig::get('sf_web_dir').'/sfDoctrineAssetsLibraryPlugin/images/'.$info['ext'].'.png'))
      {
        $info['icon'] = '/sfDoctrineAssetsLibraryPlugin/images/'.$info['ext'].'.png';
      }
    }

    return $info;
  }

  /**
   * @param  string  $file
   * @return string
   */
  public static function sanitizeName($file)
  {
    return preg_replace('/[^a-z0-9_\.-]/i', '_', $file);
  }

  /**
   * @param  string  $dirName
   * @param  string  $parentDirName
   * @return boolean
   */
  public static function mkdir($dirName, $parentDirName)
  {
    $dirName = rtrim($dirName, '/');

    if (!is_dir(self::getMediaDir(true) . $parentDirName))
    {
      list($parent, $name) = self::splitPath($parentDirName);
      if ($parent && $name)
      {
        $result = self::mkdir($name, $parent);
        if (!$result)
        {
          return false;
        }
      }
    }

    if (!$dirName)
    {
      throw new sfException('Trying to make a folder with no name');
    }
    $parentDirName = ($parentDirName)? rtrim($parentDirName, '/') . '/' : '';
    $absCurrentDir = self::getMediaDir(true).$parentDirName.$dirName;
    $absThumbDir   = $absCurrentDir.DIRECTORY_SEPARATOR.sfConfig::get('app_sfAssetsLibrary_thumbnail_dir', 'thumbnail');
    $mkdir_success = true;
    try
    {
      $old = umask(0);
      if (!is_dir($absCurrentDir))
      {
        mkdir($absCurrentDir, 0770);
      }
      if (!is_dir($absThumbDir))
      {
        mkdir($absThumbDir, 0770);
      }
      umask($old);
    }
    catch(sfException $e)
    {
      $mkdir_success = false;
    }

    return $mkdir_success;
  }

  /**
   * @param  string  $root
   * @return boolean
   */
  public static function deleteTree($root)
  {
    if (!is_dir($root))
    {
      return false;
    }
    foreach (glob($root.'/*', GLOB_ONLYDIR) as $dir)
    {
      if (!is_link($dir))
      {
        self::deleteTree($dir);
      }
    }

    return rmdir($root);
  }

  /**
   * @param  string $path
   * @param  string $filename
   * @param  string $thumbnailType
   * @param  string $fileSystem
   * @return string
   */
  public static function createAssetUrl($path, $filename, $thumbnailType = 'full', $fileSystem = true)
  {
    if ($thumbnailType == 'full')
    {
      return self::getMediaDir($fileSystem) . $path . DIRECTORY_SEPARATOR . $filename;
    }
    else
    {
      return self::getMediaDir($fileSystem) . self::getThumbnailDir($path) . $thumbnailType . '_' . $filename;
    }
  }

//  /**
//   * Retrieves a sfMedia object from a relative URL like
//   *    /medias/foo/bar.jpg
//   * i.e. the kind of URL returned by getAssetUrl($sf_media, 'full', false)
//   * @param  string  $url
//   * @return sfMedia
//   */
//  public static function getAssetFromUrl($url)
//  {
//
//    $url = str_replace(sfConfig::get('app_sfAssetsLibrary_upload_dir', 'media'), '', $url);
//    
//    $url = rtrim($url, '/');
//    $parts = explode('/', $url);
//    $filename = array_pop($parts);
//    $relPath = '/' . implode('/', $parts);
//
//    $c = new Criteria();
//    $c->add(sfMediaPeer::FILENAME, $filename);
//    $c->add(sfMediaPeer::REL_PATH, $relPath ?  $relPath : null);
//
//    return sfMediaPeer::doSelectOne($c);
//  }

  /**
   * @param  boolean $fileSystem
   * @return string
   */
  public static function getMediaDir($fileSystem = false)
  {
    return $fileSystem ? sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR : '/';
  }

  /**
   * Gives thumbnails folder for a folder
   *
   * @param  string $path
   * @return string
   */
  public static function getThumbnailDir($path, $separator = DIRECTORY_SEPARATOR)
  {
    $thumb_dir = $path . $separator . sfConfig::get('app_sfAssetsLibrary_thumbnail_dir', 'thumbnail');

    return rtrim($thumb_dir, $separator) . $separator;
  }

  /**
   * Get path of thumbnail
   * @param  string $path
   * @param  string $filename
   * @param  string $thumbnailType
   * @return string
   */
  public static function getThumbnailPath($path, $filename, $thumbnailType = 'full')
  {
    if ($thumbnailType == 'full')
    {
      return self::getMediaDir(true) . $path . DIRECTORY_SEPARATOR . $filename;
    }
    else
    {
      if (substr($filename, -4) == '.pdf')
      {
        $filename = substr($filename, 0, -3) . 'jpg';
      }

      return self::getMediaDir(true) . self::getThumbnailDir($path) . $thumbnailType . '_' . $filename;
    }
  }

  /**
   * Create the thumbnails for image assets
   * The numbe and size of thumbnails can be configured in the app.yml
   * The configuration accepts various formats:
   *   small: { width: 80, height: 80, shave: true }  // 80x80 shaved
   *   small: [80, 80, true]                          // 80x80 shaved
   *   small: [80]                                    // 80x80 not shaved
   * @param string $folder
   * @param string $filename
   * @param boolean $pdf
   */
  public static function createThumbnails($folder, $filename, $pdf = false)
  {
    $source = self::getThumbnailPath($folder, $filename, 'full');
    $thumbnailSettings = sfConfig::get('app_sfAssetsLibrary_thumbnails', array(
      'small' => array('width' => 84, 'height' => 84, 'shave' => true),
      'large' => array('width' => 194, 'height' => 152)
    ));
    foreach ($thumbnailSettings as $key => $params)
    {
      $width  = $params['width'];
      $height = $params['height'];
      $shave  = isset($params['shave']) ? $params['shave'] : false;
      if ($pdf)
      {
        self::createPdfThumbnail($source, self::getThumbnailPath($folder, $filename, $key), $width, $height, $shave);
      }
      else
      {
      self::createThumbnail($source, self::getThumbnailPath($folder, $filename, $key), $width, $height, $shave);
      }
    }
  }

  /**
   * Resize automatically an image
   * @param  string  $source
   * @param  string  $dest
   * @param  integer $width
   * @param  integer $height
   * @param  boolean $shave_all Recommanded when  "image source HEIGHT" < "image source WIDTH"
   * @return boolean
   */
  public static function createThumbnail($source, $dest, $width, $height, $shave_all = false)
  {
    if (class_exists('sfThumbnail') && file_exists($source))
    {
      if (sfConfig::get('app_sfAssetsLibrary_use_ImageMagick', false))
      {
        $adapter = 'sfImageMagickAdapter';
        $mime = 'image/jpg';
      }
      else
      {
        $adapter = 'sfGDAdapter';
        $mime = 'image/jpeg';
      }
      if ($shave_all)
      {
        $thumbnail  = new sfThumbnail($width, $height, false, true, 85, $adapter, array('method' => 'shave_all'));
      }
      else
      {
        list($w, $h, $type, $attr) = getimagesize($source);
        $newHeight = $width > 0 && $w > 0 ? ceil(($width * $h) / $w) : $height;
        $thumbnail = new sfThumbnail($width, $newHeight, true, true, 85, $adapter);
      }
        $thumbnail->loadFile($source);
        $thumbnail->save($dest, $mime);

        return true;
      }

    return false;
  }

  /**
   * Create thumbnail for a PDF file
   * @param  string  $source
   * @param  string  $dest
   * @param  integer $width
   * @param  integer $height
   * @param  boolean $shave_all Recommanded when  "image source HEIGHT" < "image source WIDTH"
   * @return boolean
   */
  public static function createPdfThumbnail($source, $dest, $width, $height, $shave_all = false)
  {
    if (class_exists('sfThumbnail') && sfConfig::get('app_sfAssetsLibrary_use_ImageMagick', false) && file_exists($source))
    {
      $adapter = 'sfImageMagickAdapter';
      $mime = 'image/jpg';
      if ($shave_all)
      {
        $thumbnail = new sfThumbnail($width, $height, false, false, 85, $adapter, array(
          'method'  => 'shave_all',
          'extract' => 0,
        ));
      }
      else
      {
        list($w, $h) = self::getPdfSize($source);
        $newHeight = $width > 0 && $w > 0 ? ceil(($width * $h) / $w) : $height;
        $thumbnail = new sfThumbnail($width, $newHeight, true, false, 85, $adapter, array('extract' => 0));
      }
      $thumbnail->loadFile($source);
      $thumbnail->save($dest, $mime, true);

        return true;
    }

    return false;
  }

  public static function getPdfSize($file)
  {
    if (sfConfig::get('app_sfAssetsLibrary_use_ImageMagick', false) == false)
    {
      throw new sfException('you must enable ImageMagick in configuration.');
    }
    $cmd = 'identify -format "%wx%h****" ' . escapeshellarg($file);
    exec($cmd, $output);
    $tmp = substr($output[0], 0, strpos($output[0], '****'));

    return explode('x', $tmp);
  }

  /**
   * @param  string $path
   * @return string
   */
  public static function getParent($path)
  {
    $dirs = explode('/', $path);
    array_pop($dirs);

    return join('/', $dirs);
  }

  /**
   * Splits a path into a basepath and a name
   *
   * @param  string $path
   * @param  string $separator
   * @return array             relative path and name
   */
  public static function splitPath($path, $separator = DIRECTORY_SEPARATOR)
  {
    $path = rtrim($path, $separator);
    $dirs = preg_split('/' . preg_quote($separator, '/') . '+/', $path);
    $name = array_pop($dirs);
    $relativePath =  implode($separator, $dirs);

    return array($relativePath, $name);
  }

  /**
   * @param string $message
   * @param string $color   "green", "red", or "yellow"
   */
  public static function log($message, $color = '')
  {
    switch ($color)
    {
      case 'green':
        $message = "\033[32m".$message."\033[0m\n";
        break;
      case 'red':
        $message = "\033[31m".$message."\033[0m\n";
        break;
      case 'yellow':
        $message = "\033[33m".$message."\033[0m\n";
        break;
      default:
        $message = $message . "\n";
    }
    fwrite(STDOUT, $message);
  }

}
