<?php $prefix = sprintf('%s%s', $sf_request->getUriPrefix(), $sf_request->getScriptName()) ?>

<h3>Récupération de 5 liens vers des MP3, par ordre décroissant de date de contribution, au format XSPF</h3>

<h4>Requête</h4>
<pre>GET <?php echo $prefix ?>/resources/link/groups/mp3/get?limit=5&sort_direction=desc&sort_field=contributed_at&format=xspf</pre>

<h4>Réponse</h4>
<pre><?php echo htmlentities('
<?xml version="1.0"?>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
  <trackList>
    <track>
      <location>http://marc.arette.free.fr/RADIO/ThisIsRadioclash_SPORT_Teaser.mp3</location>
    </track>
    <track>
      <location>http://www.glafouk.com/dlz/radioclash_astrotease.mp3</location>
    </track>
    <track>
      <location>http://www.morning-glories.net/freshpoulp/FPR047/02_Djose.mp3</location>
    </track>
    <track>
      <location>http://www.morning-glories.net/freshpoulp/FPR047/04_Space_Screwdriver.mp3</location>
    </track>
    <track>
      <location>http://www.morning-glories.net/freshpoulp/FPR047/07_Plan_americain.mp3</location>
    </track>
  </trackList>
</playlist>')
?></pre>

<h3>Récupération du premier lien posté vers une vidéo Youtube par l'utilisateur "mbertier", au format Json</h3>

<h4>Requête</h4>
<pre>GET <?php echo $prefix?>/resources/link/groups/youtube/get?limit=5&sort_direction=asc&sort_field=contributed_at&contributor_name=mbertier&format=json</pre>

<h4>Réponse</h4>
<pre><?php echo htmlentities('
[
  {
    "url":                 "http:\/\/www.youtube.com\/watch?v=LKu_QA8Bn9o",
    "domain_parent":       "youtube.com",
    "domain_fqdn":         "www.youtube.com",
    "mime_type":           null,
    "contributed_at":      "2007-05-09T21:22:05Z",
    "contributor_id":      2,
    "contributor_name":    "mbertier",
    "comment_id":          3088,
    "discussion_id":       299,
    "discussion_name":     "Mitch a un coup dans le nez"
  }
]')
?></pre>

<h3>Récupération de toutes les vidéos du topic "Des clips, des clips, rien que des clips", au format PHP sérialisé</h3>

<h4>Requête</h4>
<pre>GET <?php echo $prefix?>/resources/link/groups/youtube/get?limit=666666&discusion_id=1679&format=php</pre>

<h4>Réponse (tronquée)</h4>
<pre><?php echo htmlentities('a:6:{i:0;a:10:{s:3:"url";s:42:"http://www.youtube.com/watch?v=0a1VMkeGkZs";s:13:"domain_parent";s:11:"youtube.com";s:11:"domain_fqdn";s:15:"www.youtube.com";s:9:"mime_type";N;s:14:"contributed_at";s:20:"2006-09-19T19:20:36Z";s:14:"contributor_id";i:1;s:16:"contributor_name";s:5:"Johan";s:10:"comment_id";i:103;s:13:"discussion_id";i:26;s:15:"discussion_name";s:66:"Xerak - Clip VidÃ?Â©os (Pixel Monster, People Want My Sex, ...)";}i:1;a:10:{s:3:"url";s:42:"http://www.youtube.com/watch?v=rquumljYtSQ";s:13:"domain_parent";s:11:"youtube.com";s:11:"domain_fqdn";s:15:"www.youtube.com";s:9:"mime_type";N;s:14:"contributed_at";s:20:"2006-09-19T19:20:36Z";s:14:"contributor_id";i:1;s:16:"contributor_name";s:5:"Johan";s:10:"comment_id";i:103;s:13:"discussion_id";i:26;s:15:"discussion_name";s:66:"Xerak - Clip VidÃ?Â©os (Pixel Monster, People Want My Sex, ...)";}i:2;a:10:{s:3:"url";s:42:"http://www.youtube.com/watch?v=HWA-lw6SZ9g";s:13:"domain_parent";s:11:"youtube.com";s:11:"domain_fqdn";s:15:"www.youtube.com";s:9:"mime_type";N;s:14:"contributed_at";s:20:"2006-09-19T19:20:36Z";s:14:"contributor_id";i:1;s:16:"contributor_name";s:5:"Johan";s:10:"comment_id";i:103;s:13:"discussion_id";i:26;s:15:"discussion_name";s:66:"Xerak - Clip VidÃ?Â©os (Pixel Monster, People Want My Sex, ...)";}i:3;a:10:{s:3:"url";s:42:"http://www.youtube.com/watch?v=EzMHQLtbJJU";s:13:"domain_parent";s:11:"youtube.com";s:11:"domain_fqdn";s:15:"www.youtube.com";s:9:"mime_type";N;s:14:"contributed_at";s:20:"2006-09-19T19:20:36Z";s:14:"contributor_id";i:1;s:16:"contributor_name";s:5:"Johan";s:10:"comment_id";i:103;s:13:"discussion_id";i:26;s:15:"discussion_name";s:66:"Xerak - Clip VidÃ?Â©os (Pixel Monster, People Want My Sex, ...)";}i:4;a:10:{s:3:"url";s:42:"http://www.youtube.com/watch?v=P7_7j4Ud9UM";s:13:"domain_parent";s:11:"youtube.com";s:11:"domain_fqdn";s:15:"www.youtube.com";s:9:"mime_type";N;s:14:"contributed_at";s:20:"2006-09-19T19:20:36Z";s:14:"contributor_id";i:1;s:16:"contributor_name";s:5:"Johan";s:10:"comment_id";i:103;s:13:"discussion_id";i:26;s:15:"discussion_name";s:66:"Xerak - Clip VidÃ?Â©os (Pixel Monster, People Want My Sex, ...)";}i:5;a:10:{s:3:"url";s:42:"http://www.youtube.com/profile?user=xerak2";s:13:"domain_parent";s:11:"youtube.com";s:11:"domain_fqdn";s:15:"www.youtube.com";s:9:"mime_type";N;s:14:"contributed_at";s:20:"2006-09-19T19:20:36Z";s:14:"contributor_id";i:1;s:16:"contributor_name";s:5:"Johan";s:10:"comment_id";i:103;s:13:"discussion_id";i:26;s:15:"discussion_name";s:66:"Xerak - Clip VidÃ?Â©os (Pixel Monster, People Want My Sex, ...)";}}') ?></pre>