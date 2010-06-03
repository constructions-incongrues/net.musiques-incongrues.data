<?php

/**
 * Installs application configuration and data.
 * Useful for initial deployment and development.
 *
 * @package    Plugin
 * @subpackage Task
 */
class ciInstallTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'Whether to force dropping of the database'),
      new sfCommandOption('with-db', null, sfCommandOption::PARAMETER_NONE, 'Whether execute database related tasks')
    ));

    $this->addArguments(array(
      new sfCommandArgument('TOKENS_FILE', sfCommandArgument::REQUIRED, 'Path to file containing properties'),
    ));

    $this->namespace        = 'miner';
    $this->name             = 'install';
    $this->briefDescription = 'Installs Vanilla Miner project.';
    $this->detailedDescription = <<<EOF

[Handle with care, this task will WIPE your data|ERROR] !

The [miner:install|INFO] is a meta task that resets application data and configuration.

An administrator with a random username and password will be created.

Call it with:

  [php ./symfony miner:install|INFO] /path/to/tokens.ini
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $continue = true;

    // Make sure that user wants to destroy all project data
    if ($options['with-db'])
    {
      if (!$options['no-confirmation'])
      {
        $continue = $this->askConfirmation(
          "                                                  \n".
          " Executing this task will erase all existing data. \n".
          " Are you sure you want to proceed ? (y/N)          \n".
          "                                                  ", 'QUESTION', false);
      }
    }

    if ($continue)
    {
      // Rebuild configuration files
      $this->runTask('configure:dist', array('TOKENS_FILE' => $arguments['TOKENS_FILE']));

      // Drop existing database
      if ($options['with-db'])
      {
        $this->runTask('doctrine:drop-db', array(), array('no-confirmation' => true));
      }

      // Generate classes
      $this->runTask('doctrine:build', array(), array('all-classes'));

      // Create database
      if ($options['with-db'])
      {
        $this->runTask('doctrine:build-db');
      }

      // Create schema
      if ($options['with-db'])
      {
        $this->runTask('doctrine:migrate');
      }

      // Load base data
      if ($options['with-db'])
      {
        $this->runTask('doctrine:data-load', array(dirname(__FILE__).'/../../data/fixtures/install'));
      }

      // Publish plugins assets
      $this->runTask('plugin:publish-assets');

      // Fix project permissions
      $this->runTask('project:permissions');

      $this->runTask('cache:clear');
    }
    else
    {
      $this->logSection('info', 'Aborting...');
    }
  }
}
