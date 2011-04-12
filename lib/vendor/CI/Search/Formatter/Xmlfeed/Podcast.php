<?php
class CI_Search_Formatter_Xmlfeed_Podcast extends CI_Search_Formatter_XmlFeed {

	protected function getEntryTitle(array $resource) {
		// Thanks to soundcloud url formalism, we can guess the track title
		if ($resource['domain_fqdn'] == 'soundcloud.com') {
			$urlParts = array_reverse(explode('/', $resource['url']));
			$resource['title'] = sprintf('%s - %s', str_replace('-', ' ', ucfirst($urlParts[2])), str_replace('-', ' ', ucfirst($urlParts[1])));
		} else {
			$resource['title'] = urldecode(basename($resource['url'], '.mp3'));
			$resource['title'] = urldecode($resource['title']);
			$resource['title'] = str_replace('_', ' ', $resource['title']);
		}
		
		return sprintf('"%s" in discussion "%s"', $resource['title'], $resource['discussion_name']);
	}
	
	protected function getEntryLink(array $resource) {
		return sprintf(
			'http://www.musiques-incongrues.net/forum/discussion/%d/%s', 
			$resource['discussion_id'], 
			$this->slugify($resource['discussion_name']));
	}
	
	protected function getEntryEnclosure(array $resource) {
		$mimeType = $resource['mime_type'];
		if (!$mimeType) {
			$mimeType = 'unknown';
		}
		$enclosure = array('uri' => $resource['url'], 'type' => $mimeType, 'length' => 666);
		return $enclosure;
	}
}