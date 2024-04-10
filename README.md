# Referecias API.
Para el desarrollo de esta prueba se creo una base de datos **"dbdonantes_colombia"** y tres tablas: **encabezados, detalles y puntos**  

## GET
Consultas a la base de datos:
> *la Tabla y el ID en la URL son pasados como parametros*

- **Registro de una tabla.** <br> Para obtener un registro de una tabla se usa la siguiente estructura de URL
```php
  GET http://localhost/api/?tabla={{nombre_tabla}}&id={{id}}
```
- **Todos los registros de una tabla.** <br> Para obtener todos los registros de una tabla se usa la siguiente estructura de URL
```php
  GET http://localhost/api/?tabla={{nombre_tabla}}&id=todos
```
**Ejemplo:**
<br> Obtiene el registro con el id: 1 de la tabla detalles
```php
  GET http://localhost/api/?tabla=detalles&id=1
```

Obtiene todos los registros de la tabla detalles
```php
  GET http://localhost/api/?tabla=detalles&id=todos
```
<br>

## POST
Para crear una donacion se utiliza la siguiente URL
```php
  POST http://localhost/api/?tabla=puntos
```
JSON de prueba
```JSON
{
    "data": [
        {
            "codigo_donacion": "COD001",
            "id_punto": 33,
            "nombre_producto": "Arroz",
            "codigo_producto": "ARZ001",
            "cantidad": 10,
            "kg_unitario": 1.5,
            "costo_unitario": 2000
        },
        {
            "codigo_donacion": "COD001",
            "id_punto": 33,
            "nombre_producto": "Frijoles",
            "codigo_producto": "FRJ001",
            "cantidad": 8,
            "kg_unitario": 1.8,
            "costo_unitario": 4500
        }
    ]
}
```
**NOTA.**
<br>
> - Antes de crear una donacion se debe insertar un registro en la tabla **Puntos**, puesto que antes de que el JSON pase a la Base de datos se valida si existe un punto por medio de id_punto, por esa razon se debe crear primero un registro en la tabla puntos
> - Para calcular el **Kg_total** y **costo_total** que se encuentra en la tabla encabezado, en el JSON anterior el **codigo_donacion** debe ser igual
> - Cuando se ejecuta el POST se crea en este caso 2 registros en la tabla **detalles** y uno en la tabla **encabezados**
