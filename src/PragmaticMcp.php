<?php

namespace pragmatic\mcp;

use Craft;
use craft\base\Model;
use craft\base\Plugin;

/**
 * @property services\ResourceService $resourceService
 * @property services\ToolService $toolService
 * @property services\QueryService $queryService
 */
class PragmaticMcp extends Plugin
{
    public static PragmaticMcp $plugin;

    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = false;

    public static function config(): array
    {
        return [
            'components' => [
                'resourceService' => ['class' => services\ResourceService::class],
                'toolService' => ['class' => services\ToolService::class],
                'queryService' => ['class' => services\QueryService::class],
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        if (Craft::$app->request->isConsoleRequest) {
            $this->controllerNamespace = 'pragmatic\\mcp\\console\\controllers';
        }

        Craft::info('Pragmatic MCP plugin loaded', __METHOD__);
    }

    protected function createSettingsModel(): ?Model
    {
        return new models\Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(
            'pragmatic-mcp/settings',
            ['settings' => $this->getSettings()]
        );
    }
}
