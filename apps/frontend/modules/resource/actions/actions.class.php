<?php

/**
 * Actions for the "frontend/resource" module.
 *
 * @package    VanillaMiner
 * @subpackage Frontend
 */
class resourceActions extends sfActions
{
  public function executeTypes(sfWebRequest $request)
  {
    $types = sfConfig::get('app_resources_types', array());
    $this->types = $types;
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $groups = array();
    if ($resource_parameters = sfConfig::get(sprintf('app_resources_%s', $request->getParameter('type')), false))
    {
      $groups = $resource_parameters['groups'];
    }

    $this->groups = $groups;
  }
  
  public function executeFormats(sfWebRequest $request)
  {
    $group_formats = array();
    if ($resource_parameters = sfConfig::get(sprintf('app_resources_%s', $request->getParameter('type')), false))
    {
      $group_formats = $resource_parameters[$request->getParameter('group')]['formats'];
    }

    $this->formats = $group_formats;
  }
  
  public function executeGet(sfWebRequest $request)
  {
    // TODO : a good place to learn about dependency injection ?
    
    // Gather meaningful parameters
    $resource_type = $request->getParameter('type', 'unknown');
    $resource_group = $request->getParameter('group', 'all');
    $format = $request->getParameter('format', 'html');
    
    // TODO : autoload those clases
    include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/%s/ResourceGroup/%s.php', ucfirst($resource_type), ucfirst($resource_group));
    include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/Formatter/%s.php', ucfirst($format));
    
    // Get results from selected resource group
    $resource_group_class  = sprintf('CI_Search_%s_ResourceGroup_%s', ucfirst($resource_type), ucfirst($resource_group));
    if (!class_exists($resource_group_class))
    {
      throw new InvalidArgumentException(sprintf('Search class "%s" does not exist for "%s/%s" resource group', $resource_group_class, $resource_type, $resource_group));
    }
    $resource_group_instance = new $resource_group_class($this->getContext()->getEventDispatcher(), sfLucene::getInstance('IndexA', 'fr'));
    $raw_results = $resource_group_instance->search($request->getParameterHolder());
    
    // Make sure results are unique (TODO : this should go in search class)
    // see http://www.php.net/manual/en/function.array-unique.php#91134
    // TODO : it sucks as it makes the "limit" parameter not trustable
    $raw_results_unique = array();
    foreach ($raw_results as $result)
    {
      $raw_results_unique[md5($result['url'])] = $result;
    }
    $raw_results = $raw_results_unique;
    
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
