<p>Displaying <?php echo count($results) ?> out of <?php echo $results['num_found']?> found results :</p>
<ul>
<?php foreach ($results as $resource): ?>
  <li><pre><?php echo print_r($resource, true) ?></pre></li>
<?php endforeach; ?>
</ul>