<?php

namespace pragmatic\mcp\controllers;

use Craft;
use craft\web\Controller;
use pragmatic\mcp\PragmaticMcp;
use yii\web\Response;

class DefaultController extends Controller
{
    public function actionIndex(): Response
    {
        return $this->redirect('pragmatic-mcp/sections');
    }

    public function actionSections(): Response
    {
        return $this->renderTemplate('pragmatic-mcp/sections', [
            'settings' => PragmaticMcp::$plugin->getSettings(),
        ]);
    }

    public function actionOptions(): Response
    {
        return $this->renderTemplate('pragmatic-mcp/options', [
            'settings' => PragmaticMcp::$plugin->getSettings(),
        ]);
    }

    public function actionSaveSettings(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $fields = (array)$request->getBodyParam('_fields', []);
        $settings = $this->normalizeSettings($fields);

        if (Craft::$app->plugins->savePluginSettings(PragmaticMcp::$plugin, $settings)) {
            Craft::$app->getSession()->setNotice(Craft::t('app', 'Settings saved.'));
            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save settings.'));
        return $this->redirectToPostedUrl();
    }

    private function normalizeSettings(array $fields): array
    {
        $request = Craft::$app->getRequest();
        $current = PragmaticMcp::$plugin->getSettings()->toArray();
        $knownFields = [
            'enableEntries',
            'enableAssets',
            'enableCategories',
            'enableUsers',
            'allowedSections',
            'enableSearchTool',
            'enableDetailsTool',
            'enableCustomQueries',
            'maxResults',
            'maxQueryComplexity',
            'exposedFields',
            'enableCache',
            'cacheDuration',
            'accessToken',
            'allowedIpAddresses',
        ];
        $targetFields = array_intersect($knownFields, $fields);

        foreach ($targetFields as $field) {
            switch ($field) {
                case 'enableEntries':
                case 'enableAssets':
                case 'enableCategories':
                case 'enableUsers':
                case 'enableSearchTool':
                case 'enableDetailsTool':
                case 'enableCustomQueries':
                case 'enableCache':
                    $current[$field] = (bool)$request->getBodyParam($field);
                    break;

                case 'maxResults':
                case 'maxQueryComplexity':
                case 'cacheDuration':
                    $current[$field] = (int)$request->getBodyParam($field, $current[$field] ?? 0);
                    break;

                case 'allowedSections':
                    $current[$field] = array_values(array_filter(
                        array_map('strval', (array)$request->getBodyParam($field, [])),
                        static fn(string $v) => $v !== ''
                    ));
                    break;

                case 'exposedFields':
                    $current[$field] = $this->normalizeExposedFields($request->getBodyParam($field, []));
                    break;

                case 'allowedIpAddresses':
                    $raw = (string)$request->getBodyParam($field, '');
                    $lines = preg_split('/\R+/', $raw) ?: [];
                    $current[$field] = array_values(array_filter(
                        array_map(static fn(string $v) => trim($v), $lines),
                        static fn(string $v) => $v !== ''
                    ));
                    break;

                case 'accessToken':
                    $current[$field] = trim((string)$request->getBodyParam($field, ''));
                    break;
            }
        }

        return $current;
    }

    private function normalizeExposedFields(mixed $raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $raw = $decoded;
            }
        }

        if (!is_array($raw)) {
            return [];
        }

        $fields = [];
        foreach ($raw as $row) {
            if (is_array($row) && isset($row['field'])) {
                $field = trim((string)$row['field']);
                if ($field !== '') {
                    $fields[] = $field;
                }
            } elseif (is_string($row)) {
                $field = trim($row);
                if ($field !== '') {
                    $fields[] = $field;
                }
            }
        }

        return array_values(array_unique($fields));
    }
}
