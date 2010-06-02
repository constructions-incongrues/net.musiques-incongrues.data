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
  * @version SVN: $Id: sfLuceneHelperTest.php 28199 2010-02-22 22:33:21Z rande $
  */

require dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new limeade_test(7, limeade_output::get());

sfLoader::loadHelpers(array('sfLucene'));

$t->diag('testing highlighting');

$t->is(highlight_result_text('Hello.  This is a pretty <em class="thing">awesome</em> thing to be talking about.', 'thing talking'), 'Hello.  This is a pretty awesome <strong class="highlight">thing</strong> to be <strong class="highlight">talking</strong> about.', 'highlight_result_text() highlights text and strips out HTML');

$t->is(highlight_result_text('Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. This is a pretty <em class="thing">awesome</em> thing to be talking about.  Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. Foo bar. ', 'thing talking', 50), '...is is a pretty awesome <strong class="highlight">thing</strong> to be <strong class="highlight">talking</strong> about....', 'highlight_result_text() highlights and truncates text');

$t->is(highlight_keywords('Hello.  This is a pretty <em class="thing">awesome</em> thing to be talking about.', 'thing talking'), 'Hello.  This is a pretty <em class="thing">awesome</em> <strong class="highlight">thing</strong> to be <strong class="highlight">talking</strong> about.', 'highlight_kewyords() highlights text');


$t->diag('testing query string manipulation');

$t->is(add_highlight_qs('test/model', 'foo bar'), 'test/model?sf_highlight=foo bar', 'add_highlight_qs() adds a query string correctly');

$t->is(add_highlight_qs('test/model?a=b', 'foo bar'), 'test/model?a=b&sf_highlight=foo bar', 'add_highlight_qs() handles existing query strings');

$t->is(add_highlight_qs('test/model#anchor', 'foo bar'), 'test/model?sf_highlight=foo bar#anchor', 'add_highlight_qs() handles anchors');

$t->is(add_highlight_qs('test/model?a=b#anchor', 'foo bar'), 'test/model?a=b&sf_highlight=foo bar#anchor', 'add_highlight_qs() handles anchors and existing query strings');