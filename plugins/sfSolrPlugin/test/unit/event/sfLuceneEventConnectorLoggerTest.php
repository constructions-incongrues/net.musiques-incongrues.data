<?php
/*
 * This file is part of the sfLucenePlugin package
 * (c) 2007 - 2008 Carl Vondrick <carl@carlsoft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
  * @package sfLucenePlugin
  * @subpackage Test
  * @author Carl Vondrick
  * @version SVN: $Id: sfLuceneEventConnectorLoggerTest.php 24784 2009-12-02 09:58:03Z rande $
  */

require dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new limeade_test(6, limeade_output::get());

class FooListener
{
  public $event;

  public function listen($event)
  {
    $this->event = $event;
  }
}

$source = new sfEventDispatcher();
$target = new sfEventDispatcher();
$formatter = new sfFormatter;

$connector = new sfLuceneEventConnectorLogger($source, 'foo', $target, 'bar', $formatter);

$t->ok($source->hasListeners('foo'), '__construct() connects a listener to the source');

$subject = 'Fabien';
$params = array('What is %s dude?  I am %d years %s now', 'down', '5', 'young');

$listener = new FooListener;
$target->connect('bar', array($listener, 'listen'));

$source->notify(new sfEvent($subject, 'foo', $params));

$t->isa_ok($listener->event, 'sfEvent', 'calling a linked event calls target');
$t->is($listener->event->getSubject(), $subject, 'calling a linked event sends correct subject');
$t->is($listener->event->getName(), 'bar', 'calling a linked event sends correct name');
$t->is($listener->event->getParameters(), array('What is down dude?  I am 5 years young now'), 'calling a linked logged event parses message correctly');

$connector = new sfLuceneEventConnectorLogger($source, 'foo', $target, 'bar', $formatter, 'Section');
$source->notify(new sfEvent($subject, 'foo', $params));

$t->is($listener->event->getParameters(), array('>> Section   What is down dude?  I am 5 years young now'), 'calling a linked logged event parses message correctly');