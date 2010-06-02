<ul>
  <?php foreach ($formats as $format): ?>
  	<li><?php echo link_to($format, sprintf('@resources_type_group_get?type=%s&group=%s&format=%s', $sf_request->getParameter('type'), $sf_request->getParameter('group'), $format)) ?></li>
  <?php endforeach; ?>
</ul>