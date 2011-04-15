<?php

class frontendConfiguration extends sfApplicationConfiguration
{
	public function configure()
	{
		// Setup Zend framework autoloading
		set_include_path(sprintf('%s/vendor/ZendFramework-1.11.4-minimal/library/'.PATH_SEPARATOR.get_include_path(), sfConfig::get('sf_lib_dir')));
		require_once(sprintf('%s/vendor/ZendFramework-1.11.4-minimal/library/Zend/Loader/Autoloader.php', sfConfig::get('sf_lib_dir')));
		Zend_Loader_Autoloader::getInstance();
	}
}
