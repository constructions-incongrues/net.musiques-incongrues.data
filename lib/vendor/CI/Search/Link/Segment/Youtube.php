<?php
class CI_Search_Link_Segment_Youtube extends CI_Search_Link_Segment
{
  protected function buildLuceneCriteria(sfParameterHolder $parameters)
  {
    $c = parent::buildLuceneCriteria($parameters);
    $c->addField('domain_parent', 'youtube.com');
    
    return $c;
  }
}