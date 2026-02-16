<?php
namespace pragmatic\mcp\console\controllers;

use Craft;
use craft\console\Controller;
use pragmatic\mcp\PragmaticMcp;
use yii\console\ExitCode;

/**
 * MCP Controller
 * 
 * Comandos de consola para el servidor MCP
 */
class McpController extends Controller
{
    /**
     * Lista todos los recursos disponibles
     */
    public function actionListResources(): int
    {
        try {
            $resources = PragmaticMcp::getInstance()->resourceService->getAvailableResources();
            echo json_encode($resources, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Error: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Lee un recurso específico
     * 
     * @param string $uri URI del recurso (ej: craft://entries/blog)
     */
    public function actionReadResource(string $uri): int
    {
        try {
            $data = PragmaticMcp::getInstance()->resourceService->readResource($uri);
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Error: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Lista todos los tools disponibles
     */
    public function actionListTools(): int
    {
        try {
            $tools = PragmaticMcp::getInstance()->toolService->getAvailableTools();
            echo json_encode($tools, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Error: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Ejecuta un tool
     * 
     * @param string $name Nombre del tool
     * @param string $arguments JSON con los argumentos del tool
     */
    public function actionExecuteTool(string $name, string $arguments = '{}'): int
    {
        try {
            $args = json_decode($arguments, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Argumentos JSON inválidos: ' . json_last_error_msg());
            }

            $result = PragmaticMcp::getInstance()->toolService->executeTool($name, $args);
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Error: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Muestra información de configuración
     */
    public function actionInfo(): int
    {
        $settings = PragmaticMcp::getInstance()->getSettings();
        
        $this->stdout("=== Craft MCP Plugin - Configuración ===\n\n");
        
        $this->stdout("Recursos habilitados:\n");
        $this->stdout("  - Entries: " . ($settings->enableEntries ? 'Sí' : 'No') . "\n");
        $this->stdout("  - Assets: " . ($settings->enableAssets ? 'Sí' : 'No') . "\n");
        $this->stdout("  - Categorías: " . ($settings->enableCategories ? 'Sí' : 'No') . "\n");
        $this->stdout("  - Usuarios: " . ($settings->enableUsers ? 'Sí' : 'No') . "\n\n");
        
        $this->stdout("Tools habilitados:\n");
        $this->stdout("  - Búsqueda: " . ($settings->enableSearchTool ? 'Sí' : 'No') . "\n");
        $this->stdout("  - Detalles: " . ($settings->enableDetailsTool ? 'Sí' : 'No') . "\n");
        $this->stdout("  - Consultas personalizadas: " . ($settings->enableCustomQueries ? 'Sí' : 'No') . "\n\n");
        
        $this->stdout("Límites:\n");
        $this->stdout("  - Máx. resultados: {$settings->maxResults}\n");
        $this->stdout("  - Cache: " . ($settings->enableCache ? 'Sí (' . $settings->cacheDuration . 's)' : 'No') . "\n\n");
        
        if (!empty($settings->allowedSections)) {
            $this->stdout("Secciones permitidas: " . implode(', ', $settings->allowedSections) . "\n");
        } else {
            $this->stdout("Secciones permitidas: Todas\n");
        }
        
        return ExitCode::OK;
    }

    /**
     * Limpia el cache del MCP
     */
    public function actionClearCache(): int
    {
        try {
            Craft::$app->cache->flush();
            $this->stdout("Cache limpiado correctamente.\n");
            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("Error al limpiar cache: " . $e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
