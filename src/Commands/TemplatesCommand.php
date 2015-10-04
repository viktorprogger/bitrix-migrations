<?php

namespace Arrilot\BitrixMigrations\Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class TemplatesCommand extends AbstractMigrationCommand
{
    /**
     * Table in DB to store migrations that have been already run.
     *
     * @var MakeCommand
     */
    protected $makeCommand;

    /**
     * Constructor.
     *
     * @param MakeCommand $makeCommand
     */
    public function __construct(MakeCommand $makeCommand)
    {
        $this->makeCommand = $makeCommand;

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('templates')->setDescription('Show the list of available migration templates');
    }

    /**
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
    {
        $table = new Table($this->output);
        $table->setHeaders(['Name', 'Path', 'Description'])->setRows($this->collectRows());
        $table->setStyle('borderless');
        $table->render();
    }

    /**
     * Collect and return templates from "MakeCommand".
     *
     * @return array
     */
    protected function collectRows()
    {
        $rows = collect($this->makeCommand->getTemplates())
            ->filter(function ($template) {
                return $template['is_alias'] == false;
            })
            ->sortBy('name')
            ->map(function ($template) {
                $row = [];

                $names = array_merge([$template['name']], $template['aliases']);
                $row[] = implode("\n/ ", $names);
                $row[] = wordwrap($template['path'], 65, "\n", true);
                $row[] = wordwrap($template['description'], 25, "\n", true);

                return $row;
            });

        return $this->separateRows($rows);
    }

    /**
     * Separate rows with a separator.
     *
     * @param $templates
     *
     * @return array
     */
    protected function separateRows($templates)
    {
        $rows = [];
        foreach ($templates as $template) {
            $rows[] = $template;
            $rows[] = new TableSeparator();
        }
        unset($rows[count($rows) - 1]);

        return $rows;
    }
}
