<?php
class CI_Search_Link_ResourceGroup_Images extends CI_Search_Link_ResourceGroup
{
  protected function buildLuceneCriteria(sfParameterHolder $parameters)
  {
    $c = parent::buildLuceneCriteria($parameters);
    $c->addField('mime_type', 'image');
    
    return $c;
  } 
}