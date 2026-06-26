El archivo .claude/project.md contiene el detalle del proyecto.

Tu objetivo en esta sesión es trabajar exclusivamente sobre el primer grupo de prioridad en .claude/tasks.md (encabezado "##") que contenga tareas pendientes (status "[ ]").

Para este grupo activo, realiza el siguiente proceso cíclico:
1. Identifica la primera tarea pendiente "[ ]" del grupo.
2. Implementa los cambios necesarios para cumplir sus criterios de terminación.
3. Genera y ejecuta los tests correspondientes para verificar que funcione.
4. Si los tests pasan, marca la tarea como completada "[x]" en .claude/tasks.md.
5. Escribe un resumen detallado del trabajo realizado para esta tarea en .claude/log.md.
6. Si quedan más tareas pendientes EN ESTE MISMO GRUPO, regresa al paso 1 y continúa inmediatamente con la siguiente.

Reglas estrictas:
- No saltes a tareas de otros grupos de prioridad.
- Haz todas las tareas que puedas de este grupo en esta única sesión.
- No pidas autorización para hacer cambios en el código, ni para correr tests ni comandos.
- Cuando ya no queden tareas pendientes en este grupo específico (o si se completó todo el grupo), despliega '<promise>COMPLETE</promise>' para finalizar la sesión.
