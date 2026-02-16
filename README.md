# Pragmatic MCP Plugin

Plugin para Craft CMS que permite acceder a los contenidos del sitio mediante el protocolo MCP (Model Context Protocol), habilitando que Claude pueda consultar y trabajar con tu contenido.

## 🚀 Características

- ✅ Acceso a Entries, Assets, Categorías y Usuarios
- ✅ Búsqueda de contenidos
- ✅ Filtrado por secciones
- ✅ Control granular de permisos
- ✅ Cache integrado
- ✅ Tools personalizables
- ✅ Interfaz de configuración visual

## 📋 Requisitos

- Craft CMS 5.x
- PHP 8.2 o superior
- Node.js 18+ (para el servidor MCP)
- Acceso SSH al servidor (para uso con Claude Desktop)

## 📦 Instalación

### 1. Instalar el Plugin

**Opción A: Via Composer (recomendado cuando esté publicado)**
```bash
composer require pragmatic/mcp-craftcms-plugin
```

**Opción B: Instalación Manual**
1. Descarga el plugin
2. Extrae el contenido en `craft/plugins/pragmatic-mcp/`
3. En el Panel de Control de Craft, ve a Configuración → Plugins
4. Instala "Pragmatic MCP"

### 2. Configurar el Plugin

1. Ve a Configuración → Plugins → Pragmatic MCP
2. Configura:
   - Recursos a exponer (Entries, Assets, etc.)
   - Secciones permitidas
   - Tools habilitados
   - Campos personalizados a incluir
   - Límites de seguridad

### 3. Instalar Dependencias de Node.js

En el servidor donde está Craft CMS:

```bash
cd /ruta/a/craft/plugins/pragmatic-mcp/mcp-server
npm install
```

### 4. Probar la Instalación

```bash
# Mostrar información del plugin
php craft mcp/info

# Listar recursos disponibles
php craft mcp/list-resources

# Listar tools disponibles
php craft mcp/list-tools

# Probar búsqueda
php craft mcp/execute-tool search_entries '{"query":"test"}'
```

## 🔧 Configuración de Claude Desktop

### Opción 1: Conexión SSH (Recomendado)

Edita el archivo de configuración de Claude Desktop:
- **Mac**: `~/Library/Application Support/Claude/claude_desktop_config.json`
- **Windows**: `%APPDATA%\Claude\claude_desktop_config.json`

```json
{
  "mcpServers": {
    "craft-cms": {
      "command": "ssh",
      "args": [
        "-i",
        "/ruta/a/tu/.ssh/id_rsa",
        "usuario@tu-servidor.com",
        "CRAFT_PATH=/var/www/html node /var/www/html/plugins/pragmatic-mcp/mcp-server/index.js"
      ]
    }
  }
}
```

### Opción 2: Servidor Local

Si Craft está en tu máquina local:

```json
{
  "mcpServers": {
    "craft-cms": {
      "command": "node",
      "args": [
        "/ruta/a/craft/plugins/pragmatic-mcp/mcp-server/index.js"
      ],
      "env": {
        "CRAFT_PATH": "/var/www/html",
        "PHP_PATH": "php"
      }
    }
  }
}
```

### Configurar SSH sin Password

```bash
# Generar clave SSH si no tienes
ssh-keygen -t rsa -b 4096

# Copiar al servidor
ssh-copy-id usuario@tu-servidor.com

# Probar conexión
ssh usuario@tu-servidor.com "echo 'Conexión OK'"
```

## 💬 Uso con Claude

Una vez configurado, reinicia Claude Desktop y podrás hacer preguntas como:

```
"¿Qué secciones tiene mi sitio Craft?"
"Busca entradas sobre 'recetas'"
"Dame detalles de la entrada con ID 123"
"¿Cuántas entradas hay en la sección 'blog'?"
"Muéstrame los últimos posts publicados"
"¿Qué assets de tipo imagen tengo?"
```

## 🛠️ Comandos Disponibles

### Comandos de Consola

```bash
# Información general
php craft mcp/info

# Listar recursos
php craft mcp/list-resources

# Leer un recurso
php craft mcp/read-resource "craft://entries/blog"

# Listar tools
php craft mcp/list-tools

# Ejecutar un tool
php craft mcp/execute-tool search_entries '{"query":"test","limit":5}'

# Limpiar cache
php craft mcp/clear-cache
```

## 🔒 Seguridad

### Mejores Prácticas

1. **Limita secciones**: Solo expone las secciones necesarias
2. **Revisa campos**: Solo incluye campos que sean seguros de compartir
3. **Usa cache**: Reduce carga del servidor
4. **Monitorea logs**: Revisa el uso del plugin regularmente
5. **SSH seguro**: Usa claves SSH en lugar de passwords

### Consideraciones

- Los usuarios NO tienen información sensible expuesta por defecto
- Las IPs permitidas son opcionales pero recomendadas
- El token de acceso añade una capa extra de seguridad
- Los datos sensibles NO deben incluirse en campos expuestos

## 🎨 Personalización

### Agregar Campos Personalizados

En la configuración del plugin, agrega los handles de campos:

```
myCustomField
featuredImage
richTextContent
relatedEntries
```

### Límites y Performance

- `maxResults`: Controla cuántos resultados máximos retorna una query
- `cacheDuration`: Tiempo en segundos que los datos permanecen en cache
- `enableCache`: Activa/desactiva el sistema de cache

## 🐛 Troubleshooting

### El servidor MCP no inicia

```bash
# Verifica que Node.js esté instalado
node --version

# Verifica las dependencias
cd mcp-server && npm install

# Prueba ejecutar manualmente
CRAFT_PATH=/var/www/html node index.js
```

### Claude no puede conectarse

```bash
# Verifica la conexión SSH
ssh usuario@tu-servidor.com "php craft mcp/info"

# Revisa los logs de Claude Desktop
# Mac: ~/Library/Logs/Claude/
# Windows: %APPDATA%\Claude\logs\
```

### No aparecen datos

1. Verifica que el plugin esté habilitado
2. Revisa la configuración de recursos y secciones
3. Limpia el cache: `php craft mcp/clear-cache`
4. Verifica permisos de PHP en los directorios

## 📝 Ejemplos de Uso

### Búsqueda Básica

```bash
php craft mcp/execute-tool search_entries '{
  "query": "tutorial",
  "limit": 10
}'
```

### Búsqueda por Sección

```bash
php craft mcp/execute-tool search_entries '{
  "query": "marketing",
  "section": "blog",
  "limit": 5
}'
```

### Obtener Detalles

```bash
php craft mcp/execute-tool get_entry_details '{
  "entryId": 123,
  "includeRelated": true
}'
```

## 🤝 Contribuir

Si encuentras bugs o tienes sugerencias:
1. Abre un issue en GitHub
2. Envía un Pull Request
3. Contacta al autor

## 📄 Licencia

MIT License - ver archivo LICENSE

## 👨‍💻 Autor

Oriol Noya - [pragmatic.cat](https://pragmatic.cat)

## 🙏 Agradecimientos

- Craft CMS por el excelente CMS
- Anthropic por Claude y el protocolo MCP
- La comunidad open source

---

**¿Necesitas ayuda?** Abre un issue en GitHub o consulta la documentación de Craft CMS.
