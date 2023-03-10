var FontDetect = function(swfId, swfLocation, onReady) {
  this._swfId = swfId;
  this._swfObjectId = swfId;
  this._swfLocation = swfLocation;
  this._onReady = onReady;
  this._fallbackWidthCache = null;
  this.loadSWF();
}

var FontDetectGlobal = (function() {
  var instance = {};
  return {
    register: function(id, object) {
      instance[id] = object;
    },
    remove: function(id) {
      var object = instance[id];
      instance[id] = null;
      return object;
    }
  };
})();

FontDetect.prototype = {
  loadSWF: function() {
    var flashvars = { onReady: "onFontDetectReady", swfObjectId: this._swfObjectId };
    var params = { allowScriptAccess: "always", menu: "false" };
    var attributes = { id: this._swfObjectId, name: this._swfObjectId };
    swfobject.embedSWF(this._swfLocation, this._swfId, "1", "1", "9.0.0", false, flashvars, params, attributes);
    FontDetectGlobal.register(this._swfObjectId, this);
    $(document).bind('swfLoaded', function(event, id) {
      var fontDetect = FontDetectGlobal.remove(id);
      fontDetect._onReady(fontDetect);
    });
  },
  checkOffsetWidth: function(family, size) {
    var node = document.createElement("p");
    $(node).css("font-family", "'" + family + "', Times New Roman");
    $(node).css("font-size", size);
    $(node).css("display", "inline");
    $(node).addClass("font-test")
    $(node).html("mmmmmmmmml");
    $("body").append(node);
    var width = node.offsetWidth;
    $("body p.font-test").remove();
    return width;
  },
  fallbackWidth: function() {
    if (!this._fallbackWidthCache) this._fallbackWidthCache = this.checkOffsetWidth("Times New Roman", "120px");
    return this._fallbackWidthCache;
  },
  checkFont: function(family) {
    if (family == "Times New Roman") return true;
    if (/\d+pt\s*$/.test(family)) return false;
    var familyWidth = this.checkOffsetWidth("'" + family + "', Times New Roman", "120px");
    return (familyWidth != this.fallbackWidth());
  },
  filterFonts: function(fonts) {
    var filtered = [];
    for (var i = 0, length = fonts.length; i < length; i++) {
      if (this.checkFont(fonts[i].fontName))
        filtered.push(fonts[i]);
    }
    return filtered;
  },
  fonts: function() {
    var swfElement = document.getElementById(this._swfObjectId);
    var fonts = swfElement.fonts();
    return this.filterFonts(fonts);
  }
};
var onFontDetectReady = function(swfObjectId) {
  $(document).trigger('swfLoaded', [ swfObjectId ]);
};