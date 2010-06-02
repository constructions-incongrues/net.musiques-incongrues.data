<p>Found <?php echo count($results) ?> results :</p>
<ul>
<?php foreach ($results as $resource): ?>
  <li><pre><?php echo print_r($resource, true) ?></pre></li>
<?php endforeach; ?>
</ul>