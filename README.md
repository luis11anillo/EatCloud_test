# Referecias API.

### GET
- **Todos los encabezados.** <br> Obtiene todos los datos de la tabla Encabezados
```php
  GET http://localhost/api/?tabla=encabezados&id=todos
```
- **Todos los puntos.** <br> Obtiene todos los datos de la tabla Puntos
```php
  GET http://localhost/api/?tabla=puntos&id=todos
```
- **Todos los detalles.** <br> Obtiene todos los datos de la tabla Detalles
```php
  GET http://localhost/api/?tabla=detalles&id=todos
```
> Para obtener por el ID: http://localhost/api/?tabla={{Nombre_tabla}}&id={{id}}
```php
  GET http://localhost/api/?tabla=detalles&id=1
```
Obtiene el registro con id: 1 de la tablas detalles, 
