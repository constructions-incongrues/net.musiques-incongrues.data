<h3 id="howto-segment">Choix du segment</h3>
<p>
  La première chose à faire est de choisir le <a href="#segments">segment</a> à requêter. Cela permet d'obtenir l'URL de requêtage.
  Par exemple, l'URL de requêtage du segment <a href="#segments-images">images</a> est 
</p>
<pre>GET <?php echo url_for('@resources_collection_segment_get?collection=link&segment=images', true) ?></pre>

<h3 id="howto-response">Définition de la forme de la réponse</h3>
<h4 id="howto-response-limit">Limitation du nombre d'enregistrements</h4>
<p>
  Par défaut, le service renvoie 50 enregistrements.
  Il faut utiliser le paramètre <a href="#common-parameters-limit">limit</a>.
  Par exemple, pour récupérer trois enregistrements :
</p>
<pre>GET <?php echo url_for('@resources_collection_segment_get?collection=link&segment=images', true) ?>?<strong>limit=3</strong></pre>

<h4 id="howto-response-sort">Définition du mode de tri</h4>
<p>
  Le tri est conditionné par les paramètres <a href="#common-parameters-sort_field">sort_field</a> et <a href="#common-parameters-sort_direction">sort_direction</a>.
  Par défaut, les enregistrements sont triés par <a href="#schema-contributed_at">date de contribution</a> croissante.
  Ainsi, pour trier les enregistrement par <a href="#schema-discussion_id">identifiant de discussion</a> décroissant :
</p>
<pre>GET <?php echo url_for('@resources_collection_segment_get?collection=link&segment=images&limit=3', true) ?>&amp;<strong>sort_field=discussion_id</strong>&amp;<strong>sort_direction=desc</strong></pre>

<h4 id="howto-response-format">Définition du format de réponse</h4>
<p>
  C'est le paramètre <a href="#common-parameters-format">format</a> qui permet de définir le format de la réponse.
  Le format par défaut est <a href="#formats-html">HTML</a>.
  Pour obtenir la réponse au format JSON :
</p>
<pre>GET <?php echo url_for('@resources_collection_segment_get?collection=link&segment=images&limit=3&sort_field=discussion_id&sort_direction=desc', true) ?>&amp;<strong>format=json</strong></pre>

<h4 id="howto-filter">Filtrage en fonction des attributs</h4>
<p>
  Il est enfin possible de restreindre le jeu d'enregistrements retourné en fonction de valeurs de leurs attributs <a href="#schema">schema</a>.
  Par exemple, pour limiter les enregistrements retournés au images contribuées par l'utilisateur "mbertier" :
</p>
<pre>GET <?php echo url_for('@resources_collection_segment_get?collection=link&segment=images&limit=3&sort_field=discussion_id&sort_direction=desc&format=json', true) ?>&amp;<strong>contributor_name=mbertier</strong></pre>

<p>La réponse à cette requête :</p>
<pre id="howto-response-test">
  <!-- Data is loaded using jQuery -->
</pre>
<script type="text/javascript">
$('#howto-response-test')
    .load('<?php echo url_for('@resources_collection_segment_get?collection=link&segment=images&limit=3&sort_field=discussion_id&sort_direction=desc&format=json&contributor_name=mbertier') ?>');
</script>
<p>
  Il est possible d'ajouter de requêter plusieurs attributs. Dans ce cas la requête est de type ET.
</p>