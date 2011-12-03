<?php if ($sf_request->getParameter('callback')): ?>
	<?php echo sprintf('%s(%s);', $sf_request->getParameter('callback'), $results) ?>
<?php else: ?>
	<?php echo $results ?>
<?php endif; ?>
