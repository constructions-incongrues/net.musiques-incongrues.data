<?php
class CI_Search_Link_Segment
{
  /**
   * @var sfEventDispatcher
   */
  protected $event_dispatcher;

  /**
   * @var sfLucene
   */
  protected $lucene;

  /**
   * @param sfEventDispatcher $dispatcher
   * @param sfLucene          $lucene
   */
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

  public function count(sfParameterHolder $parameters)
  {
    // Build search criteria from request parameters
    $c = $this->buildLuceneCriteria($parameters);
    $c->setLimit(0);

    // Retrieve results from Solr
    $results_lucene = $this->lucene->friendlyFind($c);

    return $results_lucene->count();
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
        // NOTE : we need to set "force" (third parameter) to true because of a weird behaviof of sfLuceneCriteria escape checking.
        // TODO : make sure that this is *not* an awful security risk
        $c->addField($name, $value, sfLuceneCriteria::TYPE_AND, true);
      }
    }

    // Define limit
    $limit = $parameters->get('limit', 50);
    if ($limit == '-1')
    {
      $limit = '666666666';
    }
    $c->setLimit($limit);

    // default : return links with availability being marked as "available" or "unknown"
    if ($parameters->get('availability', null) === null)
    {
        $c->addField('-availability', 'unavailable');
    }

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

    // Take care of random sorting
    $sort_field = $parameters->get('sort_field', 'contributed_at');
    if ($sort_field == 'random')
    {
      $sort_field = sprintf('random_%s', uniqid());
    }
    $c->$sort_method($sort_field);

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

    // Total number of results found
    $results_array['num_found'] = $results_lucene->count();

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