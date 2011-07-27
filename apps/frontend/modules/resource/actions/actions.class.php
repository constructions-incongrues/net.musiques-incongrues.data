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
		$collections = array();
		$collections_names = sfConfig::get('app_miner_collections', array());

		foreach ($collections_names as $collection_name => $collection_info)
		{
			include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/%s/Segment.php', $collection_info['model']);

			// Alphabetical sort of segments
			sort($collection_info['segments']);

			// Gather informations about each collection segment
			$segments_infos = array();
			foreach ($collection_info['segments'] as $segment_name)
			{
				include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/%s/Segment/%s.php', $collection_info['model'], ucfirst($segment_name));
				$search_classname = sprintf('CI_Search_%s_Segment_%s', $collection_info['model'], $segment_name);
				// TODO : Lucene instance must be configurable
				$search = new $search_classname($this->dispatcher, sfLucene::getInstance('IndexA', 'fr'));
				$segments_infos[$segment_name] = array('count' => $search->count($request->getParameterHolder()));
			}

			$collections[$collection_name] = array(
                'count'    => Doctrine::getTable($collection_info['model'])->count(),
                'segments' => $segments_infos
			);
		}
		$this->collections = $collections;
	}

	public function executeSegments(sfWebRequest $request)
	{
		$collections = sfConfig::get('app_miner_collections');
		$segments = array();
		if (isset($collections[$request->getParameter('collection')]));
		{
			$segments = $collections[$request->getParameter('collection')]['segments'];
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
		$formatterOptions = $request->getParameter('formatter_options', array());

		// Get model corresponding to collection
		$collections_infos = sfConfig::get('app_miner_collections');
		if (!isset($collections_infos[$resource_collection]))
		{
			throw new InvalidArgumentException(sprintf('Unknown collection "%s"', $resource_collection));
		}
		$collection = $collections_infos[$resource_collection];
		$resource_model = $collection['model'];

		// TODO : autoload those classes
		include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/%s/Segment.php', ucfirst($resource_model));
		include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/%s/Segment/%s.php', ucfirst($resource_model), ucfirst($resource_segment));
		
		include sfConfig::get('sf_lib_dir').'/vendor/CI/Search/Formatter.php';
		if (strstr($format, '_')) {
			$partsFormat = explode('_', $format);
			include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/Formatter/%s.php', ucfirst($partsFormat[0]));
			include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/Formatter/%s/%s.php', ucfirst($partsFormat[0]), ucfirst($partsFormat[1]));
		} else {
			include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/Formatter/%s.php', ucfirst($format));
		}

		// Get results from selected resource segment
		$resource_segment_class  = sprintf('CI_Search_%s_Segment_%s', ucfirst($resource_model), ucfirst($resource_segment));
		if (!class_exists($resource_segment_class))
		{
			throw new InvalidArgumentException(sprintf('Search class "%s" does not exist for "%s/%s" resource segment', $resource_segment_class, $resource_model, $resource_segment));
		}
		// TODO : lucene index must be configurable
		$resource_segment_instance = new $resource_segment_class($this->getContext()->getEventDispatcher(), sfLucene::getInstance('IndexA', 'fr'));

		// Default sort
		if (!$request->hasParameter('sort_field')) {
			$request->getParameterHolder()->add(array('sort_field' => 'contributed_at'));
		}
		if (!$request->hasParameter('sort_direction')) {
			$request->getParameterHolder()->add(array('sort_direction' => 'desc'));
		}
		$raw_results = $resource_segment_instance->search($request->getParameterHolder());

		// Format results
		if (strstr($format, '_')) {
			$partsFormat = explode('_', $format);
			$formatter_class = sprintf('CI_Search_Formatter_%s_%s', ucfirst($partsFormat[0]), ucfirst($partsFormat[1]));
		} else {
			$formatter_class = sprintf('CI_Search_Formatter_%s', ucfirst($format));
		}
		
		if (!class_exists($formatter_class))
		{
			throw new InvalidArgumentException(sprintf('Formatter class "%s" does not exist for format "%s"', $formatter_class, $format));
		}
		$formatter = new $formatter_class($this->getContext()->getEventDispatcher(), $request, $formatterOptions);
		$results = $formatter->format($raw_results);

		// Tweak response depending on formatter
		$this->getResponse()->setContentType($formatter->content_type);
		$this->setLayout($formatter->sf_has_layout);
		if (!$formatter->sf_has_layout)
		{
			sfConfig::set('sf_web_debug', false);
		}

		// Pagination
		$routing = $this->getContext()->getRouting();
		$routeCurrent = $routing->getCurrentRouteName();
		$pagination['urlNext'] = $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('start' => $request->getParameter('start') + 50)));
		$pagination['urlPrevious'] = $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('start' => $request->getParameter('start') - 50)));

		// Get other available formats
		// TODO : formats autodiscovery through introspection
		$urlsFormats = array(
			'json'     => $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('format' => 'json'))),
			'podcast'  => $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('format' => 'xmlfeed_podcast'))), 
			'php'      => $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('format' => 'php'))),
			'xspf'     => $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('format' => 'xspf')))
		);

		// Pass results to view
		$this->results = $results;
		$this->collection = $resource_collection;
		$this->segment = $resource_segment;
		$this->pagination = $pagination;
		$this->urlsFormats = $urlsFormats;
		$this->formatterOptions = $formatter->getOptions();

		// Select template
		return ucfirst($format);
	}

	public function executeExtract(sfWebRequest $request)
	{
		// This does not help here
		sfConfig::set('sf_web_debug', false);
		 
		// Awaited data structure for each posted resource
		// TODO : use doctrine model validation capabilities 
		$resourceSpec = array(
    		'url'                => FILTER_SANITIZE_URL, 
    		'comment_id'         => FILTER_SANITIZE_NUMBER_INT, 
    		'contributed_at'     => FILTER_SANITIZE_STRING,
    		'contributor_id'     => FILTER_SANITIZE_NUMBER_INT,
    		'contributor_name'   => FILTER_SANITIZE_STRING, 
    		'discussion_id'      => FILTER_SANITIZE_NUMBER_INT, 
    		'discussion_name'    => FILTER_SANITIZE_STRING
		);
		 
		// Read payload
		$data = urldecode(trim(file_get_contents('php://input')));

		// Decode JSON payload
		$resources = json_decode($data, true);
		if (!$resources) {
			throw new InvalidArgumentException(sprintf('Malformed JSON string : "%s"', $data), 400);
		}
		 
		// Required for extension => mime type conversion
		require_once(sprintf('%s/vendor/php_arrays/extensions.php', sfConfig::get('sf_lib_dir')));
		
		// Handle each posted resource
		foreach ($resources as $resource) {
			// TODO : Move /extract to /collections/link/extract and abstract resource instanciation
			$link = new Link();
			
			// Sanity checks
			foreach ($resourceSpec as $key => $filter) {
				if (!isset($resource[$key])) {
					throw new InvalidArgumentException(sprintf('Missing resource property "%s"', $key), 400);
				}
				$link->$key = filter_var($resource[$key], $filter);
			}

			// Enhance data
			$link->autoPopulate($items);
			
			// Fix date strings for solr to understand them
			$link->contributed_at = strftime('%Y-%m-%dT%T.000Z', $link->contributed_at);
			
			try {
				// Save link to database and solr
				$link->save();
				
				// Clear related cache
				$cache = Zend_Cache::factory('Core', new Zend_Cache_Backend_File(array('cache_dir' => sfConfig::get('sf_cache_dir'), 'file_name_prefix' => 'mi_miner_cache')));
				$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array(sprintf('discussion_%d', $link->discussion_id)));
			} catch (Doctrine_Exception $e) {
				// 23000 == Duplicate record
				if ($e->getCode() === 23000) {
					// TODO : handle multiple url occurences in index
					$this->getLogger()->notice(sprintf('Link with URL "%s" already exist in database. Save cancelled.', $link->url));
				}
			}
		}

		// TODO : Appropriate HTTP response code
		return sfView::NONE;
	}
}