<?php use_helper('Text') ?>
<?php $hiddenParameters = array('action', 'sf_format', 'collection', 'segment', 'module') ?>
<div class="results html">
	<p style="text-align: center;">Displaying <strong><?php echo count($results) - 1  ?></strong> out of <strong><?php echo $results['num_found']?></strong> found results</p>
	<hr />
	<dl>
		<dt>Critères de recherche</dt>
		<?php $parameters = $sf_request->getParameterHolder()->getAll() ?>
		<?php foreach (array_keys($sf_request->getParameterHolder()->getAll()) as $paramName => $paramValue): ?>
			<?php if (!in_array($paramValue, $hiddenParameters)): ?>
				<dd><strong><?php echo $paramValue ?> :</strong> <?php echo $parameters[$paramValue] ?></dd>
			<?php endif; ?>
		<?php endforeach; ?>
	</dl>
	<hr />
	<?php if (count($results) > 1): ?>
		<?php foreach ($results as $resource): ?>
			<?php if (is_array($resource)): ?>
			  <dl>
			  	<dt><?php echo auto_link_text($resource['url']) ?></dt>
			  	<?php foreach ($resource as $propName => $propValue): ?>
			  	<dd><strong><?php echo $propName ?> :</strong> <a href="<?php echo url_for(sprintf('@resources_collection_segment_get?collection=%s&segment=%s', $collection, $segment)) ?>?<?php echo urlencode($propName) ?>=<?php echo urlencode($propValue) ?>"><?php echo utf8_decode($propValue)?> </a></dd>
			  	<?php endforeach; ?>
			  </dl>
			  <?php endif; ?>
		<?php endforeach; ?>
	<?php else: ?>
		<p style="text-align: center;">No results. DARN !</p>
	<?php endif; ?>
</div>