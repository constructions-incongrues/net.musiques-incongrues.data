<p>Chaque requête peut renvoyer une réponse dans un des formats suivants.</p>

<div id="formats-html">
	<h4>html</h4>
	<p>Les résultats sont renvoyés au format HTML. Ce format est principalement destiné à la consultation des ressources par des êtres humains et au déboggage.</p>
	<dl>
	  <dt>Caractéristiques</dt>
	  <dd>Type MIME : <code>text/html</code></dd>
	</dl>
</div>

<div id="formats-json">
	<h4>json</h4>
	<p>Les résultats sont renvoyés au format <a href="http://json.org/">JSON</a>.</p>
	<dl>
	  <dt>Caractéristiques</dt>
	  <dd>Type MIME : <code>application/json</code></dd>
	</dl>
</div>

<div id="formats-plain">
	<h4>plain</h4>
	<p>Les résultats sont renvoyés au format plein texte, une URL par lige.</p>
		<dl>
		  <dt>Caractéristiques</dt>
		  <dd>Type MIME : <code>text/plain</code></dd>
		  <dt>Utilisation</dt>
		  <dd>
		  	Ce format se combine facilement avec des outils en ligne de commande.
		  	Par exemple, pour télécharger les dix dernières images postées par l'utilisateur "mbertier" :
		  	<p>
		  		<code>
		  		wget `GET "http://data.musiques-incongrues.net/collections/links/segments/images/get?contributor=mbertier&limit=10&format=plain"` | xargs
		  		</code>
		  	</p>
		  </dd>
		</dl>
</div>

<div id="formats-php">
	<h4>php</h4>
	<p>Les résultats sont renvoyés au format <a href="http://www.php.net/serialize">PHP sérialisé</a>.</p>
	<dl>
	  <dt>Caractéristiques</dt>
	  <dd>Type MIME : <code>application/vnd.php.serialized</code></dd>
	</dl>
</div>

<div id="formats-rss">
	<h4>rss</h4>
	<p>Les résultats sont renvoyés au format RSS.</p>
	<dl>
	  <dt>Caractéristiques</dt>
	  <dd>Type MIME : <code>application/rss+xml</code></dd>
	</dl>
</div>

<div id="formats-xspf">
	<h4>xspf</h4>
	<p>Les résultats sont renvoyés au format <a href="http://xspf.org/">XSPF</a>.</p>
	<dl>
	  <dt>Caractéristiques</dt>
	  <dd>Type MIME : <code>application/xspf+xml</code></dd>
	</dl>
</div>