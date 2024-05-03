# crypto-market
Tienda online en donde se pueden comprar criptomonedas.

# Descripción del proyecto
La aplicación será una página web en donde los usuarios podrán ver información sobre las distintas criptomonedas disponibles, interactuar con otros usuarios, realizar compras y evaluar sus rendimientos.

## Usuarios
Al ingresar al sitio, verán la lista de criptomonedas disponibles junto con distintos datos: precio, volumen, porcentaje de variación del precio, etc.
Al clickear en alguna de la lista, podrán ver más detalles sobre la misma: información del proyecto, comentarios de otros usuarios, gráfico ampliado, redes disponibles, etc. En esta misma pantalla podrá realizar compras y ventas.

### Comentarios
Los usuarios podrán dejar comentarios y opiniones al respecto. También podrán votar anónimamente "creo que va a subir"/"creo que va a bajar". Esta información se mostrará a modo de gráfico en la pantalla de información de la criptomoneda.

### Portafolio de inversión
Desde la pantalla de portafolio de inversión, cada usuario podría ingresar y ver el rendimiento de sus compras, y realizar las compras/ventas desde ahí mismo.
Sería el sustituto al "carrito de compras", que por las características del proyecto no lo veo necesario.

## Administradores
Los administradores del sitio podrán ingresar desde la pantalla de administración de Laravel.
Las acciones que podrán realizar son:
* Agregar una nueva criptomoneda a la tienda.
* Ajustar el porcentaje de comisión para la compra/venta para cada una.

Solo se permiten esas dos operaciones ya que el precio y la información de cada criptomoneda es dinámica y no tiene sentido que distintos valores como el precio o volumen sean fijados manualmente por los administradores. 

# Recursos
* Usuarios: los datos relacionados a cada usuario estarán almacenados en la base de datos propia: username, contraseña, transacciones, nacionalidad, etc.
* Criptomonedas: los datos relacionados a cada cripto serán obtenidos desde una API externa, con el fin de lograr un funcionamiento lo más realista posible. De este modo el usuario verá los precios actualizados en tiempo real cuando navega por la aplicación.

# Frontend
Se usará React como framework. Las principales librerías que se utilizarán son:
* react-router-dom para el manejo de rutas.
* react-icons para mostrar los íconos en la aplicación.
* tailwindcss para la definición de los estilos.
* react-query para la solicitud de datos a APIs externas.

# Backend
Se utilizará PHP junto con Laravel para la implementación del backend.
