<?php

namespace pragmatic\mcp\controllers;

use craft\helpers\UrlHelper;
use craft\web\Controller;
use pragmatic\mcp\PragmaticMcp;
use yii\web\Response;

class DefaultController extends Controller
{
    public function actionIndex(): Response
    {
        return $this->redirectToSettings(null);
    }

    public function actionOptions(): Response
    {
        return $this->redirectToSettings('options');
    }

    public function actionSections(): Response
    {
        return $this->redirectToSettings('sections');
    }

    private function redirectToSettings(?string $tab): Response
    {
        $url = UrlHelper::cpUrl('settings/plugins/' . PragmaticMcp::$plugin->id);

        if ($tab === 'options') {
            $url .= '#mcp-pane-options';
        } elseif ($tab === 'sections') {
            $url .= '#mcp-pane-sections';
        }

        return $this->redirect($url);
    }
}
