<?php use_helper('Text') ?>
<div class="results html">
	<p>Displaying <?php echo count($results) - 1  ?> out of <?php echo $results['num_found']?> found results :</p>
	<?php foreach ($results as $resource): ?>
	  <dl>
	  	<dt><?php echo auto_link_text($resource['url']) ?></dt>
	  	<?php foreach ($resource as $propName => $propValue): ?>
	  	<dd><strong><?php echo $propName ?> :</strong> <?php echo utf8_decode(auto_link_text($propValue)) ?></dd>
	  	<?php endforeach; ?>
	  </dl>
	<?php endforeach; ?>
</div>