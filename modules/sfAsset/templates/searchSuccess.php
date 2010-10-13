<?php use_helper('sfAsset', 'Date', 'I18N') ?>

<h1><?php echo __('Search results', null, 'sfAsset') ?></h1>

<?php if (!$popup) : ?>
  <?php include_partial('search_header') ?>
<?php endif ?>

<div id="sf_asset_bar">
  <p><?php echo link_to(__('Back to the list', null, 'sfAsset'), '@sf_asset_library_list') ?></p>
  <?php include_partial('sfAsset/sidebar_sort') ?>
  <?php include_partial('sfAsset/sidebar_search', array('form' => $filterform)) ?>
</div>

<div id="sf_asset_container">
  <?php foreach ($pager->getResults() as $sf_asset): ?>
    <div class="search_result" style="clear:left">
      <?php include_partial('sfAsset/asset', array('sf_asset' => $sf_asset)) ?>
      <div class="details">
        <?php echo assets_library_breadcrumb($sf_asset->getFolderPath(ESC_RAW), true, 'list') ?><?php echo link_to_asset_action($sf_asset->getFilename(), $sf_asset) ?><br />
        <?php if ($description = $sf_asset->getDescription(ESC_RAW)): ?>
          <?php echo $description ?><br />
        <?php endif ?>
        <?php if ($copyright = $sf_asset->getCopyright(ESC_RAW)): ?>
          <?php echo __('Copyright: %copyright%', array('%copyright%' => $copyright), 'sfAsset') ?><br />
        <?php endif ?>
        <?php if ($author = $sf_asset->getAuthor(ESC_RAW)): ?>
          <?php echo __('Author: %author%', array('%author%' => $author), 'sfAsset') ?><br />
        <?php endif ?>
        <?php echo __('Created on %date%', array('%date%' => format_date($sf_asset->getCreatedAt('U'))), 'sfAsset') ?>
      </div>
    </div>
  <?php endforeach ?>
</div>

<div class="sf_asset_pager" style="clear: both">
<?php if ($pager->haveToPaginate(ESC_RAW)): ?>
  <?php if ($pager->getPage(ESC_RAW) != 1): ?>
    <?php echo link_to(__('< Previous page', null, 'sfAsset'), '@sf_asset_library_search?page='.$pager->getPreviousPage()) ?>
  <?php endif ?>
  <?php echo __('Page %number% on %total%', array('%number%' => $pager->getPage(), '%total%' => $pager->getLastPage()), 'sfAsset') ?>
  <?php if ($pager->getPage() != $pager->getLastPage()): ?>
    <?php echo link_to(__('Next page >', null, 'sfAsset'), '@sf_asset_library_search?page='.$pager->getNextPage()) ?>
  <?php endif ?>
<?php endif ?>
</div>

<?php if (!$popup) : ?>
  <?php include_partial('sfAsset/search_footer') ?>
<?php endif ?>