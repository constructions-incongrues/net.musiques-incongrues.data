$(document)
    .ready(
        function() {
          $('#toc').toc( {
            context : '#documentation-container',
            exclude : 'h1, h4, h5, h6'
          });

          if (typeof sh_highlightDocument == 'function') {
            sh_highlightDocument();
          }

          $('.help').click(function() {
        	 $('#help').load($(this).attr('href') + ' ' + $(this).attr('x-fragment'));
        	 return false;
          });
          
          $('#ananas')
              .click(
                  function() {
                    var uri = '/collections/links/segments/images/get?limit=1&sort_field=random&format=json';
                    $
                        .get(
                            uri,
                            null,
                            function(data) {
                              var discussion_name = makeSlug(data[0].discussion_name);
                              url_discussion = 'http://www.musiques-incongrues.net/forum/discussion/'
                                  + data[0].discussion_id
                                  + '/'
                                  + discussion_name + '/';
                              $('#url').html(
                                  '<a href="' + url_discussion
                                      + '" title="Extrait de la discussion '
                                      + data[0].discussion_name
                                      + '"><img src="' + data[0].url
                                      + '" /></a>');
                            });

                    $('#url').show();
                    $("#ananas").fadeOut(800).fadeIn(800);
                  });
        });
