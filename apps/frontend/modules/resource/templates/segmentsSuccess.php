<h2>Documentation de la collection</h2>
<h3>Schema</h3>
<?php include_partial(sprintf('resource/documentation/%s/schema', $sf_request->getParameter('collection'))) ?>

<h2>Segments</h2>

<?php foreach ($segments as $segment): ?>
  <h3><?php echo $segment ?></h3>
  <?php include_partial(sprintf('resource/documentation/%s/segment/%s', $sf_request->getParameter('collection'), $segment)); ?>
  <?php echo link_to('Accéder au groupe', sprintf('@resources_collection_segment_formats?collection=%s&segment=%s', $sf_request->getParameter('collection'), $segment)) ?>
<?php endforeach; ?>


<h2>Exemples</h2>
<?php include_partial(sprintf('resource/documentation/%s/cookbook', $sf_request->getParameter('collection'))); ?>

<h2>TODO : Essayer </h2>
<!-- jquery live tester -->