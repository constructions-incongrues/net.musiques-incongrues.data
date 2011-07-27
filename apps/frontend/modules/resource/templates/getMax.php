<?php for ($i = 0; $i < count($results); $i++): ?>
<?php echo $i ?>, "<?php echo implode('" "', $results[$i]) ?>";<?php echo "\n"?>
<?php endfor; ?>