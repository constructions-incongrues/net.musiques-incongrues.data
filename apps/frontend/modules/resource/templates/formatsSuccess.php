<ul>
  <?php foreach ($formats as $format): ?>
  	<li><?php echo link_to($format, sprintf('@resources_type_segment_get?type=%s&segment=%s&format=%s', $sf_request->getParameter('type'), $sf_request->getParameter('segment'), $format)) ?></li>
  <?php endforeach; ?>
</ul>