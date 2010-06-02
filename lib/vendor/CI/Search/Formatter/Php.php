<?php
class CI_Search_Formatter_Php
{
  public $content_type = 'application/vnd.php.serialized';
  public $sf_has_layout = false;
  
  protected $event_dispatcher;
  
  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->event_dispatcher = $dispatcher;
  }
  
  public function format(array $resources)
  {
    return serialize($resources);
  }
}