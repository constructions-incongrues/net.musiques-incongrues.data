<?php
class CI_Search_Formatter_Php extends CI_Search_Formatter
{
  public $content_type = 'application/vnd.php.serialized';
  public $sf_has_layout = false;
  
  public function format(array $resources)
  {
    return serialize($resources);
  }
}