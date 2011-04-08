<?php
class CI_Search_Formatter_Json extends CI_Search_Formatter
{
  public $content_type = 'application/json';
  public $sf_has_layout = false;

  public function format(array $resources)
  {
    return json_encode($resources);
  }
}