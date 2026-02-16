<?php

namespace pragmatic\mcp;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;
use craft\web\twig\variables\Cp;
use craft\events\RegisterCpNavItemsEvent;

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
    public bool $hasCpSection = true;

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

        Craft::$app->i18n->translations['pragmatic-mcp'] = [
            'class' => \yii\i18n\PhpMessageSource::class,
            'basePath' => __DIR__ . '/translations',
            'forceTranslation' => true,
        ];

        if (Craft::$app->request->isConsoleRequest) {
            $this->controllerNamespace = 'pragmatic\\mcp\\console\\controllers';
            Craft::$app->controllerMap['mcp'] = console\controllers\McpController::class;
        }

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['pragmatic-mcp'] = 'pragmatic-mcp/default/index';
                $event->rules['pragmatic-mcp/options'] = 'pragmatic-mcp/default/options';
                $event->rules['pragmatic-mcp/sections'] = 'pragmatic-mcp/default/sections';
                $event->rules['pragmatic-mcp/save-settings'] = 'pragmatic-mcp/default/save-settings';
            }
        );

        // Register nav item under shared "Tools" group
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) {
                $toolsLabel = Craft::t('pragmatic-mcp', 'Tools');
                $groupKey = null;
                foreach ($event->navItems as $key => $item) {
                    if (($item['label'] ?? '') === $toolsLabel && isset($item['subnav'])) {
                        $groupKey = $key;
                        break;
                    }
                }

                if ($groupKey === null) {
                    $newItem = [
                        'label' => $toolsLabel,
                        'url' => 'pragmatic-mcp',
                        'icon' => __DIR__ . '/icons/icon.svg',
                        'subnav' => [],
                    ];

                    // Insert after the first matching nav item
                    $afterKey = null;
                    $insertAfter = ['users', 'assets', 'categories', 'entries'];
                    foreach ($insertAfter as $target) {
                        foreach ($event->navItems as $key => $item) {
                            if (($item['url'] ?? '') === $target) {
                                $afterKey = $key;
                                break 2;
                            }
                        }
                    }

                    if ($afterKey !== null) {
                        $pos = array_search($afterKey, array_keys($event->navItems)) + 1;
                        $event->navItems = array_merge(
                            array_slice($event->navItems, 0, $pos, true),
                            ['pragmatic' => $newItem],
                            array_slice($event->navItems, $pos, null, true),
                        );
                        $groupKey = 'pragmatic';
                    } else {
                        $event->navItems['pragmatic'] = $newItem;
                        $groupKey = 'pragmatic';
                    }
                }

                $event->navItems[$groupKey]['subnav']['mcp'] = [
                    'label' => 'MCP',
                    'url' => 'pragmatic-mcp',
                ];

                $path = Craft::$app->getRequest()->getPathInfo();
                if ($path === 'pragmatic-mcp' || str_starts_with($path, 'pragmatic-mcp/')) {
                    $event->navItems[$groupKey]['url'] = 'pragmatic-mcp';
                }
            }
        );
    }

    protected function createSettingsModel(): ?Model
    {
        return new models\Settings();
    }

    public function getCpNavItem(): ?array
    {
        return null;
    }
}
