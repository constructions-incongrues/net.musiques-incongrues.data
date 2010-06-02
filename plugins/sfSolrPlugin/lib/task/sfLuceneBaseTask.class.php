<?php
/*
 * This file is part of the sfLucenePlugin package
 * (c) 2007 - 2008 Carl Vondrick <carl@carlsoft.net>
 * (c) 2009 - Thomas Rabaix <thomas.rabaix@soleoweb.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../sfLucene.class.php');
require_once(dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php');

/**
* This class represents a base task for all sfLucene tasks.
*
* @author Carl Vondrick <carl@carlsoft.net>
* @author Thomas Rabaix <thomas.rabaix@soleoweb.com>
* @package sfLucenePlugin
* @subpackage Tasks
* @version SVN: $Id: sfLuceneBaseTask.class.php 18881 2009-06-03 08:33:59Z rande $
*/

abstract class sfLuceneBaseTask extends sfBaseTask
{
  protected function bootstrapSymfony($app, $env, $debug = true)
  {

    $configuration = ProjectConfiguration::getApplicationConfiguration($app, $env, $debug);

    sfContext::createInstance($configuration);
  }
  
  protected function standardBootstrap($app, $env = 'search')
  {
    $this->bootstrapSymfony($app, $env, true);
  }

  protected function setupEventDispatcher($search)
  {
    $source = $search->getEventDispatcher();
    $target = $this->dispatcher;
    $formatter = $this->formatter;

    new sfLuceneEventConnectorLogger($source, 'lucene.log', $target, 'command.log', $formatter, 'Lucene');
    new sfLuceneEventConnectorLogger($source, 'indexer.log', $target, 'command.log', $formatter, 'Indexer');
  }
}