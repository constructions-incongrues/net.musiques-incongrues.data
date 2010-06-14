<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" type="image/png" href="<?php echo $sf_request->getRelativeUrlRoot() ?>/images/favicon.png" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>

    <h1><?php echo link_to('data.musiques-incongrues.net', '@homepage', array('title' => "Retourner à la page d'accueil")) ?></h1>

    <img id="ananas" src="http://www.musiques-incongrues.net/forum/extensions/Vanillacons/smilies/otro%20-%20fruits/pin01.gif" title="ANANAS !" alt="Ananas"/>

   <p id="url"></p>

    <?php echo $sf_content ?>

    <hr />

    <p><a href="https://launchpad.net/vanilla-miner/+milestone/0.1.0">Vanilla Miner 0.1.0</a> a été développé par <a href="http://www.constructions-incongrues.net/">Constructions Incongrues</a> et est hébergé par <a href="http://www.pastis-hosting.net">Pastis Hosting</a>.</p>
    <p>Le code source du service est <a href="https://launchpad.net/vanilla-miner">distribué</a> sous licence <a href="http://www.gnu.org/licenses/agpl.html">GNU Affero GPLv3</a>.</p>
    <p>Ce service utilise (notamment) <a href="http://www.symfony-project.org">symfony</a>, <a href="http://www.doctrine-project.org">Doctrine</a> et <a href="http://lucene.apache.org/solr/">Solr</a>.</p>
    <p>Contact : contact @ musiques-incongrues . net</p>

  </body>
</html>