var sfAssetsLibrary_Engine = function(){};

sfAssetsLibrary_Engine.prototype = {
  init: function(url)
  {
    this.url = url;
  },

  load: function()
  {
    var asset_url = document.getElementById('sf_asset_js_url');
    if (asset_url)
    {
      var url = asset_url.firstChild.data
      this.url = url;
    }
    var asset_input = document.getElementById('sf_asset_input_image');
    if (asset_input)
    {
      eval('var rel = ' + asset_input.getAttribute('rel'));
      var fname = asset_input.previousSibling.form.name;
      sfAssetsLibrary.addEvent(asset_input, 'click', function(e) {
        sfAssetsLibrary.openWindow({
          form_name: fname,
          field_name: rel.name,
          type: rel.type,
          url: rel.url,
          scrollbars: 'yes'
        });
        sfAssetsLibrary.prevDef(e);
        sfAssetsLibrary.stopProp(e);
      }, false);
    }
  },

  fileBrowserReturn: function(url, thumbUrl, id)
  {
    if (this.isTinyMCE)
    {
      tinyMCE.setWindowArg('editor_id', this.fileBrowserWindowArg);
      if (this.fileBrowserType == 'image')
      {
        this.callerWin.showPreviewImage(url);
      }
    }
    this.callerWin.document.forms[this.callerFormName].elements[this.callerFieldName].value = id;
    var callerId = this.callerWin.document.forms[this.callerFormName].elements[this.callerFieldName].id;
    var img = this.callerWin.document.getElementById(callerId + '_img');
    if (img)
    {
      img.src = thumbUrl;
    }
  },

  // tried to do multiple adds.
  // not working: can't add DOM elements in a window different from current one :-|
  fileBrowserAdd: function(url, id)
  {
    var ul = this.callerWin.document.getElementById('multiassets');
    if (!ul)
    {
      return;
    }
    var li = this.callerWin.document.createElement('li');
    var img = this.callerWin.document.createElement('img');
    img.setAttribute('src', url);
    li.appendChild(img);
    ul.appendChild(ul);
  },

  fileBrowserCallBack: function(field_name, url, type, win)
  {
    if (!this.url)
    {
      this.load();
      if (!this.url)
      {
        alert('error in getting asset url');
        return;
      }
    }

    var params = type == 'image' ? 'images_only=1&tiny=1' : 'tiny=1';
    tinyMCE.activeEditor.windowManager.open({
      file :      sfAssetsLibrary.addParams(this.url, params),
      title:      'Assets',
      width :     550,
      height :    600,
      inline:     'yes',
      resizable : 'yes',
      scrollbars: 'yes'
    },
    {
      input:      field_name,
      type:       type,
      window:     win
    });

    return false;
  },

  openWindow: function(options)
  {
    var width, height, x, y, resizable, scrollbars, url;

    if (!options) return;
    if (!options['field_name']) return;
    if (options['url'])
    {
      this.url = options['url'];
    }
    else if (!this.url)
    {
      return;
    }
    this.callerWin = self;
    this.callerFormName = (options['form_name'] == '') ? 0 : options['form_name'];
    this.callerFieldName = options['field_name'];
    this.fileBrowserType = options['type'];
    url = this.url;

    if (options['type'] == 'image') url = sfAssetsLibrary.addParams(url, 'images_only=1');
    if (!(width = parseInt(options['width']))) width = 1000;
    if (!(height = parseInt(options['height']))) height = 600;

    // Add to height in M$ due to SP2 WHY DON'T YOU GUYS IMPLEMENT innerWidth of windows!!
    if (sfAssetsLibrary.isMSIE)
      height += 40;
    else
      height += 20;

    x = parseInt(screen.width / 2.0) - (width / 2.0);
    y = parseInt(screen.height / 2.0) - (height / 2.0);

    resizable = (options && options['resizable']) ? options['resizable'] : "no";
    scrollbars = (options && options['scrollbars']) ? options['scrollbars'] : "no";

    var modal = (resizable == "yes") ? "no" : "yes";

    if (sfAssetsLibrary.isGecko && sfAssetsLibrary.isMac) modal = "no";

    if (options['close_previous'] != "no") try {sfAssetsLibrary.lastWindow.close();} catch (ex) {}

    var win = window.open(url, "sfPopup" + new Date().getTime(), "top=" + y + ",left=" + x + ",scrollbars=" + scrollbars + ",dialog=" + modal + ",minimizable=" + resizable + ",modal=" + modal + ", width=1000, height=600,resizable=" + resizable);
    this.fileBrowserWin = win;
    if (options['close_previous'] != "no") sfAssetsLibrary.lastWindow = win;

    win.focus();
  },

  addParams: function(url, params)
  {
    return url.indexOf('?') > 0 ? url + '&' + params : url + '?' + params;
  },

  // x-browser event listener
  addEvent: function(el, eType, fn, uC)
  {
    if (el.addEventListener)
    {
      el.addEventListener(eType, fn, uC);
      return true;
    }
    else if (el.attachEvent)
    {
      return el.attachEvent('on' + eType, fn);
    }
    else
    {
      el['on' + eType] = fn;
    }
  },

  // x-browser stop propagation
  stopProp: function(e)
  {
    if (e && e.stopPropogation) e.stopPropogation();
    else if (window.event && window.event.cancelBubble)
    window.event.cancelBubble = true;
  },

  // x-browser prevent default
  prevDef: function(e)
  {
    if (e && e.preventDefault) e.preventDefault();
    else if (window.event && window.event.returnValue)
    window.eventReturnValue = false;
  }

}

var sfAssetsLibrary = new sfAssetsLibrary_Engine();

window.onload = sfAssetsLibrary.load;
