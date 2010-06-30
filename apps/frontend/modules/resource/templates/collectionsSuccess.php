<div id="container" class="narrow">
  <div id="presentation">
    <p>
    Ce site est dédié à l'exploration et à l'exploitation des ressources postées
    par les contributeurs du <a href="http://www.musiques-incongrues.net">forum des Musiques Incongrues</a>.
    </p>

    <p>
    Le site met à votre disposition des <strong>collections</strong> de <strong>ressources</strong> qui peuvent être requêtées selon différents critères.<br />
    La réponse à chaque requête pourra être obtenue sous différents <strong>formats</strong>, en fonction du besoin.<br />
    Pour faciliter le travail d'exploitation, le service propose des <strong>segments de ressources</strong> prédéfinis,
    dépendants des collections de ressources.
    </p>

    <p>
    En sélectionnant une collection de ressources ci-dessous, vous aurez accès à une documentation expliquant comment l'exploiter,
    ainsi qu'à une série d'exemples de requêtage.
    </p>
  </div>

  <hr />

  <h2>Collections de ressources</h2>
  <?php foreach ($collections as $collection_name => $collection_data): ?>
    <h3><?php echo $collection_name ?></h3>
    <?php include_partial(sprintf('resource/documentation/%s/description', $collection_name), array('collection_name' => $collection_name, 'collection_data' => $collection_data)) ?>
    <p>À ce jour, cette collection regroupe <strong><?php echo $collection_data['segments']['all']['count'] ?></strong> ressources.</p>
    <p><?php echo link_to('Consulter la documentation', '@resources_collection?collection='.$collection_name) ?></p>
    <p>Segments disponibles :</p>
    <ul>
    <?php foreach ($collection_data['segments'] as $segment_name => $segment_infos): ?>
        <li><a href="<?php echo url_for(sprintf('@resources_collection?collection=%s#segments-%s', $collection_name, $segment_name)) ?>" title="Consulter la documentation du segment <?php echo $segment_name ?>"><?php echo $segment_name ?></a> : <strong><?php echo $segment_infos['count']?></strong> ressources</li>
    <?php endforeach; ?>
    </ul>
  <?php endforeach; ?>
</div>
