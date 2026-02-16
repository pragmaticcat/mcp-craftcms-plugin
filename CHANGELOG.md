# Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/lang/es/).

## [1.0.0] - 2024-02-14

### Añadido
- Lanzamiento inicial del plugin
- Soporte para acceso a Entries, Assets, Categorías y Usuarios
- Sistema de recursos (Resources) para listar contenidos
- Tools para búsqueda y consulta de detalles
- Configuración visual desde el panel de Craft
- Sistema de cache integrado
- Filtrado por secciones permitidas
- Control de campos personalizados expuestos
- Comandos de consola para gestión
- Servidor MCP en Node.js para conexión con Claude
- Documentación completa
- Límites de seguridad configurables
- Soporte para conexión SSH

### Características de Seguridad
- Control granular de permisos por sección
- Lista de IPs permitidas (opcional)
- Token de acceso (opcional)
- Límites de resultados por query
- Cache para optimizar rendimiento

## Roadmap

### [1.1.0] - Próxima versión
- [ ] Soporte para consultas GraphQL personalizadas
- [ ] Webhooks para notificar cambios en contenido
- [ ] Logs de auditoría para rastrear accesos
- [ ] Integración con permisos nativos de Craft
- [ ] Soporte para Matrix fields mejorado
- [ ] API REST opcional (además de MCP)
- [ ] Dashboard con estadísticas de uso

### [1.2.0] - Futuro
- [ ] Soporte para Commerce (productos, órdenes)
- [ ] Capacidad de escritura (crear/editar entries)
- [ ] Sistema de roles y permisos avanzado
- [ ] Versionado de contenido
- [ ] Búsqueda semántica con embeddings
- [ ] Rate limiting configurable

## Notas de Seguridad

**IMPORTANTE**: Este plugin permite acceso a contenidos de tu sitio. Asegúrate de:
- Revisar qué secciones expones
- Limitar campos sensibles
- Usar conexiones SSH seguras
- Monitorear logs regularmente
