<ul>
  <?php foreach ($types as $type): ?>
  	<li><?php echo link_to(ucfirst($type), '@resources_type?type='.$type) ?></li>
  <?php endforeach; ?>
</ul>