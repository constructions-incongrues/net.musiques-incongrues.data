$(document).ready(function() {
	var regexImages = /\.(jpg|jpeg|gif|png|tif?|pict|bmp)($|\\?)/i;
	$('a.oembed').each(function() {
		if ($(this).attr('href').match(regexImages)) {
			$(this).replaceWith($('<img src="'+$(this).attr('href')+'" />'));
		} else {
			$(this).oembed($(this).attr('href'), {
				'maxWidth': '210px',
				'embedMethod': 'fill'
			});
		}
	});

$(div.results).imagesLoaded(function() {
	$('#resources').masonry({
	    // options
	    itemSelector : '.item',
	    columnWidth : 430
	  });
	});
});
