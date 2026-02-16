#!/usr/bin/env node

/**
 * Craft CMS MCP Server
 * 
 * Este servidor actúa como puente entre Claude y Craft CMS,
 * ejecutando comandos de consola de Craft para obtener datos.
 */

import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
  ListResourcesRequestSchema,
  ReadResourceRequestSchema,
  ListToolsRequestSchema,
  CallToolRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";
import { execSync } from "child_process";

// Configuración
const CRAFT_PATH = process.env.CRAFT_PATH || "/var/www/html";
const PHP_PATH = process.env.PHP_PATH || "php";

function parseCraftJsonOutput(output) {
  const text = (output ?? "").trim();
  if (!text) {
    throw new Error("La salida de Craft está vacía");
  }

  try {
    return JSON.parse(text);
  } catch (_) {
    // Continue with block extraction fallback.
  }

  const starts = [];
  for (let i = 0; i < text.length; i++) {
    const ch = text[i];
    if (ch === "{" || ch === "[") {
      starts.push(i);
    }
  }

  for (let s = starts.length - 1; s >= 0; s--) {
    const start = starts[s];
    const stack = [];
    let inString = false;
    let escape = false;

    for (let i = start; i < text.length; i++) {
      const ch = text[i];

      if (inString) {
        if (escape) {
          escape = false;
          continue;
        }
        if (ch === "\\") {
          escape = true;
          continue;
        }
        if (ch === "\"") {
          inString = false;
        }
        continue;
      }

      if (ch === "\"") {
        inString = true;
        continue;
      }

      if (ch === "{") {
        stack.push("}");
        continue;
      }
      if (ch === "[") {
        stack.push("]");
        continue;
      }
      if (ch === "}" || ch === "]") {
        if (stack.length === 0 || stack[stack.length - 1] !== ch) {
          break;
        }
        stack.pop();
        if (stack.length === 0) {
          const candidate = text.slice(start, i + 1);
          try {
            return JSON.parse(candidate);
          } catch (_) {
            break;
          }
        }
      }
    }
  }

  throw new Error("No se pudo parsear un bloque JSON válido desde la salida de Craft");
}

/**
 * Ejecuta un comando de Craft CMS y retorna el resultado JSON
 */
function craftCommand(command, args = []) {
  try {
    // Escapar argumentos para shell
    const escapedArgs = args.map(arg => {
      if (typeof arg === 'string') {
        return `'${arg.replace(/'/g, "'\\''")}'`;
      }
      return `'${JSON.stringify(arg).replace(/'/g, "'\\''")}'`;
    });
    
    const argsStr = escapedArgs.join(" ");
    const cmd = `cd ${CRAFT_PATH} && ${PHP_PATH} craft mcp/${command} ${argsStr}`;
    
    console.error(`Ejecutando: ${cmd}`);
    
    const result = execSync(cmd, { 
      encoding: "utf-8",
      maxBuffer: 10 * 1024 * 1024, // 10MB buffer
      stdio: ['pipe', 'pipe', 'pipe']
    });
    
    return parseCraftJsonOutput(result);
  } catch (error) {
    console.error("Error ejecutando comando Craft:", error.message);
    if (error.stderr) {
      console.error("STDERR:", error.stderr.toString());
    }
    if (error.stdout) {
      console.error("STDOUT:", error.stdout.toString());
    }
    throw new Error(`Error en comando Craft: ${error.message}`);
  }
}

// Crear servidor MCP
const server = new Server(
  {
    name: "pragmatic-mcp-plugin",
    version: "1.0.0",
  },
  {
    capabilities: {
      resources: {},
      tools: {},
    },
  }
);

/**
 * Listar recursos disponibles
 */
server.setRequestHandler(ListResourcesRequestSchema, async () => {
  console.error("Listando recursos...");
  
  try {
    const resources = craftCommand("list-resources");
    console.error(`Recursos encontrados: ${resources.length}`);
    return { resources };
  } catch (error) {
    console.error("Error listando recursos:", error);
    return { resources: [] };
  }
});

/**
 * Leer un recurso específico
 */
server.setRequestHandler(ReadResourceRequestSchema, async (request) => {
  const uri = request.params.uri;
  console.error(`Leyendo recurso: ${uri}`);
  
  try {
    const data = craftCommand("read-resource", [uri]);
    
    return {
      contents: [
        {
          uri: uri,
          mimeType: "application/json",
          text: JSON.stringify(data, null, 2),
        },
      ],
    };
  } catch (error) {
    console.error(`Error leyendo recurso ${uri}:`, error);
    throw error;
  }
});

/**
 * Listar herramientas disponibles
 */
server.setRequestHandler(ListToolsRequestSchema, async () => {
  console.error("Listando tools...");
  
  try {
    const tools = craftCommand("list-tools");
    console.error(`Tools encontrados: ${tools.length}`);
    return { tools };
  } catch (error) {
    console.error("Error listando tools:", error);
    return { tools: [] };
  }
});

/**
 * Ejecutar una herramienta
 */
server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args } = request.params;
  console.error(`Ejecutando tool: ${name}`);
  console.error(`Argumentos:`, JSON.stringify(args, null, 2));
  
  try {
    const result = craftCommand("execute-tool", [name, JSON.stringify(args)]);
    
    return {
      content: [
        {
          type: "text",
          text: JSON.stringify(result, null, 2),
        },
      ],
    };
  } catch (error) {
    console.error(`Error ejecutando tool ${name}:`, error);
    
    return {
      content: [
        {
          type: "text",
          text: JSON.stringify({
            error: true,
            message: error.message
          }, null, 2),
        },
      ],
      isError: true,
    };
  }
});

/**
 * Iniciar servidor
 */
async function main() {
  console.error("=================================");
  console.error("Craft CMS MCP Server");
  console.error("=================================");
  console.error(`CRAFT_PATH: ${CRAFT_PATH}`);
  console.error(`PHP_PATH: ${PHP_PATH}`);
  console.error("=================================");
  
  const transport = new StdioServerTransport();
  await server.connect(transport);
  
  console.error("✓ Servidor MCP iniciado correctamente");
  console.error("✓ Esperando conexiones...");
}

// Manejo de errores global
process.on('uncaughtException', (error) => {
  console.error('Error no capturado:', error);
  process.exit(1);
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('Promesa rechazada no manejada:', reason);
  process.exit(1);
});

// Iniciar
main().catch((error) => {
  console.error("Error fatal iniciando servidor:", error);
  process.exit(1);
});
