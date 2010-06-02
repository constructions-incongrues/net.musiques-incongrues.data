<?php
class CI_Search_Link_ResourceGroup_Youtube extends CI_Search_Link_ResourceGroup
{
  protected function buildLuceneCriteria(sfParameterHolder $parameters)
  {
    $c = parent::buildLuceneCriteria($parameters);
    $c->addField('domain_parent', 'youtube.com');
    
    return $c;
  }
}