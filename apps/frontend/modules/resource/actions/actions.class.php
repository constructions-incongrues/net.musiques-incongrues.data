<?php

/**
 * Actions for the "frontend/resource" module.
 *
 * @package    VanillaMiner
 * @subpackage Frontend
 */
class resourceActions extends sfActions
{
  public function executeCollections(sfWebRequest $request)
  {
    $collections = sfConfig::get('app_resources_collections', array());
    $this->collections = $collections;
  }
  
  public function executeSegments(sfWebRequest $request)
  {
    $segments = array();
    if ($resource_parameters = sfConfig::get(sprintf('app_resources_%s', $request->getParameter('collection')), false))
    {
      $segments = $resource_parameters['segments'];
    }

    $this->segments = $segments;
  }
  
  public function executeFormats(sfWebRequest $request)
  {
    $segment_formats = array();
    if ($resource_parameters = sfConfig::get(sprintf('app_resources_%s', $request->getParameter('collection')), false))
    {
      $segment_formats = $resource_parameters[$request->getParameter('segment')]['formats'];
    }

    $this->formats = $segment_formats;
  }
  
  public function executeGet(sfWebRequest $request)
  {
    // TODO : a good place to learn about dependency injection ?
    // TODO : "q" query parameter makes it possible to directly query solr (no sfLuceneCriteria)
    
    // Gather meaningful parameters
    $resource_collection = $request->getParameter('collection', 'unknown');
    $resource_segment = $request->getParameter('segment', 'all');
    $format = $request->getParameter('format', 'html');
    
    // TODO : autoload those clases
    include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/%s/Segment.php', ucfirst($resource_collection));
    include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/%s/Segment/%s.php', ucfirst($resource_collection), ucfirst($resource_segment));
    include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/Formatter/%s.php', ucfirst($format));
    
    // Get results from selected resource segment
    $resource_segment_class  = sprintf('CI_Search_%s_Segment_%s', ucfirst($resource_collection), ucfirst($resource_segment));
    if (!class_exists($resource_segment_class))
    {
      throw new InvalidArgumentException(sprintf('Search class "%s" does not exist for "%s/%s" resource segment', $resource_segment_class, $resource_collection, $resource_segment));
    }
    // TODO : lucene index must be configurable
    $resource_segment_instance = new $resource_segment_class($this->getContext()->getEventDispatcher(), sfLucene::getInstance('IndexA', 'fr'));
    $raw_results = $resource_segment_instance->search($request->getParameterHolder());
    
    // Make sure results are unique (this a just a hack)
    // see http://www.php.net/manual/en/function.array-unique.php#91134
    // NOTE : it sucks as it makes the "limit" parameter not trustable
    // TODO : resource unicity must be enforced when importing documents into solr
    if ($this->getRequestParameter('hack_unique_results', false))
    {
      $raw_results_unique = array();
      foreach ($raw_results as $result)
      {
        $raw_results_unique[md5($result['url'])] = $result;
      }
      $raw_results = $raw_results_unique;
    }
    
    // Format results
    $formatter_class = sprintf('CI_Search_Formatter_%s', ucfirst($format));
    if (!class_exists($formatter_class))
    {
      throw new InvalidArgumentException(sprintf('Formatter class "%s" does not exist for format "%s"', $formatter_class, $format));
    }
    $formatter = new $formatter_class($this->getContext()->getEventDispatcher());
    $results = $formatter->format($raw_results);
    
    // Tweak response depending on formatter
    $this->getResponse()->setContentType($formatter->content_type);
    $this->setLayout($formatter->sf_has_layout);
    if (!$formatter->sf_has_layout)
    {
      sfConfig::set('sf_web_debug', false);
    }
    
    // Pass results to view
    $this->results = $results;
    
    // Select template
    return ucfirst($format);
  }  
}
