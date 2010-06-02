<h2>Documentation de la collection</h2>
<p>TODO. Présentation du schema et des paramètres transverses.</p>

<h2>Groupes de resources</h2>

<?php foreach ($groups as $group): ?>
  <h3><?php echo $group ?></h3>
  <?php include_partial(sprintf('resource/documentation/%s/group/%s', $sf_request->getParameter('type'), $group)); ?>
  <?php echo link_to('Accéder au groupe', sprintf('@resources_type_group_formats?type=%s&group=%s', $sf_request->getParameter('type'), $group)) ?>
<?php endforeach; ?>


<h2>Exemples</h2>
<?php include_partial(sprintf('resource/documentation/%s/cookbook', $sf_request->getParameter('type'))); ?>

<h2>TODO : Essayer </h2>
<!-- jquery live tester -->