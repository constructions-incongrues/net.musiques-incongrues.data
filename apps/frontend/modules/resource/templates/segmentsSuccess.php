<style>
div#toc ul {
    list-style: none;
}
div#toc ul li ul {
    margin-bottom: 0.75em;
}
div#toc ul li ul li ul {
    margin-bottom: 0.25em;
}
</style>
<div id="documentation-container">
<h2 id="documentation">Documentation de la collection</h2>

<div id="toc"></div>


  <h3 id="schema">Schema</h3>
  <?php include_partial(sprintf('resource/documentation/%s/schema', $sf_request->getParameter('collection'))) ?>

  <h3 id="segments">Segments disponibles</h3>

  <?php foreach ($segments as $segment): ?>
    <h4 id="segment-<?php echo $segment?>"><?php echo $segment ?></h4>
    <?php include_partial(sprintf('resource/documentation/%s/segment/%s', $sf_request->getParameter('collection'), $segment)); ?>
    <p>URL de requête :</p>
    <pre>GET <?php echo url_for(sprintf('@resources_collection_segment_get?collection=%s&segment=%s', $sf_request->getParameter('collection'), $segment), true) ?></pre>
  <?php endforeach; ?>


  <h2 id="examples">Exemples</h2>
  <?php include_partial(sprintf('resource/documentation/%s/cookbook', $sf_request->getParameter('collection'))); ?>
</div>