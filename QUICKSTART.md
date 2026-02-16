# Guía de Configuración Rápida

## ⚡ Setup en 5 minutos

### 1. Instalar Plugin en Craft

```bash
# Copia el plugin a tu directorio de Craft
cp -r pragmatic-mcp /var/www/html/plugins/

# O via Composer (cuando esté disponible)
composer require pragmatic/mcp-craftcms-plugin
```

### 2. Activar en Craft CMS

1. Panel de Control → Configuración → Plugins
2. Click en "Install" junto a "Pragmatic MCP"
3. Ve a Configuración → Pragmatic MCP
4. Habilita los recursos que necesites (Entries, Assets, etc.)

### 3. Instalar Dependencias Node.js

```bash
cd /var/www/html/plugins/pragmatic-mcp/mcp-server
npm install
```

### 4. Configurar SSH (si no lo tienes)

```bash
# En tu Mac/PC
ssh-keygen -t rsa -b 4096
ssh-copy-id usuario@tu-servidor.com

# Prueba
ssh usuario@tu-servidor.com "php craft mcp/info"
```

### 5. Configurar Claude Desktop

**Mac**: `~/Library/Application Support/Claude/claude_desktop_config.json`

```json
{
  "mcpServers": {
    "craft-cms": {
      "command": "ssh",
      "args": [
        "usuario@tu-servidor.com",
        "CRAFT_PATH=/var/www/html node /var/www/html/plugins/pragmatic-mcp/mcp-server/index.js"
      ]
    }
  }
}
```

### 6. Reiniciar Claude Desktop

Cierra completamente Claude Desktop y vuelve a abrirlo.

### 7. ¡Probar!

Pregunta a Claude:
> "¿Qué secciones tiene mi sitio Craft CMS?"

---

## 🔧 Configuración Recomendada

### Para Blog/Sitio de Contenido

```
✓ Habilitar Entries
✓ Habilitar Assets
✓ Habilitar Categorías
✗ Usuarios (no necesario)

Secciones: blog, pages
Campos expuestos: featuredImage, excerpt, bodyContent
Máx resultados: 50
Cache: Activado (3600s)
```

### Para E-commerce

```
✓ Habilitar Entries (productos)
✓ Habilitar Assets (imágenes productos)
✓ Habilitar Categorías
✗ Usuarios

Secciones: products, categories
Campos expuestos: price, sku, stock, productImages
Máx resultados: 100
Cache: Activado (1800s)
```

### Para Sitio Corporativo

```
✓ Habilitar Entries
✓ Habilitar Assets
✗ Categorías
✗ Usuarios

Secciones: services, team, news
Campos expuestos: role, bio, serviceDescription
Máx resultados: 30
Cache: Activado (7200s)
```

---

## ✅ Checklist de Verificación

- [ ] Plugin instalado y activado en Craft
- [ ] Dependencias Node.js instaladas (`npm install`)
- [ ] SSH configurado sin password
- [ ] Archivo `claude_desktop_config.json` editado
- [ ] Claude Desktop reiniciado
- [ ] Comando `php craft mcp/info` funciona en SSH
- [ ] Claude responde preguntas sobre tu sitio

---

## 🐛 Problemas Comunes

### "Connection refused"
→ Verifica que SSH funcione: `ssh usuario@servidor "echo OK"`

### "Command not found: php"
→ Especifica la ruta completa: `PHP_PATH=/usr/bin/php8.2`

### "No resources found"
→ Activa recursos en la configuración del plugin en Craft

### Claude no responde sobre mi sitio
→ Reinicia Claude Desktop completamente (Quit, no solo cerrar ventana)

---

## 📞 Soporte

- GitHub Issues: https://github.com/pragmaticcat/mcp-craftcms-plugin
- Email: oriolnoya@pragmatic.cat
- Docs: [URL]

---

¡Listo! Ahora Claude puede ayudarte con tu contenido de Craft CMS 🎉
