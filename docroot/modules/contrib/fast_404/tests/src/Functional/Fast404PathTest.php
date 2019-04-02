<?php

namespace Drupal\Tests\fast404\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the path checking functionality.
 *
 * @group fast404
 */
class Fast404PathTest extends BrowserTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = ['fast404'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Tests the Url not found markup.
   */
  public function testPathCheck() {
    // Ensure path check isn't activated by default.
    $this->drupalGet('/does_not_exist');
    $this->assertSession()->statusCodeEquals(404);
    $this->assertSession()->pageTextContains('The requested page could not be found.');
    \Drupal::service('cache.page')->deleteAll();

    $settings['settings']['fast404_path_check'] = (object) [
      'value' => TRUE,
      'required' => TRUE,
    ];
    $settings['settings']['fast404_html'] = (object) [
      'value' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>',
      'required' => TRUE,
    ];
    $this->writeSettings($settings);
    
    $this->drupalGet('/does_not_exist');
    $this->assertSession()->statusCodeEquals(404);
    $this->assertSession()->pageTextContains('Not Found');
    $this->assertSession()->responseContains('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "/does_not_exist" was not found on this server.</p></body></html>');
  }

}
