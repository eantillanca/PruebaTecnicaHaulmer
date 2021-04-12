# PruebaTecnicaHaulmer
API REST para CRUD de usuarios con autenticación mediante JWT

## INSTALACIÓN 

Una vez clonado el respositorio y estando en su directorio, ejecutar los comandos:

~~~~
composer install 
cp .env.example .env 
php artisan key:generate
php artisan jwt:secret
docker-compose up
~~~~

Una vez el contenedor docker este creado abrir una nueva terminar en el directorio del proyecto y ejecutar los siguientes comandos: 

~~~~
docker exec -ti mysql bash
mysql -u root -p -e "create database if not exists <nombre-db>"

(contraseña por defecto configurada en docker-compose.yml: 123456)

exit
~~~~


## VARIABLES DE ENTORNO 

en archivo .env agregar: 

~~~~
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=<nombre-db>
DB_USERNAME=root
DB_PASSWORD=123456

MOCKAPI_URL=https://607096da85c3f000174707a9.mockapi.io/api/v1/users
~~~~

finalmente con la base de datos creada y conectada al proyecto ejecutar las migraciones con el comando: 

~~~~
docker exec -ti sk2020-container php artisan migrate
~~~~




## SUPUESTOS

- Docker ya debería estar instalado y corriendo en el equipo anfitrión. 
- Se asume que al utilzar JWT para el login, existe una base de datos de usuarios, 
por tanto esta base de datos es creada en la instación del proyecto. 
- Mockapi se utilza para guardar datos extra del usuario (nombre, direccion, telefono y profesión) 
además del correo y contraseña por el cual hace login en base de datos.
-  al crear un nuevo usuario y guardarlo en mockapi, se guarda en base de datos el id con el cual 
fue registrado en mockapi para luego obtener sus datos. 
- Las peticiones que requieren estar logeado en el endpoint /me toman el token de autenticación enviado por el usuario
para identificarlo y de esta forma solo podrá acceder a su información.
- Si el toquen de autenticación no es enviado, el middleware jwt no dejará pasar las peticiones y devolverá un mensaje
de token no entontrado, invalido o expirado segun corresponda.
