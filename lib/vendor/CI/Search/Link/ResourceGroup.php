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
    
    // Define which fields will be fetched
    $schema_fields =  $this->getSearchModelFields();
    foreach ($parameters->getAll() as $name => $value)
    {
      if (in_array($name, $schema_fields))
      {
        $c->addField($name, $value);
      }
    }
    
    // Define limit
    $c->setLimit($parameters->get('limit', 50));
    
    // Define sorting
    $sorting_direction = $parameters->get('sort_direction', 'asc');
    if ($sorting_direction == 'desc')
    {
      $sort_method = 'addDescendingSortBy';
    }
    else
    {
      $sort_method = 'addAscendingSortBy';
    }
    $c->$sort_method($parameters->get('sort_field', 'contributed_at'));

    return $c;
  }
  
  protected function buildResultsArray(sfLuceneResults $results_lucene)
  {
    $schema_fields = $this->getSearchModelFields();
    
    $results_array = array();
    foreach ($results_lucene as $result)
    {
      $result_array = array();
      foreach ($schema_fields as $schema_field_name)
      {
        $result_field = $result->getResult()->getField($schema_field_name);
        $result_array[$schema_field_name] = $result_field['value'];
      }
      $results_array[] = $result_array;
    }
   
    return $results_array;
  }
  
  private function getSearchModelFields()
  {
    // TODO : Get config info from lucene instance
    $solr_config = sfLucene::getConfig();
    $schema_fields = $solr_config['IndexA']['models']['Link']['fields'];
    
    return array_keys($schema_fields);
  }
  
}