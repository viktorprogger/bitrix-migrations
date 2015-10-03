<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface;
use Arrilot\BitrixMigrations\Repositories\FileRepository;
use Illuminate\Support\Str;
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
     * Constructor.
     *
     * @param array $config
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

        $stubData = $this->files->getContent($this->stubPath());
        $stubData = $this->replaceDummiesInStub($stubData, $className);

        $this->createMigrationFile($fileName, $stubData);

        $this->message("<info>Migration created:</info> {$fileName}.php" );
    }

    /**
     * Path to the file where a stub is located.
     *
     * @return string
     */
    protected function stubPath()
    {
        return __DIR__.'/stubs/migration.' . $this->findOutStubTemplate() . '.stub';
    }

    /**
     * Find out stub template depending on user input.
     *
     * @return string
     */
    protected function findOutStubTemplate()
    {
        $possibleTemplates = [
            //TBD
        ];

        $template = $this->input->getOption('template');

        return in_array($template, $possibleTemplates) ? $template : 'plain';
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
     * Replace all dummies in the stub.
     *
     * @param string $stub
     * @param string $className
     * @return string
     */
    protected function replaceDummiesInStub($stub, $className)
    {
        return str_replace('DummyClassName', $className, $stub);
    }

    /**
     * Construct migration file name from user input and current time.
     *
     * @param $input
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
     * @return void
     */
    protected function createMigrationFile($fileName, $content)
    {
        $this->files->putContent($this->dir . '/' . $fileName . '.php', $content);
    }
}
