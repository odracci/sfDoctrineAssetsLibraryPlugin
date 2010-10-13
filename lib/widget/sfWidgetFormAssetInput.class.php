<?php

/**
 * sfWidgetFormAssetInput
 *
 * @package    symfony
 * @subpackage widget
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */
class sfWidgetFormAssetInput extends sfWidgetFormInputHidden
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * asset_type: The asset type ('all' for all types)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('asset_type', 'image');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetFormInput
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfProjectConfiguration::getActive()->loadHelpers('sfAsset');
    init_asset_library();

    $html = parent::render($name, $value, $attributes, $errors);
    $attributes = $this->fixFormId(array('name' => $name));
    $html .= input_sf_asset_image_tag($name, $value, array('id' => $attributes['id'], 'type' => $this->getOption('asset_type')));

    return $html;
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavascripts()
  {
    return array('/sfAssetsLibraryPlugin/js/main');
  }

  /**
   * this is needed for correct rendering in admin generator
   */
  public function isHidden()
  {
    return false;
  }
}