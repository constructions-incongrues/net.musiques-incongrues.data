<?php
class CI_Search_Formatter_Xspf extends CI_Search_Formatter
{
	public $content_type = 'application/xspf+xml';
	public $sf_has_layout = false;

	public function format(array $resources) {
		error_reporting(E_ALL & ~E_DEPRECATED);
		require 'File/XSPF.php';
		$playlist = new File_XSPF();
		
		foreach ($resources as $resource) {
			$track = new File_XSPF_Track();
			
			// Track location
			$trackLocation = new File_XSPF_Location();
			$trackLocation->setUrl($resource['url']);
			$track->addLocation($trackLocation);

			// Track title
			if (is_array($resource)) {
				$track->setTitle($this->getTrackTitle($resource));
			}
			
			// URL to related discussion
			$topicUrl = sprintf('http://www.musiques-incongrues.net/forum/discussion/%d/%s', $resource['discussion_id'], $this->slugify($resource['discussion_name']));
			$track->setInfo($topicUrl);
			
			// Add track to playlist
			$playlist->addTrack($track);
		}

		return $playlist;
	}
	
	private function getTrackTitle(array $resource) {
		// Thanks to soundcloud url formalism, we can guess the track title
		if ($resource['domain_fqdn'] == 'soundcloud.com') {
			$urlParts = array_reverse(explode('/', $resource['url']));
			$resource['title'] = sprintf('%s - %s', str_replace('-', ' ', ucfirst($urlParts[2])), str_replace('-', ' ', ucfirst($urlParts[1])));
		} else {
			$resource['title'] = urldecode(basename($resource['url'], '.mp3'));
			$resource['title'] = urldecode($resource['title']);
			$resource['title'] = str_replace('_', ' ', $resource['title']);
		}
		
		return $resource['title'];
	}
}