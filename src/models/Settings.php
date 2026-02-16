<?php
namespace pragmatic\mcp\models;

use craft\base\Model;

class Settings extends Model
{
    // Recursos disponibles
    public bool $enableEntries = true;
    public bool $enableAssets = true;
    public bool $enableCategories = true;
    public bool $enableUsers = false;
    
    // Secciones permitidas (vacío = todas)
    public array $allowedSections = [];
    
    // Tools disponibles
    public bool $enableSearchTool = true;
    public bool $enableDetailsTool = true;
    public bool $enableCustomQueries = false;
    
    // Límites de seguridad
    public int $maxResults = 100;
    public int $maxQueryComplexity = 5;
    
    // Custom queries (solo si enableCustomQueries = true)
    public array $customQueries = [];
    
    // Cache
    public bool $enableCache = true;
    public int $cacheDuration = 3600; // 1 hora
    
    // Seguridad
    public string $accessToken = '';
    public array $allowedIpAddresses = [];
    
    // Campos personalizados a exponer
    public array $exposedFields = [];

    public function rules(): array
    {
        return [
            [['maxResults', 'maxQueryComplexity', 'cacheDuration'], 'integer'],
            [['maxResults'], 'integer', 'min' => 1, 'max' => 1000],
            [
                [
                    'enableEntries',
                    'enableAssets',
                    'enableCategories',
                    'enableUsers',
                    'enableSearchTool',
                    'enableDetailsTool',
                    'enableCustomQueries',
                    'enableCache'
                ],
                'boolean'
            ],
            [['allowedSections', 'customQueries', 'exposedFields', 'allowedIpAddresses'], 'safe'],
            [['accessToken'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'enableEntries' => 'Habilitar Entries',
            'enableAssets' => 'Habilitar Assets',
            'enableCategories' => 'Habilitar Categorías',
            'enableUsers' => 'Habilitar Usuarios',
            'allowedSections' => 'Secciones Permitidas',
            'enableSearchTool' => 'Tool: Búsqueda',
            'enableDetailsTool' => 'Tool: Detalles de Entrada',
            'enableCustomQueries' => 'Tool: Consultas Personalizadas',
            'maxResults' => 'Máximo de Resultados',
            'maxQueryComplexity' => 'Complejidad Máxima de Query',
            'customQueries' => 'Consultas Personalizadas',
            'enableCache' => 'Habilitar Cache',
            'cacheDuration' => 'Duración del Cache (segundos)',
            'accessToken' => 'Token de Acceso',
            'allowedIpAddresses' => 'IPs Permitidas',
            'exposedFields' => 'Campos Expuestos',
        ];
    }
}
