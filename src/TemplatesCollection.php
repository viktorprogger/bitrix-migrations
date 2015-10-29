<?php

namespace Arrilot\BitrixMigrations;

use InvalidArgumentException;
use RuntimeException;

class TemplatesCollection
{
    /**
     * Configuration array.
     *
     * @var array
     */
    protected $config;

    /**
     * Array of available migration file templates.
     *
     * @var array
     */
    protected $templates = [];

    /**
     * Constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->registerTemplate([
            'name' => 'default',
            'path' => $this->config['composerPath'].'/vendor/arrilot/bitrix-migrations/templates/default.template',
            'description' => 'Default migration template',
        ]);
    }

    /**
     * Register basic templates.
     */
    public function registerBasicTemplates()
    {
        $templates = [
            [
                'name' => 'add_iblock',
                'path' => $this->config['composerPath'].'/vendor/arrilot/bitrix-migrations/templates/add_iblock.template',
                'description' => 'Add iblock',
            ],
            [
                'name' => 'add_iblock',
                'path' => $this->config['composerPath'].'/vendor/arrilot/bitrix-migrations/templates/add_iblock.template',
                'description' => 'Add iblock',
            ],
            [
                'name' => 'add_iblock_element_property',
                'path' => $this->config['composerPath'].'/vendor/arrilot/bitrix-migrations/templates/add_iblock_element_property.template',
                'description' => 'Add iblock element property',
                'aliases' => [
                    'add_iblock_prop',
                    'add_iblock_element_prop',
                    'add_element_prop',
                    'add_element_property',
                ],
            ],
            [
                'name' => 'add_table',
                'path' => $this->config['composerPath'].'/vendor/arrilot/bitrix-migrations/templates/add_table.template',
                'description' => 'Create table',
                'aliases' => [
                    'create_table',
                ],
            ],
            [
                'name' => 'delete_table',
                'path' => $this->config['composerPath'].'/vendor/arrilot/bitrix-migrations/templates/delete_table.template',
                'description' => 'Drop table',
                'aliases' => [
                    'drop_table',
                ],
            ],
            [
                'name' => 'query',
                'path' => $this->config['composerPath'].'/vendor/arrilot/bitrix-migrations/templates/query.template',
                'description' => 'Simple database query',
            ],
        ];

        foreach ($templates as $template) {
            $this->registerTemplate($template);
        }
    }

    /**
     * Register templates for automigrations.
     */
    public function registerAutoTemplates()
    {
        $templates = [
            'add_iblock',
            'update_iblock',
            'delete_iblock',
            'add_iblock_element_property',
            'update_iblock_element_property',
            'delete_iblock_element_property',
            'add_uf',
            'update_uf',
            'delete_uf',
            'add_hlblock',
            'update_hlblock',
            'delete_hlblock',
            'add_group',
            'update_group',
            'delete_group',
        ];

        foreach ($templates as $template) {
            $this->registerTemplate([
                'name' => 'auto_'.$template,
                'path' => $this->config['composerPath'].'/vendor/arrilot/bitrix-migrations/templates/auto/'.$template.'.template',
            ]);
        }
    }

    /**
     * Getter for registered templates.
     *
     * @return array
     */
    public function all()
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
        $template = $this->normalizeTemplateDuringRegistration($template);

        $this->templates[$template['name']] = $template;

        $this->registerTemplateAliases($template, $template['aliases']);
    }

    /**
     * Path to the file where a template is located.
     *
     * @param string $name
     *
     * @return string
     */
    public function getTemplatePath($name)
    {
        return $this->templates[$name]['path'];
    }

    /**
     * Find out template name from user input.
     *
     * @param $template
     *
     * @return string
     */
    public function selectTemplate($template)
    {
        if (!$template) {
            return 'default';
        }

        if (!array_key_exists($template, $this->templates)) {
            throw new RuntimeException("Template \"{$template}\" is not registered");
        }

        return $template;
    }

    /**
     * Check template fields and normalize them.
     *
     * @param $template
     *
     * @return array
     */
    protected function normalizeTemplateDuringRegistration($template)
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
}
