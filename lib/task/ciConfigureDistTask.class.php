<?php

/**
 * Replaces tokens in distribution files.
 *
 * @package    ciToolkit
 * @subpackage Task
 */
class ciConfigureDistTask extends sfBaseTask
{
    /**
     * @see sfTask
     */
    protected function configure()
    {
        $this->addArguments(array(
        new sfCommandArgument('TOKENS_FILE', sfCommandArgument::REQUIRED, 'Path to file containing properties'),
        ));

        $this->addOption('dry-run', 'n', sfCommandOption::PARAMETER_NONE, 'Do not perform replacement, just display what would be done');
        $this->addOption('suffix', 's', sfCommandOption::PARAMETER_OPTIONAL, 'Distribution file suffix', '-dist');
        $this->addOption('delimiter', 'd', sfCommandOption::PARAMETER_OPTIONAL, 'Token delimiter', '@');

        $this->namespace = 'configure';
        $this->name = 'dist';

        $this->briefDescription = 'Replaces tokens in distribution files';

        $this->detailedDescription = <<<EOF
The [configure:dist|INFO] task searches for distribution files in project, and performs tokens replacements within:

  [./symfony configure:dist ./config/properties.prod.ini|INFO]
EOF;
    }

    /**
     * Executes command.
     *
     * @see sfTask
     */
    protected function execute($arguments = array(), $options = array())
    {
        // Check that properties file is readable
        if (!is_readable($arguments['TOKENS_FILE']))
        {
            throw new sfCommandException(sprintf('"%s" file is not readable', $arguments['TOKENS_FILE']));
        }

        // Get list of tokens from property file
        $tokens = parse_ini_file($arguments['TOKENS_FILE']);

        // Search for distribution files
        $finder = new sfFinder();
        $files = $finder->name('*'.$options['suffix'])->in(sfConfig::get('sf_root_dir'));
        $this->logSection('info', sprintf('Replacing %d tokens in %d files', count($tokens), count($files), sfConfig::get('sf_root_dir')));

        if (!$options['dry-run'])
        {
            // Create non -dist files
            $copied_files = array();
            foreach ($files as $file)
            {
                $new_file = substr($file, 0, strlen($file) - strlen($options['suffix']));
                $this->getFilesystem()->copy($file, $new_file, array('override' => true));
                $copied_files[] = $new_file;
            }

            // Perform replacements
            $this->getFilesystem()->replaceTokens($copied_files, $options['delimiter'], $options['delimiter'], $tokens);
        }
        else
        {
            $this->logSection('info', 'Dry run mode enabled, NOT performing replacements');
        }
    }
}