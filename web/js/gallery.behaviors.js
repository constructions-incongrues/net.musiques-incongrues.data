$(document).ready(function() {
	var regexImages = /\.(jpg|jpeg|gif|png|tif?|pict|bmp)($|\\?)/i;
	var regexAudio = /\.(mp3|ogg)/i;
	$('a.oembed').each(function() {
		if ($(this).attr('href').match(regexImages)) {
			$(this).replaceWith($('<img src="'+$(this).attr('href')+'" />'));
		} else if ($(this).attr('href').match(regexAudio)) {
			$(this).replaceWith($('<a href="'+$(this).attr('href')+'">'+$(this).attr('href').replace(/^.*[\/\\]/g, '')+'</a><br /><audio controls="controls"><source src="'+$(this).attr('href')+'" /></audio>'));
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
