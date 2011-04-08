<?php
class CI_Search_Formatter_XmlFeed extends CI_Search_Formatter
{
	public $content_type = 'text/xml';
	public $sf_has_layout = false;
	protected $resources = array();

	public function format(array $resources) {

		$this->resources = $resources;
		
		// Setup Zend
		// TODO : make sure embedded Zend Framework is used, not system wide
		require_once(dirname(__FILE__).'/../../../ZendFramework-1.11.4-minimal/library/Zend/Loader/Autoloader.php');
		Zend_Loader_Autoloader::getInstance();

		// Cache generation issue workaround (see http://framework.zend.com/issues/browse/ZF-6668)
		// TODO : This should not be necessary
		$_ENV['TMPDIR'] = dirname(__FILE__).'/../../../../../cache';
		
		// Create feed
		$feed = $this->getFeed();

		// Create feed entries
		foreach ($resources as $resource) {

			// Skip "num_found"
			if (!is_array($resource)) {
				continue;
			}

			// Create feed entry
			$entry = $this->createEntry($feed, $resource);
	
			// Add entry to feed
			$feed->addEntry($entry);
		}

		return $feed;
	}

	/**
	 * @return Zend_Feed_Writer_Feed
	 */
	protected function getFeed() {
		$feed = new Zend_Feed_Writer_Feed();
		$feed->setTitle(sprintf('data.musiques-incongrues.net XML feed - collection : %s / segment : %s / formatter : %s', $this->request->getParameter('collection'), $this->request->getParameter('segment'), __CLASS__));
		$feed->setLink(sprintf('http://data.musiques-incongrues.net/collections/%s/segments/%s/get?%s&format=%s', $this->request->getParameter('collection'), $this->request->getParameter('segment'), http_build_query($this->request->getParameterHolder()->getAll()), $this->options['type']));
		$feed->setFeedLink(sprintf('http://data.musiques-incongrues.net/collections/%s/segments/%s/get?%s&format=html', http_build_query($this->request->getParameterHolder()->getAll()), $this->request->getParameter('collection'), $this->request->getParameter('segment')), $this->options['type']);
		$feed->setDescription($this->getFeedDescription());
		$feed->setDateModified(time());
		
		return $feed;
	}
	
	/**
	 * @param Zend_Feed_Writer_Feed $feed
	 * @param array $resource
	 * 
	 * @return Zend_Feed_Entry_Abstract
	 */
	protected function createEntry(Zend_Feed_Writer_Feed $feed, array $resource) {
		
		// Instanciate entry
		$entry = $feed->createEntry();
		
		// Entry title
		$entry->setTitle($this->getEntryTitle($resource));
		
		// Entry link
		$entry->setLink($this->getEntryLink($resource));
		
		// Entry authors
		foreach ($this->getEntryAuthors($resource) as $author) {
			$entry->addAuthor($author['name'], $author['email'], $author['url']);
		}
		
		// Entry creation and modification date
		$entry->setDateModified(new Zend_Date($resource['contributed_at']));
		$entry->setDateCreated(new Zend_Date($resource['contributed_at']));
		
		// Entry enclosure
		$entry->setEnclosure($this->getEntryEnclosure($resource));

		return $entry;
	}
	
	protected function getEntryTitle(array $resource) {
		return $resource['discussion_name'];
	}
	
	protected function getEntryLink(array $resource) {
		return sprintf(
			'http://www.musiques-incongrues.net/forum/discussion/%d/%s', 
			$resource['discussion_id'], 
			$this->slugify($resource['discussion_name']));
	}
	
	protected function getEntryAuthors(array $resource) {
		return array(array(
			'name'  => $resource['contributor_name'],
			'email' => null,
			'url'   => sprintf('http://www.musiques-incongrues.net/forum/account/%d/', $resource['contributor_id']))
		);
	}
	
	protected function getEntryEnclosure(array $resource) {
		$mimeType = $resource['mime_type'];
		if (!$mimeType) {
			$mimeType = 'unknown';
		}
		$enclosure = array('uri' => $resource['url'], 'type' => $mimeType, 'length' => 666);
		return $enclosure;
	}
	
	protected function getDefaultOptions() {
		return array('type' => 'atom');
	}

	protected function getFeedDescription() {
		$exclude = array('module', 'action', 'format', 'sf_format', 'collection', 'segment');
		$criterion = array();
		foreach ($this->request->getParameterHolder()->getAll() as $parameterName => $parameterValue) {
			if (!in_array($parameterName, $exclude)) {
				$criterion[] = sprintf('%s : %s', $parameterName, $parameterValue);
			}
		}
		return sprintf('Search criterion : %s', implode(' / ', $criterion));		
	}
}
