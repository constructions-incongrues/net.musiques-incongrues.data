<p>
Bonjour, ce site est dédié à l'exploration et à l'exploitation des ressources postées 
par les contributeurs du <a href="http://www.musiques-incongrues.net">forum des Musiques Incongrues</a>.
</p>

<p>
Le site met à votre disposition des <strong>collections</strong> de <strong>ressources</strong> qui peuvent être requêtées selon différent critères.<br />
La réponse à chaque requête pourra être obtenue sous différent <strong>formats</strong>, en fonction du besoin.<br />
Pour faciliter le travail d'exploitation, le service propose des <strong>segments de ressources</strong> prédéfinis, 
dépendants des collections de ressources. 
</p>

<p>
En sélectionnant une collection de ressources ci-dessous, vous aurez accès à une documentation expliquant comment l'exploiter, 
ainsi qu'à une série d'exemples de requêtage.
</p>

<h2>Collections de ressources</h2>
<?php foreach ($collections as $collection_name => $collection_data): ?>
  <h3><?php echo $collection_name ?></h3>
  <?php // TODO : data are not accessible in partial. WTF ? ?>
  <?php include_partial(sprintf('resource/documentation/%s/description', $collection_name), array('collection_name' => $collection_name, 'collection_data' => $collection_data)) ?>
  <p>À ce jour, cette collection regroupe <strong><?php echo $collection_data['count'] ?></strong> ressources.</p>
  <p><?php echo link_to('Consulter la documentation', '@resources_collection?collection='.$collection_name) ?></p>
<?php endforeach; ?>

<h2>Sous l'capot</h2>
<p>Ce service utilise (notamment) <a href="http://www.symfony-project.org">symfony</a>, <a href="http://www.doctrine-project.org">Doctrine</a> et <a href="http://lucene.apache.org/solr/">Solr</a>.</p>
<p>Ce service a été développé par <a href="http://www.constructions-incongrues.net/">Constructions Incongrues</a> et est hébergé par <a href="http://www.pastis-hosting.net">Pastis Hosting</a>.</p>
<p>Le code source du service est <a href="https://launchpad.net/vanilla-miner">distribué</a> sous licence <a href="http://www.gnu.org/licenses/agpl.html">GNU Affero GPLv3</a></p>