<div class="form-row">
  <?php echo image_tag('/sfDoctrineAssetsLibraryPlugin/images/magnifier.png', 'alt=search align=top') ?>
  <?php echo link_to(__('Search', null, 'sfAsset'), '@sf_asset_library_search', array('class' => 'toggle', 'rel' => '{ div: \'sf_asset_search_form\'}')) ?>
</div>

<div id="sf_asset_search_form" style="display:none">
  <form action="<?php echo url_for('@sf_asset_library_search') ?>" method="get" id="sf_asset_search">

    <?php echo $form ?>

    <ul class="sf_admin_actions">
      <li>
        <input type="submit" value="<?php echo __('Search', null, 'sfAsset') ?>" name="search" class="sf_admin_action_filter" />
      </li>
    </ul>

  </form>
</div>