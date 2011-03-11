<?php use_helper('Text') ?>
<?php $hiddenParameters = array('action', 'sf_format', 'collection', 'segment', 'module') ?>
<?php $inSearch = array_keys($sf_request->getParameterHolder()->getAll()) ?>

<h2>Collection : <?php echo $sf_request->getParameter('collection') ?> / Segment : <?php echo $sf_request->getParameter('segment') ?></h2>

<div class="results html">
	<p style="text-align: center;">Vous consultez les résultats <strong><?php echo $sf_request->getParameter('start', 0) ?></strong> à <strong><?php echo $sf_request->getParameter('start', 0) + 50 ?></strong> parmi les  <strong><?php echo $results['num_found']?></strong> trouvés</p>
	<p style="text-align: center;">
	  <?php if ($sf_request->getParameter('start', 0) > 0): ?>
	    <a style="font-size: 2em;" href="<?php echo $pagination['urlPrevious']?>" title="Résultats précédents">&larr;</a>
	  &nbsp;&nbsp;
	  <?php endif; ?>
	  <?php if ($sf_request->getParameter('start', 0) + 50 < $results['num_found'] ): ?>
	  <a style="font-size: 2em;" href="<?php echo $pagination['urlNext'] ?>" title="Résultats suivants">&rarr;</a>
	  <?php endif; ?>
	</p>
	<hr />
	<dl>
		<dt>Critères de recherche (<a href="<?php echo url_for(sprintf('@resources_collection?collection=%s#segments-%s', $sf_request->getParameter('collection'), $sf_request->getParameter('segment'))) ?>" title="Consulter la documentation du segment <?php echo $sf_request->getParameter('segment') ?>">?</a>)</dt>
		<?php $parameters = $sf_request->getParameterHolder()->getAll() ?>
		<?php foreach (array_keys($sf_request->getParameterHolder()->getAll()) as $paramName => $paramValue): ?>
			<?php if (!in_array($paramValue, $hiddenParameters)): ?>
				<dd class="<?php in_array($paramValue, $inSearch) ? 'in_search' : '' ?>"><strong><?php echo $paramValue ?> :</strong> <?php echo $parameters[$paramValue] ?></dd>
			<?php endif; ?>
		<?php endforeach; ?>
	</dl>
	<dl>
		<dt>Autres formats</dt>
		<?php foreach ($urlsFormats as $format => $urlFormat): ?>
		<dd><a href="<?php echo $urlFormat ?>" title="Obtenir les mêmes résultats au format <?php echo ucfirst($format) ?> format"><?php echo ucfirst($format) ?></a></dd>
		<?php endforeach; ?>
	</dl>
	<hr />
	<?php if (count($results) > 1): ?>
		<?php foreach ($results as $resource): ?>
			<?php if (is_array($resource)): ?>
			  <dl>
			  	<dt><a href="<?php echo $resource['url'] ?>" title="Accéder à la ressource" target="_blank"><?php echo $resource['url'] ?></a></dt>
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