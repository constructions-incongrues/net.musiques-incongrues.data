<?php

class minerExpandLinksTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('progress', null, sfCommandOption::PARAMETER_NONE, 'Display a progress bar'),
      new sfCommandOption('verbose', null, sfCommandOption::PARAMETER_NONE, 'Display more informations about extraction process'),
      new sfCommandOption('all', null, sfCommandOption::PARAMETER_NONE, 'Expand all links in database. By default, only new links are expanded'),
      new sfCommandOption('with-unavailable', null, sfCommandOption::PARAMETER_NONE, 'When expanding all links (--all), also include links previously marked as unavailable'),
      // TODO : add --older-than option
    ));

    $this->namespace        = 'miner';
    $this->name             = 'expand-links';
    // TODO : write descriptions
    $this->briefDescription = 'Expands informations about links by crawling their URLs';
    $this->detailedDescription = <<<EOF

Use cases :
 * Expand new urls : [php symfony miner:expand-links|INFO]
 * Expand all urls (a word about --with-unavailable) : [php symfony miner:expand-links --all|INFO]
 * Expand all urls, including those previously marked as unavailable : [php symfony miner:expand-links --all --with-unavailable|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // Open database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // Build query for fetching links from database
    $q = Doctrine_Query::create()
      ->select('l.url')
      ->from('Link l');
    if (!$options['all'])
    {
      $q->where('l.expanded_at is null');
    }
    if (!$options['with-unavailable'])
    {
      $q->andWhere('l.is_available != 0 or l.is_available is null');
    }

    // Fetch links from database
    $links_count = $q->count();
    $links = $q->execute(null, Doctrine_Core::HYDRATE_ON_DEMAND);
    $q->free();
    $this->logSection('info', sprintf('Expanding %s links', $links_count));

    // Instanciate progress bar, if user requested so
    $links_expanded = 0;
    if ($options['progress'])
    {
      include 'Console/ProgressBar.php';
      $progress_bar = new Console_ProgressBar(
      '** Links %fraction% comments [%bar%] %percent% | ',
      '=>', '-', 80, $links_count, array('ansi_terminal' => true)
      );
      $progress_bar->update($links_expanded);
    }

    // Launch a HEAD request on each link, and use data in response headers to update informations about link in database
    // TODO : move crawling code to dedicated class. and then create miner:crawl-url task
    require 'HTTP/Request2.php';
    $request = new HTTP_Request2(null, HTTP_Request2::METHOD_HEAD, array('follow_redirects' => true));
    $request->setHeader('user-agent', 'vanilla-miner/1.1 (https://launchpad.net/vanilla-miner)');

    foreach ($links as $link)
    {
      $link->expanded_at = time();
      try
      {
        $request->setUrl($link->url);
        $response = $request->send();
        if (200 == $response->getStatus())
        {
          if ($options['progress'])
          {
            $this->log(sprintf('[%d] %s', $response->getStatus(), $link->url));
          }
          else
          {
            $this->logSection('info', sprintf('[%d] %s - Updating metadata, marking as available', $response->getStatus(), $link->url));
          }

          // Extract meaningful informations from server response
          $header = $response->getHeader();
          $header = $this->normalizeHeader($header);
          $link->mime_type = $this->getMimeType($header);

          // Mark link as available
          $link->is_available = true;

          // Save link to database
          $link->replace();
        }
        else
        {
          if ($options['progress'])
          {
            $this->log(sprintf('[%d] %s', $response->getStatus(), $link->url));
          }
          else
          {
            $this->logSection('notice', sprintf(
                '[%d] %s (%d %s) - Marking as unavailable',
                $response->getStatus(),
                $link->url,
                $response->getStatus(),
                $response->getReasonPhrase()
              )
            );
          }
          $link->is_available = false;
          $link->replace();
        }
      }
      catch (HTTP_Request2_Exception $e)
      {
        if ($options['progress'])
        {
          $this->log(sprintf('[ERR] %s', $link->url));
        }
        else
        {
          $this->logSection('error', sprintf('[ERR] Received exception with message "%s" for link "%s" - Marking as unavailable.', $e->getMessage(), $link->url));
        }
        $link->is_available = false;
        $link->replace();
      }

      // Update progress bar
      if ($options['progress'])
      {
        $progress_bar->update(++$links_expanded);
      }

    }
  }

  private function normalizeHeader(array $header)
  {
    // Make all header names lower case
    $header_rev = array_flip($header);
    array_walk($header_rev, create_function('&$item, $key', 'strtolower($item);'));
    $header = array_flip($header_rev);

    return $header;
  }

  private function getMimeType(array $header)
  {
    $mime_type = null;

    if (isset($header['content-type']))
    {
      $mime_type = $header['content-type'];

      // Extract mime type from content-type header
      // TODO : use a regular expression instead of this crappy flow
      $matches = array();
      if (strpos($header['content-type'], 'charset') !== false)
      {
        if (preg_match('/(.+); ?charset=.+/i', $header['content-type'], $matches))
        {
          $mime_type = $matches[1];
        }
      }
    }

    return $mime_type;
  }
}