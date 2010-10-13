<ul class="sf_admin_actions">
  <li class="sf_admin_action_list">
    <?php echo link_to(__('Back to the list', null, 'sfAsset'), '@sf_asset_library_list') ?>
  </li>
  <li>
    <input type="submit" class="sf_admin_action_save" value="<?php echo __(empty($button) ? 'Save' : $button, null, 'sfAsset') ?>" />
  </li>
</ul>
