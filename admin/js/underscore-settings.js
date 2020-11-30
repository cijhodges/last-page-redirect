(function() {
  _.templateSettings = {
	  evaluate : /\{\[([\s\S]+?)\]\}/g,
	  interpolate: /\{{3}([\s\S]+?)\}{3}/,
	  escape: /\{\{([^\{\}]+?)(?!\}\}\})\}\}/
  };
});
