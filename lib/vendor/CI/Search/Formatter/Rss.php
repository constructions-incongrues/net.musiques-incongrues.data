<?php
class CI_Search_Formatter_Rss
{
	public $content_type = 'application/rss+xml';
	public $sf_has_layout = false;

	protected $event_dispatcher;

	public function __construct(sfEventDispatcher $dispatcher) {
		$this->event_dispatcher = $dispatcher;
	}

	public function format(array $resources) {

		// Setup Zend
		require_once(dirname(__FILE__).'/../../../ZendFramework-1.11.4-minimal/library/Zend/Loader/Autoloader.php');
		Zend_Loader_Autoloader::getInstance();

		// Non-writable /tmp workaround (see http://framework.zend.com/issues/browse/ZF-6668)
		Zend_Locale::disableCache(true);
		
		// Create feed
		$feed = new Zend_Feed_Writer_Feed();
		$feed->setTitle('data.musiques-incongrues.net autogenerated playlist');
		$feed->setLink('http://data.musiques-incongrues.net/collections/links/segments/images/get?sort_field=contributed_at&sort_direction=desc&format=html');
		$feed->setFeedLink('http://data.musiques-incongrues.net/collections/links/segments/images/get?sort_field=contributed_at&sort_direction=desc&format=rss', 'rss');
		$feed->setDescription('TODO');
		$feed->setDateModified(time());
		
		foreach ($resources as $resource) {
			// Skip "num_found"
			if (!is_array($resource)) {
				continue;
			}

			// Create feed entry
			$entry = $feed->createEntry();
			$entry->setTitle($this->getTrackTitle($resource));
			$entry->setLink(sprintf(
				'http://www.musiques-incongrues.net/forum/discussion/%d/%s', 
				$resource['discussion_id'], 
				$this->cleanupString($resource['discussion_name'])
			));
			$entry->addAuthor(
				$resource['contributor_name'], 
				null, 
				sprintf('http://www.musiques-incongrues.net/forum/account/%d/', $resource['contributor_id'])
			);
			$entry->setDateModified(new Zend_Date($resource['contributed_at']));
			$entry->setDateCreated(new Zend_Date($resource['contributed_at']));
			$mimeType = $resource['mime_type'];
			if (!$mimeType) {
				$mimeType = 'unknown';
			}
			$entry->setEnclosure(array('uri' => $resource['url'], 'type' => $mimeType, 'length' => 666));
			
			// Add entry to feed
			$feed->addEntry($entry);
		}

		return $feed;
	}

	// TODO : implement default method in parent class (getItemTitle)
	// TODO : implement specialized formatters : podcast, photofeed, etc (inherit from this class)
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
	
	private function cleanupString($string) {
		$Code = explode(',', '&lt;,&gt;,&#039;,&amp;,&quot;,À,Á,Â,Ã,Ä,&Auml;,Å,Ā,Ą,Ă,Æ,Ç,Ć,Č,Ĉ,Ċ,Ď,Đ,Ð,È,É,Ê,Ë,Ē,Ę,Ě,Ĕ,Ė,Ĝ,Ğ,Ġ,Ģ,Ĥ,Ħ,Ì,Í,Î,Ï,Ī,Ĩ,Ĭ,Į,İ,Ĳ,Ĵ,Ķ,Ł,Ľ,Ĺ,Ļ,Ŀ,Ñ,Ń,Ň,Ņ,Ŋ,Ò,Ó,Ô,Õ,Ö,&Ouml;,Ø,Ō,Ő,Ŏ,Œ,Ŕ,Ř,Ŗ,Ś,Š,Ş,Ŝ,Ș,Ť,Ţ,Ŧ,Ț,Ù,Ú,Û,Ü,Ū,&Uuml;,Ů,Ű,Ŭ,Ũ,Ų,Ŵ,Ý,Ŷ,Ÿ,Ź,Ž,Ż,Þ,Þ,à,á,â,ã,ä,&auml;,å,ā,ą,ă,æ,ç,ć,č,ĉ,ċ,ď,đ,ð,è,é,ê,ë,ē,ę,ě,ĕ,ė,ƒ,ĝ,ğ,ġ,ģ,ĥ,ħ,ì,í,î,ï,ī,ĩ,ĭ,į,ı,ĳ,ĵ,ķ,ĸ,ł,ľ,ĺ,ļ,ŀ,ñ,ń,ň,ņ,ŉ,ŋ,ò,ó,ô,õ,ö,&ouml;,ø,ō,ő,ŏ,œ,ŕ,ř,ŗ,š,ù,ú,û,ü,ū,&uuml;,ů,ű,ŭ,ũ,ų,ŵ,ý,ÿ,ŷ,ž,ż,ź,þ,ß,ſ,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Э,Ю,Я,а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,э,ю,я');
		$Translation = explode(',', ',,,,,A,A,A,A,Ae,A,A,A,A,A,Ae,C,C,C,C,C,D,D,D,E,E,E,E,E,E,E,E,E,G,G,G,G,H,H,I,I,I,I,I,I,I,I,I,IJ,J,K,K,K,K,K,K,N,N,N,N,N,O,O,O,O,Oe,Oe,O,O,O,O,OE,R,R,R,S,S,S,S,S,T,T,T,T,U,U,U,Ue,U,Ue,U,U,U,U,U,W,Y,Y,Y,Z,Z,Z,T,T,a,a,a,a,ae,ae,a,a,a,a,ae,c,c,c,c,c,d,d,d,e,e,e,e,e,e,e,e,e,f,g,g,g,g,h,h,i,i,i,i,i,i,i,i,i,ij,j,k,k,l,l,l,l,l,n,n,n,n,n,n,o,o,o,o,oe,oe,o,o,o,o,oe,r,r,r,s,u,u,u,ue,u,ue,u,u,u,u,u,w,y,y,y,z,z,z,t,ss,ss,A,B,V,G,D,E,YO,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,H,C,CH,SH,SCH,Y,Y,E,YU,YA,a,b,v,g,d,e,yo,zh,z,i,y,k,l,m,n,o,p,r,s,t,u,f,h,c,ch,sh,sch,y,y,e,yu,ya');
		$sReturn = $string;
		$sReturn = str_replace($Code, $Translation, $sReturn);
		$sReturn = urldecode($sReturn);
		$sReturn = preg_replace('/[^A-Za-z0-9 ]/', '', $sReturn);
		$sReturn = str_replace(' ', '-', $sReturn);
		return strtolower(str_replace('--', '-', $sReturn));
	}
}
