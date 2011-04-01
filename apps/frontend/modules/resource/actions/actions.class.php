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
        include sprintf(sfConfig::get('sf_lib_dir').'/vendor/CI/Search/Formatter/%s.php', ucfirst($format));

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

        // Pagination
        $routing = $this->getContext()->getRouting();
        $routeCurrent = $routing->getCurrentRouteName();
        $pagination['urlNext'] = $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('start' => $request->getParameter('start') + 50)));
        $pagination['urlPrevious'] = $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('start' => $request->getParameter('start') - 50)));

        // Get other available formats
		$urlsFormats = array(
			'json' => $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('format' => 'json'))), 
			'php'  => $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('format' => 'php'))),
			'xspf'  => $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('format' => 'xspf'))),
			'rss'  => $routing->generate($routeCurrent, array_merge($request->getParameterHolder()->getAll(), array('format' => 'rss'))),
		);
        
        // Pass results to view
        $this->results = $results;
        $this->collection = $resource_collection;
        $this->segment = $resource_segment;
        $this->pagination = $pagination;
        $this->urlsFormats = $urlsFormats;

        // Select template
        return ucfirst($format);
    }
    
    public function executeExtract(sfWebRequest $request)
    {
		// Dependencies
		require_once(sfConfig::get('sf_lib_dir').'/vendor/SolrPhpClient/Apache/Solr/Document.php');
		require_once(sfConfig::get('sf_lib_dir').'/vendor/SolrPhpClient/Apache/Solr/Service.php');
    	
    	// This does not help here
    	sfConfig::set('sf_web_debug', false);
    	
    	// Awaited data structure for each posted resource
    	$resourceSpec = array(
    		'url'                => FILTER_SANITIZE_URL, 
    		'comment_id'         => FILTER_SANITIZE_NUMBER_INT, 
    		'contributed_at'     => FILTER_SANITIZE_STRING,
    		'contributor_id'     => FILTER_SANITIZE_NUMBER_INT,
    		'contributor_name'   => FILTER_SANITIZE_STRING, 
    		'discussion_id'      => FILTER_SANITIZE_NUMBER_INT, 
    		'discussion_name'    => FILTER_SANITIZE_STRING
    	);
    	
    	// Check payload
    	$data = array();
    	$data = urldecode(trim(file_get_contents('php://input')));
    	
    	// Decode JSON payload
    	$resources = json_decode($data, true);
    	if (!$resources) {
    		throw new InvalidArgumentException(sprintf('Malformed JSON string : "%s"', $data), 400);
    	}
    	
		// Handle each posted resource
		$solrDocuments = array();
		foreach ($resources as $resource) {
			// Sanity checks
			$resourceClean = array();
			foreach ($resourceSpec as $key => $filter) {
				if (!isset($resource[$key])) {
					throw new InvalidArgumentException(sprintf('Missing resource property "%s"', $key), 400);
				}
				$resourceClean[$key] = filter_var($resource[$key], $filter);
			}
			
			// Make sure url does not already exist in index
			$solrService = new Apache_Solr_Service('127.0.0.1', '8983', '/solr/IndexA_fr/');
			$results = $solrService->search(sprintf('url:"%s"', $resourceClean['url']));

			if ($results->response->numFound === 0) {
				// Build Solr document
				$solrDocument = new Apache_Solr_Document();
				$solrDocument->setField('url', $resource['url']);
				$solrDocument->setField('comment_id', $resource['comment_id']);
				$solrDocument->setField('contributed_at', strftime('%Y-%m-%dT%T.000Z', $resource['contributed_at']));
				$solrDocument->setField('contributor_id', $resource['contributor_id']);
				$solrDocument->setField('contributor_name', $resource['contributor_name']);
				$solrDocument->setField('discussion_id', $resource['discussion_id']);
				$solrDocument->setField('discussion_name', $resource['discussion_name']);
				$solrDocument->setField('sfl_guid', uniqid());
				$solrDocuments[] = $solrDocument;
			}
		}

		// Post documents to Vanilla Miner instance
		$solrResponse = $solrService->addDocuments($solrDocuments);
		$solrService->commit(true);
		
    	return sfView::NONE;
    }
}
