<?php

/**
 * Extractor dedicated gather URLs from Lussumo Vanilla 1 forum comments.
 */
class CI_Extractor_LussumoVanilla1 extends CI_Extractor
{
  /**
   * Returns an array of resources fetched from database.
   * Elements in this array will then be fed to getPopulatedLink().
   *
   * @param string $dsn_source Source database DSN
   *
   * @return array
   *
   * NEXT : make table prefix configurable
   */
  protected function getResources($dsn_source)
  {
    // TODO : do not select whispered and deleted comments
    $q = 'select c.CommentID, c.Body, c.DateCreated, c.AuthUserID, c.DiscussionID, d.Name as DiscussionName, u.Name
    	from LUM_Comment c
    	inner join LUM_User u on c.AuthUserID = u.UserID
    	inner join LUM_Discussion d on c.DiscussionID = d.DiscussionID';

    return $this->getConnection($dsn_source)->fetchAssoc($q);
  }

  /**
   * Returns text from which URLs will be extracted.
   *
   * @param mixed $resource
   *
   * @return string
   */
  protected function getResourceText($resource)
  {
   return $resource['Body'];
  }

  /**
   * Returns populated Link instance, ready to be saved to database.
   *
   * @param string $url
   * @param mixed  $resource
   *
   * @return Link
   */
  protected function getPopulatedLink($url, $resource)
  {
    $link = new Link();
    $link->url = $url;
    $link->comment_id = $resource['CommentID'];
    // See http://lucene.472066.n3.nabble.com/Unparseable-date-tp484681p484691.html
    $link->contributed_at = substr($resource['DateCreated'], 0, 10) . "T" . substr($resource['DateCreated'], 11) . "Z";
    $link->contributor_id = $resource['AuthUserID'];
    $link->contributor_name = $resource['Name'];
    $link->discussion_id = $resource['DiscussionID'];
    $link->discussion_name = $resource['DiscussionName'];
    try
    {
      $link->autoPopulate($this->extensions_mimetype_map);
    }
    catch (InvalidArgumentException $e)
    {
      $this->log($e->getMessage());
    }

    return $link;
  }

  /**
   * Returns total number of resources that will be parsed for URL extraction.
   *
   * @return int
   */
  public function countResources($dsn)
  {
    // TODO : do not select whispered and deleted comments
    $q = 'select count(c.CommentID) from LUM_Comment c';

    return (int)$this->getConnection($dsn)->fetchOne($q);
  }
}