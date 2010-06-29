<?php

/**
 * Base class for all extractors.
 */
abstract class CI_Extractor
{
  /**
   * Returns an array of resources fetched from database.
   * Elements in this array will then be fed to getPopulatedLink().
   *
   * @param string $dsn_source Source database DSN
   *
   * @return array
   */
  abstract protected function getResources($dsn_source, $sources = null);

  /**
   * Returns text from which URLs will be extracted.
   *
   * @param mixed $resource
   *
   * @return string
   */
  abstract protected function getResourceText($resource);

  /**
   * Returns populated Link instance, ready to be saved to database.
   *
   * @param string $url
   * @param mixed  $resource
   *
   * @return Link
   */
  abstract protected function getPopulatedLink($url, $resource);

  /**
   * Returns total number of resources that will be parsed for URL extraction.
   *
   * @return int
   */
  abstract protected function countResources($dsn_source, $since = null);

  /**
   * @var array
   */
  protected $extensions_mimetype_map = array();

  /**
   * @var sfEventDispatcher
   */
  protected $event_dispatcher;

  /**
   * @var sfProjectConfiguration
   */
  protected $configuration;

  /**
   * @var array
   */
  private $resources;

  /**
   * @var int
   */
  private $cursor = 0;

  /**
   * Instanciates and configures extractor.
   *
   * @param sfEventDispatcher      $event_dispatcher
   * @param sfProjectConfiguration $configuration
   */
  public function __construct(sfEventDispatcher $event_dispatcher, sfProjectConfiguration $configuration)
  {
    // Inject
    $this->event_dispatcher = $event_dispatcher;
    $this->configuration  = $configuration;

    // Provides extention <=> mimetype array maps
    require_once dirname(__FILE__).'/../php_arrays/extensions.php';
    $this->extensions_mimetype_map = $items;
  }

  /**
   * Instanciates connection.
   *
   * @param string $dsn
   * @return sfDoctrineConnection
   */
  protected function getConnection($dsn)
  {
    // Create custom doctrine database connection
    $database = new sfDoctrineDatabase(array('dsn' => $dsn));

    return $database->getDoctrineConnection();
  }

  /**
   * Extracts URLs from supplied text.
   *
   * @param string $text
   * @return array
   */
  protected function extractUrls($text)
  {
    $matches = array();
    preg_match_all('#\b..?tps?://[-A-Z0-9+&@\#/%?=~_|!:,.;]*[-A-Z0-9+&@\#/%=~_|]#i', $text, $matches);
    $urls_found = $matches[0];

    return $urls_found;
  }

  /**
   * Extracts links from source database and inserts them into links collection.
   *
   * @param string $dsn_source
   * @param string $connection_dest
   * @param string $since           Extract urls from resources updated or created since this date (Y-m-d H:i:s)
   */
  public function extract($dsn_source, $connection_dest, $since = null)
  {
    if (!$this->resources)
    {
      // Retrieve comments from database
      $resources = $this->getResources($dsn_source, $since);
      $this->log(sprintf('Extracting URLs from %d resources using extractor "%s"', count($resources), get_class($this)));
      $this->resources = $resources;
    }

    // Switch to destination database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($connection_dest)->getConnection();

    // Extract URLs from each resource
    $resources_count = count($this->resources);
    while ($this->cursor < $resources_count)
    {
      $resource = $this->resources[$this->cursor++];

      // Initial extraction informations array
      $resource_extraction_infos = array(
      	'urls_found_count' => null
      );

      // Extract URLs from resource text
      $urls = $this->extractUrls($this->getResourceText($resource));

      // Create link object and add it to collection
      foreach ($urls as $url)
      {
        $link = $this->getPopulatedLink($url, $resource);
        try
        {
          $link->save();
        }
        // Invalid URLs, thrown by $link->autoPopulate()
        catch (InvalidArgumentException $e)
        {
          $this->log($e->getMessage());
        }
        // Duplicate URLs
        catch (Doctrine_Connection_Mysql_Exception $e)
        {
          if ($e->getPortableCode() === Doctrine_Core::ERR_ALREADY_EXISTS)
          {
            // NEXT : skipping insertion is not ideal as we lose meta informations : link existing in several discussion, posted by several users, etc.
            $this->log(sprintf('Found duplicate link "%s" in discussion "%d". Skipping insertion', $link->url, $link->discussion_id));
          }
          else
          {
            throw $e;
          }
        }
      }

      // Update resource extraction informations
      $resource_extraction_infos = array(
        'resources_parsed_count' => $this->cursor,
      	'urls_found_count'       => count($urls)
      );

      return $resource_extraction_infos;
    }

    $this->resources = null;
  }

  /**
   * Logs a message.
   * Logging is performed by broadcasting "log" events.
   *
   * @param string $message
   *
   * @broadcasts log
   */
  protected function log($message)
  {
    $this->event_dispatcher->notify(new sfEvent($this, 'log', array('message' => $message)));
  }
}