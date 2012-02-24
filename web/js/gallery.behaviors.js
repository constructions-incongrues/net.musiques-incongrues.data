$(document).ready(function() {
	var regexImages = /\.(jp?g|gif|png|tif?|pict|bmp)($|\\?)/i;
	$('a.oembed').each(function() {
		if ($(this).attr('href').match(regexImages)) {
			$(this).replaceWith($('<img src="'+$(this).attr('href')+'" />'));
		} else {
			$(this).oembed();
		}
	});
})