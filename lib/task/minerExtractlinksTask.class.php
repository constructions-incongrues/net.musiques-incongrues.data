<?php

class minerExtractlinksTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
    new sfCommandArgument('dsn', sfCommandArgument::REQUIRED),
    ));

    $this->addOptions(array(
    new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    new sfCommandOption('progress', null, sfCommandOption::PARAMETER_NONE, 'Display a progress bar'),
    new sfCommandOption('verbose', null, sfCommandOption::PARAMETER_NONE, 'Display more informations about extraction process'),
    ));

    $this->namespace        = 'miner';
    $this->name             = 'extract-links';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [miner:extract-links|INFO] extracts links in comments from supplied Lussumo Vanilla database comments.
Call it with:

  [php symfony miner:extract-links "driver://user:pass@host/dbname"|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    require_once dirname(__FILE__).'/../vendor/php_arrays/extensions.php';
    $extensions_mimetype_map = $items;

    // Create custom doctrine database connection
    $database = new sfDoctrineDatabase(array('dsn' => $arguments['dsn']));
    $connection = $database->getDoctrineConnection();

    // Fetch all links from datasource
    $q = 'select c.CommentID, c.Body, c.DateCreated, c.AuthUserID, c.DiscussionID, d.Name as DiscussionName, u.Name
    	from LUM_Comment c
    	inner join LUM_User u on c.AuthUserID = u.UserID
    	inner join LUM_Discussion d on c.DiscussionID = d.DiscussionID
        where c.Deleted = "0" and c.WhisperUserID = 0';
    $comments = $connection->fetchAssoc($q);

    // Log
    $this->logSection('info', sprintf('Extracting URLs from %d comments', count($comments)));

    // Switch connection back to project's one
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // For logging purposes
    $urls_found_count = 0;
    $urls_inserted_count = 0;

    // Extract links from each comment
    $comments_parsed = 0;

    // Instanciate progress bar, if user requested so
    if ($options['progress'])
    {
      include 'Console/ProgressBar.php';
      $progress_bar = new Console_ProgressBar(
      '** '.$arguments['dsn'].' %fraction% comments [%bar%] %percent%',
      '=>', '-', 80, count($comments), array('ansi_terminal' => true)
      );
    }

    foreach ($comments as $comment)
    {
      if ($options['progress'])
      {
        // Update progress bar
        $progress_bar->update(++$comments_parsed);
      }

      // Match URLs in comment body
      $matches = array();
      preg_match_all('#\b..?tps?://[-A-Z0-9+&@\#/%?=~_|!:,.;]*[-A-Z0-9+&@\#/%=~_|]#i', $comment['Body'], $matches);
      $urls_found = $matches[0];

      // If links are found, insert them into database
      if (count($urls_found))
      {
        if ($options['verbose'])
        {
          $this->logSection('info', sprintf('Found %d urls in comment %d. Let\'s add them to database.'  , count($urls_found), $comment['CommentID']));
        }
        $urls_found_count += count($urls_found);
        foreach ($urls_found as $url)
        {
          $link = new Link();
          $link->url = $url;
          $link->comment_id = $comment['CommentID'];
          // See http://lucene.472066.n3.nabble.com/Unparseable-date-tp484681p484691.html
          $link->contributed_at = substr($comment['DateCreated'], 0, 10) . "T" . substr($comment['DateCreated'], 11) . "Z";
          $link->contributor_id = $comment['AuthUserID'];
          $link->contributor_name = $comment['Name'];
          $link->discussion_id = $comment['DiscussionID'];
          $link->discussion_name = $comment['DiscussionName'];
          try
          {
            $link->autoPopulate($extensions_mimetype_map);
            $link->save();
            $urls_inserted_count++;
          }
          // Invalid URLs, thrown by $link->autoPopulate()
          catch (InvalidArgumentException $e)
          {
            $this->logSection('error', $e->getMessage());
          }
          // Duplicate URLs
          catch (Doctrine_Connection_Mysql_Exception $e)
          {
            if ($e->getPortableCode() === Doctrine_Core::ERR_ALREADY_EXISTS)
            {
              if ($options['verbose'])
              {
                // TODO : skipping insertion is not ideal as we lose meta informations : link existing in several discussion, posted by several users, etc.
                $this->logSection('info', sprintf('Found duplicate link "%s" in discussion "%d". Skipping insertion', $link->url, $link->discussion_id));
              }
            }
            else
            {
              throw $e;
            }
          }
        }
      }
    }

    // Log
    $this->logSection('info', sprintf('Inserted %d out of %d extracted URLs from %d comments', $urls_inserted_count, $urls_found_count, count($comments)));
  }
}
