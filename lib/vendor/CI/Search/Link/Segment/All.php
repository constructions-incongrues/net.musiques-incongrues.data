<?php
class CI_Search_Link_Segment_All extends CI_Search_Link_Segment
{
  protected function buildLuceneCriteria(sfParameterHolder $parameters)
  {
    $c = parent::buildLuceneCriteria($parameters);
    $c->addField('sfl_guid', '[* TO *]', sfLuceneCriteria::TYPE_AND, true);
    
    return $c;
  }
}