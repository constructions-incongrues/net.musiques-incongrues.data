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
    ));

    $this->namespace        = 'miner';
    $this->name             = 'extract-links';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [miner:extract-links|INFO] task does things.
Call it with:

  [php symfony miner:extract-links|INFO]
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
    // TODO : do not extract deleted comments nor whispers
    $q = 'select c.CommentID, c.Body, c.DateCreated, c.AuthUserID, c.DiscussionID, d.Name as DiscussionName, u.Name 
    	from LUM_Comment c 
    	inner join LUM_User u on c.AuthUserID = u.UserID
    	inner join LUM_Discussion d on c.DiscussionID = d.DiscussionID';
    $comments = $connection->fetchAssoc($q);
    
    // Log
    $this->logSection('info', sprintf('Extracting URLs from %d comments', count($comments)));
    
    // Switch connection back to project's one
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    // For logging purposes
    $urls_found_count = 0;
    
    // Extract links from each comment
    foreach ($comments as $comment)
    {
      $matches = array();
      preg_match_all('#\bhttps?://[-A-Z0-9+&@\#/%?=~_|!:,.;]*[-A-Z0-9+&@\#/%=~_|]#i', $comment['Body'], $matches);
      $urls_found = $matches[0];

      // If links are found, insert them into database
      
      if (count($urls_found))
      {
        
        //$this->logSection('info', sprintf('Found %d urls in comment %d. Let\'s add them to database.'  , count($urls_found), $comment['CommentID']));
        $urls_found_count += count($urls_found);
        foreach ($urls_found as $url)
        {
          $link = new Link();
          $link->url = $url;
          $link->comment_id = $comment['CommentID'];
          $link->contributed_at = $comment['DateCreated'];
          $link->contributor_id = $comment['AuthUserID'];
          $link->contributor_name = $comment['Name'];
          $link->discussion_id = $comment['DiscussionID'];
          $link->discussion_name = $comment['DiscussionName'];
          try
          {
            $link->autoPopulate($extensions_mimetype_map);
            $link->save();
          }
          catch (InvalidArgumentException $e)
          {
            $this->logSection('error', $e->getMessage());
          }
        }
      }
    }
    
    // Log
    $this->logSection('info', sprintf('%d URLs where extracted from %d comments', $urls_found_count, count($comments)));
  }
}
