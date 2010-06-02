<?php

/**
 * resource actions.
 *
 * @package    vanilla-miner
 * @subpackage resource
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
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
    // Get results from selected resource group
    $resource_group_class  = sprintf('CI_Search_Resource_%s', ucfirst($request->getParameter('group', 'all')));
    $resource_group = new $resource_group_class($this->getContext()->getEventDispatcher(), sfLucene::getInstance('IndexA', 'fr'));
    $raw_results = $resource_group->search($request->getParameterHolder()->getAll());
    
    // Format results
    $formatter_class = sprintf('CI_Search_Formatter_%s', ucfirst($request->getParameter('format', 'html')));
    $formatter = new $formatter_class($this->getContext()->getEventDispatcher());
    $results = $formatter->format($raw_results, $request, $response);
    
    // Pass results to view
    $this->results = $results;
    
    // Select template
    return ucfirst($request->getParameter('format', 'html'));
    
    $resources_getter = array($this, sprintf('get%s', ucfirst($request->getParameter('group'))));
    $resources = call_user_func($resources_getter, $request);
    $results = call_user_func(array($this, sprintf('format%s', ucfirst($request->getParameter('format', 'html')))), $resources, $request, $this->getResponse());
    $this->results = $results;
    
    return ucfirst($request->getParameter('format', 'html'));
  }
  
  public function getImages(sfWebRequest $request)
  {
    $c = new sfLuceneCriteria();
    $c
      ->addField('mime_type', 'image')
      ->setLimit($request->getParameter('limit', 50));

    // retrieve the lucene instance
    $lucene = sfLucene::getInstance('IndexA', 'fr');

    // retrieve the results
    $sf_lucene_results = $lucene->friendlyFind($c);

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
  
  public function getMp3(sfWebRequest $request)
  {
    $c = new sfLuceneCriteria();
    $c
      ->addField('mime_type', 'audio/mpeg')
      ->setLimit($request->getParameter('limit', 50));

    // retrieve the lucene instance
    $lucene = sfLucene::getInstance('IndexA', 'fr');

    // retrieve the results
    $sf_lucene_results = $lucene->friendlyFind($c);

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

  public function getYoutube(sfWebRequest $request)
  {
    $c = new sfLuceneCriteria();
    $c
      ->addField('domain_parent', 'youtube.com')
      ->setLimit($request->getParameter('limit', 50));

    // retrieve the lucene instance
    $lucene = sfLucene::getInstance('IndexA', 'fr');

    // retrieve the results
    $sf_lucene_results = $lucene->friendlyFind($c);

    $array_results = array();
    foreach ($sf_lucene_results as $result)
    {
      $url_field = $result->getResult()->getField('url');
      $array_results[] = array(
        'url' => $url_field['value']
      );
    }
    
    if ($request->getParameter('sf_format') == 'json')
    {
      $array_results = json_encode($array_results);
    }
    
    return $array_results;
  }

  public function formatHtml(array $resources, sfWebRequest $request, sfWebResponse $response)
  {
    return $resources;
  }
  
  public function formatXspf(array $resources, sfWebRequest $request, sfWebResponse $response)
  {
    $response->setContentType('application/xspf+xml');
//    $response->setContentType('text/xml');
    sfConfig::set('sf_web_debug', false);
    $this->setLayout(false);
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
  
  public function formatJson(array $resources, sfWebRequest $request, sfWebResponse $response)
  {
    $response->setContentType('application/json');
    sfConfig::set('sf_web_debug', false);
    $this->setLayout(false);
    return json_encode($resources);
  }
}
