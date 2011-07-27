<?php
class CI_Search_Formatter_Max extends CI_Search_Formatter
{
  public $content_type = 'application/maxmsp+text';
  public $sf_has_layout = false;

  public function format(array $resources)
  {
  	// Remove unused index
  	unset($resources['num_found']);
  	
  	// Decode entities
  	foreach ($resources as $i => $resource) {
  		foreach ($resource as $k => $v) {
  			$resource[$k] = html_entity_decode($v);
  		}
  		$resources[$i] = $resource;
  	}
  	
    return $resources;
  }
}