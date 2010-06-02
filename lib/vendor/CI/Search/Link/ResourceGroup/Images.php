<?php
class CI_Search_Link_ResourceGroup_Images
{
  protected $event_dispatcher;
  protected $lucene;
  
  public function __construct(sfEventDispatcher $dispatcher, sfLucene $lucene)
  {
    $this->event_dispatcher = $dispatcher;
    $this->lucene = $lucene;
  }
  
  public function search(sfParameterHolder $parameters)
  {
    $c = new sfLuceneCriteria();
    $c
      ->addField('mime_type', 'image')
      ->setLimit($parameters->get('limit', 50));

    // retrieve the results
    $sf_lucene_results = $this->lucene->friendlyFind($c);

    $array_results = array();
    foreach ($sf_lucene_results as $result)
    {
      $url_field = $result->getResult()->getField('url');
      $array_results[] = array(
        'url' => $url_field['value']
      );
    }
    
    return $array_results;
  }
}