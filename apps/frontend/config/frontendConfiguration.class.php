<?php

class frontendConfiguration extends sfApplicationConfiguration
{
	public function configure()
	{
		 $this->getEventDispatcher()->connect('ci.miner.resource.new', array($this, 'onNewResource'));
	}
	
	public function onNewResource(sfEvent $event)
	{
		// URL to updated feed
		$urlTopic = sprintf(
			'%s%s/collections/links/segments/all/get?format=xmlfeed&formatter_options[type]=atom', 
			$event['request']->getUriPrefix(),
			$event['request']->getRelativeUrlRoot()
		);	
		
		// Setup Zend
		// TODO : make sure embedded Zend Framework is used, not system wide
		require_once(sprintf('%s/vendor/ZendFramework-1.11.4-minimal/library/Zend/Loader/Autoloader.php', sfConfig::get('sf_lib_dir')));
		Zend_Loader_Autoloader::getInstance();
		
		$publisher = new Zend_Feed_Pubsubhubbub_Publisher;
		foreach (sfConfig::get('app_pubsub_hubs') as $urlHub) {
			$publisher->addHubUrl($urlHub);
		}
		$publisher->addUpdatedTopicUrl($urlTopic);
		$publisher->notifyAll();
		 
		if (!$publisher->isSuccess()) {
		    // check for errors
		    $errors     = $publisher->getErrors();
		    $failedHubs = array();
		    foreach ($errors as $error) {
		        $failedHubs[] = $error['hubUrl'];
		    }
		} else {
			sfContext::getInstance()->getLogger()->info(sprintf('Successfuly posted update notice on "%s" to hub at "%s"', $urlTopic, $urlHub));
		}
	}
}
