<?php 
// TODO : compute prefix in action
if (sfConfig::get('sf_no_script_name'))
{
  $prefix = $sf_request->getUriPrefix();
}
else
{
  $prefix = sprintf('%s%s', $sf_request->getUriPrefix(), $sf_request->getScriptName());
}
?>

<p>Ces paramètres sont communs à toutes les collections et permettent d'influer sur la forme de la réponse à la requête.</p>

<h4 id="common-parameters-limit">limit</h4>
<p>Ce paramètre permet de définir le nombre d'enregistrements maximum retourné par la requête. Par exemple :</p>
<pre>GET <?php echo $prefix?>/collections/link/segments/all/get?<strong>limit=5</strong></pre>

<dl>
  <dt>Caractéristiques</dt>
  <dd>Type : Entier</dd>
  <dd>Valeur par défaut : <code>50</code></dd>

  <dt>Valeurs spéciales</dt>
  <dd>-1 : Tous les enregistrements sont retournés</dd>
</dl>

<h4 id="common-parameters-sort_field">sort_field</h4>
<p>C'est le paramètre qui détermine quel attribut du <a href="#schema">schéma</a> sera utilisé pour trier les enregistrements.</p>
<dl>
  <dt>Caractéristiques</dt>
  <dd>Type : Chaîne de caractères</dd>
  <dd>Valeur par défaut : <a href="#schema-contributed_at">contributed_at</a></dd>
  
  <dt>Valeurs spéciales</dt>
  <dd>random : Les enregistrements sont triés aléatoirement</dd>
</dl>

<h4 id="common-parameters-sort_direction">sort_direction</h4>
<p>Ce paramètre conditionne la direction du tri des enregistrements.</p>
<dl>
  <dt>Caractéristiques</dt>
  <dd>Type : Chaîne de caractère. <code>asc</code> ou <code>desc</code></dd>
  <dd>Valeur par défaut : <code>asc</code></dd>
</dl>

<h4 id="common-parameters-format">format</h4>
<p>Ce paramètre détermine le <a href="#formats">format</a> de la réponse à la requête.</p>
<dl>
  <dt>Caractéristiques</dt>
  <dd>Type : Chaîne de caractères</dd>
  <dd>Valeur par défaut : <code>html</code></dd>
</dl>