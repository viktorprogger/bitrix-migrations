<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface;
use Arrilot\BitrixMigrations\Repositories\FileRepository;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCommand extends AbstractMigrationCommand
{
    /**
     * Directory where migration files are stored.
     *
     * @var string
     */
    protected $dir;

    /**
     * Files interactions.
     *
     * @var FileRepositoryInterface
     */
    protected $files;

    /**
     * Array of available migration file templates.
     *
     * @var array
     */
    protected $templates = [];

    /**
     * Constructor.
     *
     * @param array                   $config
     * @param FileRepositoryInterface $files
     */
    public function __construct($config, FileRepositoryInterface $files = null)
    {
        $this->dir = $config['dir'];
        $this->files = $files ?: new FileRepository();

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('make')
            ->setDescription('Create a new migration file')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the migration'
            )
            ->addOption(
                'template',
                't',
                InputOption::VALUE_REQUIRED,
                'Migration template'
            );
    }

    /**
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
    {
        $this->files->createDirIfItDoesNotExist($this->dir);

        $fileName = $this->constructFileName($this->input->getArgument('name'));
        $className = $this->getMigrationClassNameByFileName($fileName);

        $templateName = $this->getCurrentTemplateName();

        $template = $this->files->getContent($this->templatePath($templateName));
        $template = $this->replacePlaceholdersInStub($template, $className);

        $this->createMigrationFile($fileName, $template);

        $this->message("<info>Migration created:</info> {$fileName}.php");
    }

    /**
     * Path to the file where a template is located.
     *
     * @param string $templateName
     *
     * @return string
     */
    protected function templatePath($templateName)
    {
        return $this->templates[$templateName]['path'];
    }

    /**
     * Getter for templates property.
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Dynamically register migration template.
     *
     * @param array $template
     *
     * @return void
     */
    public function registerTemplate($template)
    {
        $template = $this->normalizeTemplate($template);

        $this->templates[$template['name']] = $template;

        $this->registerTemplateAliases($template, $template['aliases']);
    }

    /**
     * Check template fields and normalize them.
     *
     * @param $template
     *
     * @return array
     */
    protected function normalizeTemplate($template)
    {
        if (empty($template['name'])) {
            throw new InvalidArgumentException('Impossible to register a template without "name"');
        }

        if (empty($template['path'])) {
            throw new InvalidArgumentException('Impossible to register a template without "path"');
        }

        $template['description'] = isset($template['description']) ? $template['description'] : '';
        $template['aliases'] = isset($template['aliases']) ? $template['aliases'] : [];
        $template['is_alias'] = false;

        return $template;
    }

    /**
     * Register template aliases.
     *
     * @param array $template
     * @param array $aliases
     *
     * @return void
     */
    protected function registerTemplateAliases($template, array $aliases = [])
    {
        foreach ($aliases as $alias) {
            $template['is_alias'] = true;
            $template['name'] = $alias;
            $template['aliases'] = [];

            $this->templates[$template['name']] = $template;
        }
    }

    /**
     * Find out template name from user input.
     *
     * @return string
     */
    protected function getCurrentTemplateName()
    {
        $templateName = $this->input->getOption('template');

        if (!$templateName) {
            return 'default';
        }

        if (!array_key_exists($templateName, $this->templates)) {
            $this->abort("Template {$templateName} was not found");
        }

        return $templateName;
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Replace all placeholders in the stub.
     *
     * @param string $stub
     * @param string $className
     *
     * @return string
     */
    protected function replacePlaceholdersInStub($stub, $className)
    {
        return str_replace('ClassPlaceholder', $className, $stub);
    }

    /**
     * Construct migration file name from user input and current time.
     *
     * @param $input
     *
     * @return string
     */
    protected function constructFileName($input)
    {
        return $this->getDatePrefix().'_'.$input;
    }

    /**
     * Create migration file and put $content inside.
     *
     * @param string $fileName
     * @param string $content
     *
     * @return void
     */
    protected function createMigrationFile($fileName, $content)
    {
        $this->files->putContent($this->dir.'/'.$fileName.'.php', $content);
    }
}
