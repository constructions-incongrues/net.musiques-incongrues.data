<ul>
  <?php foreach ($formats as $format): ?>
  	<li><?php echo link_to($format, sprintf('@resources_collection_segment_get?collection=%s&segment=%s&format=%s', $sf_request->getParameter('collection'), $sf_request->getParameter('segment'), $format)) ?></li>
  <?php endforeach; ?>
</ul>