<?php
/**
 *
 * CAPTCHA 4 post. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\captcha4post\tests\functional;

/**
 * @group functional
 */
class view_test extends \phpbb_functional_test_case
{
	/**
	 * @inheritdoc
	 */
	protected static function setup_extensions()
	{
		return ['davidiq/captcha4post'];
	}

	/**
	 * Test crawls the extension's page route /demo/ with the variable: foo
	 * Asserts that only the expected text is found, "Hello foo"
	 */
	public function test_view_foo()
	{
		$crawler = self::request('GET', 'app.php/demo/foo');
		$this->assertContains('foo', $crawler->filter('h2')->text());

		$this->add_lang_ext('davidiq/captcha4post', 'common');
		$this->assertContains($this->lang('CAPTCHA4POST_HELLO', 'foo'), $crawler->filter('h2')->text());
		$this->assertNotContains($this->lang('CAPTCHA4POST_GOODBYE', 'foo'), $crawler->filter('h2')->text());

		$this->assertNotContainsLang('ACP_CAPTCHA4POST_GOODBYE', $crawler->filter('h2')->text());
	}

	/**
	 * Test crawls the extension's page route /demo/ again with a new variable: bar
	 * Asserts that only the expected text "bar" is found and that "foo" is no longer present.
	 */
	public function test_view_bar()
	{
		$crawler = self::request('GET', 'app.php/demo/bar');
		$this->assertNotContains('foo', $crawler->filter('h2')->text());
		$this->assertContains('bar', $crawler->filter('h2')->text());
	}
}
