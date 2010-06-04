<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
  	<h1><?php echo link_to('data.musiques-incongrues.net', '@homepage', array('title' => "Retourner à la page d'accueil")) ?></h1>
    
    <?php echo $sf_content ?>
    
    <hr />
    <p><a href="https://launchpad.net/vanilla-miner/+milestone/0.1.0">Vanilla Miner 0.1.0</a> a été développé par <a href="http://www.constructions-incongrues.net/">Constructions Incongrues</a> et est hébergé par <a href="http://www.pastis-hosting.net">Pastis Hosting</a>.</p>
    <p>Le code source du service est <a href="https://launchpad.net/vanilla-miner">distribué</a> sous licence <a href="http://www.gnu.org/licenses/agpl.html">GNU Affero GPLv3</a></p>
  </body>
</html>