<?php

namespace Drupal\system\Tests\Installer;

use Drupal\simpletest\InstallerTestBase;

/**
 * Provides a base class for testing installing from existing configuration.
 */
abstract class InstallerExistingConfigTestBase extends InstallerTestBase {

  /**
   * {@inheritdoc}
   */
  protected function installParameters() {
    $parameters = parent::installParameters();

    // The options that change configuration are disabled when installing from
    // existing configuration.
    unset($parameters['forms']['install_configure_form']['site_name']);
    unset($parameters['forms']['install_configure_form']['site_mail']);
    unset($parameters['forms']['install_configure_form']['update_status_module']);

    return $parameters;
  }

  /**
   * Confirms that the installation installed the configuration correctly.
   */
  public function testConfigSync() {
    // After installation there is no snapshot and nothing to import.
    $change_list = $this->configImporter()->getStorageComparer()->getChangelist();
    $expected = [
      'create' => [],
      // The system.mail is changed configuration because the test system
      // changes it to ensure that mails are not sent.
      'update' => ['system.mail'],
      'delete' => [],
      'rename' => [],
    ];
    $this->assertEqual($expected, $change_list);
  }

}
