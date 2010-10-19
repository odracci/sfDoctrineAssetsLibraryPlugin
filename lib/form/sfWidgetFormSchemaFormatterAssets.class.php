<?php

/**
 * formatter for mass upload form
 * 
 * @package    symfony
 * @subpackage form
 * @author     Massimiliano Arione <garakkio@gmail.com>
 */

class sfWidgetFormSchemaFormatterAssets extends sfWidgetFormSchemaFormatter
{

  protected
    $rowFormat       = "<div class=\"form-row\">\n%label%\n%error% %field%%help%%hidden_fields%\n</div>",
    $errorRowFormat  = "<div class=\"errors\">\n%errors%\n</div>",
    $errorListFormatInARow = '%errors% ',
    $errorRowFormatInARow  = '%error% ',
    $helpFormat      = '<br />%help%',
    $decoratorFormat = '%content%';

  /**
   * Constructor
   *
   * @param sfWidgetFormSchema $widgetSchema
   */
  public function __construct(sfWidgetFormSchema $widgetSchema)
  {
    $this->setTranslationCatalogue('sfAsset');
    parent::__construct($widgetSchema);
  }

}

?>
