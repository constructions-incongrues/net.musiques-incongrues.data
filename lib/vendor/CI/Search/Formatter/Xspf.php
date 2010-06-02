<?php
class CI_Search_Formatter_Xspf
{
  public $content_type = 'application/xspf+xml';
  public $sf_has_layout = false;
  
  protected $event_dispatcher;
  
  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->event_dispatcher = $dispatcher;
  }
  
  public function format(array $resources)
  {
    error_reporting(E_ALL);
    require 'File/XSPF.php';
    $playlist = new File_XSPF();
    foreach ($resources as $resource)
    {
      $track_location = new File_XSPF_Location();
      $track_location->setUrl($resource['url']);
      $track = new File_XSPF_Track();
      $track->addLocation($track_location);
      $playlist->addTrack($track);
    }
    
    return $playlist;
  }
}