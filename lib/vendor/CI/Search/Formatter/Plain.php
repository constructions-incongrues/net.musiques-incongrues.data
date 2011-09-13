<?php
class CI_Search_Formatter_Plain extends CI_Search_Formatter
{
  public $content_type = 'text/plain';
  public $sf_has_layout = false;
  
  public function format(array $resources)
  {
  	$urls = array();
  	foreach ($resources as $resource) {
  		$urls[] = $resource['url'];
  	}
    return implode(' ', $urls);
  }
}