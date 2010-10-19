sfAssetUtil = {

  load: function()
  {
    var links = sfAssetUtil.getElementsByClassName('toggle');
    if (!links)
    {
      return;
    }
    for (var i = 0; i < links.length; i ++)
    {
      sfAssetUtil.addEvent(links[i], 'click', function(e) {
        sfAssetUtil.toggle(e);
        sfAssetUtil.prevDef(e);
        sfAssetUtil.stopProp(e);
      }, false);
    }
  },

  toggle: function(e)
  {
    var link = sfAssetUtil.getTarget(e);
    eval('var rel = ' + link.getAttribute('rel'));
    var div = document.getElementById(rel.div);
    if (div)
    {
      if (div.style.display != 'none' )
      {
        div.style.display = 'none';
      }
      else
      {
        div.style.display = '';
      }
    }
  },

  getElementsByClassName: function(cl)
  {
    var retnode = [];
    var myclass = new RegExp('\\b'+cl+'\\b');
    var elem = document.getElementsByTagName('*');
    for (var i = 0; i < elem.length; i++)
    {
      var classes = elem[i].className;
      if (myclass.test(classes)) retnode.push(elem[i]);
    }
    return retnode;
  },

  // x-browser event listener
  addEvent : function(el, eType, fn, uC)
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

  getTarget: function(e)
  {
    var targ;
    if (e.target) targ = e.target;
    else if (e.srcElement) targ = e.srcElement;
    if (targ.nodeType == 3) // defeat Safari bug
      targ = targ.parentNode;

    return targ;
  },

  // x-browser stop propagation
  stopProp : function(e)
  {
    if (e && e.stopPropogation) e.stopPropogation();
    else if (window.event && window.event.cancelBubble)
    window.event.cancelBubble = true;
  },

  // x-browser prevent default
  prevDef : function(e)
  {
    if (e && e.preventDefault) e.preventDefault();
    else if (window.event && window.event.returnValue)
    window.eventReturnValue = false;
  }

}

window.onload = sfAssetUtil.load;