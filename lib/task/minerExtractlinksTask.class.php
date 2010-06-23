<?php

/**
 * Symfony task for extracting urls using an extraction drivers.
 * Extracted urls will be added to miner's instance links collection.
 */
class minerExtractlinksTask extends sfBaseTask
{
  /**
   * Configures task.
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('dsn', sfCommandArgument::REQUIRED),
    ));

    // TODO add a --verbose switch
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('extraction-driver', null, sfCommandOption::PARAMETER_REQUIRED, 'Extraction driver class name', 'CI_Extractor_LussumoVanilla1'),
      new sfCommandOption('progress', null, sfCommandOption::PARAMETER_NONE, 'Displays a progress bar'),
    ));

    $this->namespace        = 'miner';
    $this->name             = 'extract-links';
    $this->briefDescription = 'Extracts links from datasource';
    $this->detailedDescription = <<<EOF
Call it with:

  [php symfony miner:extract-links --extraction-driver=My_Extraction_Driver|INFO]
EOF;
  }

  /**
   * Executes task.
   *
   * @param array $arguments
   * @param array $options
   */
  protected function execute($arguments = array(), $options = array())
  {
    // Setup logging
    $this->dispatcher->connect('log', array($this, 'onLog'));

    // TODO : autoload classes
    $driver_classname_parts = explode('_', $options['extraction-driver']);
    require sprintf('%s/vendor/CI/Extractor.php', sfConfig::get('sf_lib_dir'));
    require sprintf('%s/vendor/CI/Extractor/%s.php', sfConfig::get('sf_lib_dir'), array_pop($driver_classname_parts));

    // Sanity checks
    if (!class_exists($options['extraction-driver']))
    {
      throw new InvalidArgumentException(sprintf('Class "%s" does not exist', $options['extraction-driver']));
    }

    // Instanciate and configure extraction engine
    $extractor = new $options['extraction-driver']($this->dispatcher, $this->configuration);

    // Extraction statistics
    $urls_found_count = 0;
    $resources_parsed = 0;
    $resources_total = $extractor->countResources($arguments['dsn']);

    // Instanciate an configure progress bar
    if ($options['progress'])
    {
      include 'Console/ProgressBar.php';
      $progress_bar = new Console_ProgressBar(
        '** '.$arguments['dsn'].' %fraction% resources [%bar%] %percent% | ',
        '=>', '-', 80, $resources_total, array('ansi_terminal' => true)
      );
    }

    // Extract resources from source and insert them in Links database
    while ($resource_extraction_info = $extractor->extract($arguments['dsn'], $options['connection']))
    {
      // Update extraction statistics
      $urls_found_count += $resource_extraction_info['urls_found_count'];

      // Update progress bar
      if ($options['progress'])
      {
        $progress_bar->update($resource_extraction_info['resources_parsed_count']);
      }
    }

    // Log
    $this->logSection('extract', sprintf('%d URLs where extracted from %d resources', $urls_found_count, $resources_total));
  }

  /**
   * Listens for "log" events and logs messages to stdout.
   *
   * @param sfEvent $event
   */
  public function onLog(sfEvent $event)
  {
    $this->logSection('extract', $event['message']);
  }
}
