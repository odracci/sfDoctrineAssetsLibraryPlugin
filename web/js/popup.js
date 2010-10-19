function setImageField(url, thumbUrl, id)
{
  var win = null;
  if (typeof tinyMCEPopup != 'undefined')
  {
    var win = tinyMCEPopup.getWindowArg('window');
  }

  if (win)
  {
    if (tinyMCEPopup.getWindowArg('type') == 'image' && win.ImageDialog.showPreviewImage)
    {
      win.ImageDialog.showPreviewImage(url);
    }

    win.document.getElementById(tinyMCEPopup.getWindowArg('input')).value = url;
    tinyMCEPopup.close();
  }
  else
  {
    if (!opener)
    {
      opener = window.opener;
    }
    opener.sfAssetsLibrary.fileBrowserReturn(url, thumbUrl, id);
    window.close();
  }
}

// XXX unused...
function addImageField(url, id)
{
  if (!opener)
  {
    opener = window.opener;
  }
  opener.sfAssetsLibrary.fileBrowserAdd(url, id);
}
