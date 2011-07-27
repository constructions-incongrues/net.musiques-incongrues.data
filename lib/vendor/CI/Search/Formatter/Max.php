<?php
class CI_Search_Formatter_Max extends CI_Search_Formatter
{
  public $content_type = 'application/maxmsp+text';
  public $sf_has_layout = false;

  public function format(array $resources)
  {
  	unset($resources['num_found']);
    return $resources;
  }
}