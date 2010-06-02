<ul>
  <?php foreach ($groups as $group): ?>
  	<li><?php echo link_to($group, sprintf('@resources_type_group_formats?type=%s&group=%s', $sf_request->getParameter('type'), $group)) ?></li>
  <?php endforeach; ?>
</ul>