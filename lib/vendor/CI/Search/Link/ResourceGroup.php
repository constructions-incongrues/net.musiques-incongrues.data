<?php
class CI_Search_Link_ResourceGroup
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
    // Build search criteria from request parameters
    $c = $this->buildLuceneCriteria($parameters);

    // Retrieve results from Solr
    $results_lucene = $this->lucene->friendlyFind($c);

    // Build results array
    $results_array = $this->buildResultsArray($results_lucene);
    
    return $results_array;
  }
  
  protected function buildLuceneCriteria(sfParameterHolder $parameters)
  {
    $c = new sfLuceneCriteria();
    $c->setLimit($parameters->get('limit', 50));
    
    return $c;
  }
  
  protected function buildResultsArray(sfLuceneResults $results_lucene)
  {
    $results_array = array();
    foreach ($results_lucene as $result)
    {
      $url_field = $result->getResult()->getField('url');
      $results_array[] = array(
        'url' => $url_field['value']
      );
    }

    return $results_array;
  }
  
}