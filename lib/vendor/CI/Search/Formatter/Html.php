<?php
class CI_Search_Formatter_Html
{
  public $content_type = 'text/html';
  public $sf_has_layout = 'layout';
  
  protected $event_dispatcher;
  
  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->event_dispatcher = $dispatcher;
  }
  
  public function format(array $resources)
  {
    return $resources;
  }
}