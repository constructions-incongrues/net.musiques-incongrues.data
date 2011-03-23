<p style="text-align: center;">
  <?php if ($sf_request->getParameter('start', 0) > 0): ?>
    <a style="font-size: 2em;" href="<?php echo $pagination['urlPrevious']?>" title="Résultats précédents">&larr;</a>
  &nbsp;&nbsp;
  <?php endif; ?>
  <?php if ($sf_request->getParameter('start', 0) + 50 < $results['num_found'] ): ?>
  <a style="font-size: 2em;" href="<?php echo $pagination['urlNext'] ?>" title="Résultats suivants">&rarr;</a>
  <?php endif; ?>
</p>

