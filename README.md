# Referecias API.
Para el desarrollo de esta prueba se creo una base de datos **"dbdonantes_colombia"** y tres tablas: **encabezados, detalles y puntos**  

### GET
Consultas a la base de datos:
- **Todos los registros de una tabla.** <br> Obtiene todos los datos de la tabla Encabezados/ Para obtener todos los registros de una tabla
```php
  GET http://localhost/api/?tabla=encabezados&id=todos
```

