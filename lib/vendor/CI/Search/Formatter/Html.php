<?php
class CI_Search_Formatter_Html extends CI_Search_Formatter
{
  public $content_type = 'text/html';
  public $sf_has_layout = 'layout';
  
  public function format(array $resources)
  {
    return $resources;
  }
}