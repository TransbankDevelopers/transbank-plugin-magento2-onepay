![Magento 2](https://cdn.rawgit.com/rafaelstz/magento2-snippets-visualstudio/master/images/icon.png)

#  Magento 2 Docker para desarrollo

### Apache 2.4 + PHP 7.2 + MariaDB

## Requerimientos

**MacOS:**

Instalar [Docker](https://docs.docker.com/docker-for-mac/install/), [Docker-compose](https://docs.docker.com/compose/install/#install-compose) y [Docker-sync](https://github.com/EugenMayer/docker-sync/wiki/docker-sync-on-OSX).

**Windows:**

Instalar [Docker](https://docs.docker.com/docker-for-windows/install/), [Docker-compose](https://docs.docker.com/compose/install/#install-compose) y [Docker-sync](https://github.com/EugenMayer/docker-sync/wiki/docker-sync-on-Windows).

**Linux:**

Instalar [Docker](https://docs.docker.com/engine/installation/linux/docker-ce/ubuntu/) y [Docker-compose](https://docs.docker.com/compose/install/#install-compose).

**Cuenta**

Además debes tener o crear una cuenta en Magento Marketplace siguiendo este tutorial oficial: [https://devdocs.magento.com/guides/v2.2/install-gde/prereq/connect-auth.html](https://devdocs.magento.com/guides/v2.2/install-gde/prereq/connect-auth.html)

Luego de crear la cuenta y crear la llave de acceso debes respaldar "Public Key" y "Private Key" dado que pueden ser requeridas durante el proceso de instalación de magento2.

## Instalación y/o ejecución

**NOTA:** Puedes seguir este README, pero además existe un documento más detallado con imagenes de este proceso:

[Documento de instalación detallado](docs/INSTALLATION.md)

### Como instalar magento2

Para instalar Magento 2, hacer lo siguiente:

Además se puede especificar la versión a instalar (e.j. `install-magento2 2.3.1`).

```
./start
./shell
install-magento2 2.3.1
magento sampledata:deploy && magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy
composer require setasign/fpdf:1.8.1
composer require transbank/transbank-sdk:VERSION
```

### Como usar

### Iniciar el contenedor

```
./start
```

### Acceder al contenedor

```
./shell
```

### Copiar el plugin al contenedor

```
./copy-plugin
```

### Desplegar el plugin dentro de contenedor anteriormente copiado

```
./shell
./deploy-plugin
```

### Archivo de logs del plugin

```
./shell
tail -f /var/www/html/administrator/logs/webpay-log.log.php
```

### Instala el plugin de Onepay en magento2 siguiendo el README

[Ir al plugin de Onepay](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay)

## Paneles (Estos comandos son información extra)

**Web server:** http://localhost/

**Admin:** http://localhost/admin

    user: admin
    password: admin123

**PHPMyAdmin:** http://localhost:8090

### Lista de commandos

| Comandos  | Descripcion  | Opciones & Ejemplos |
|---|---|---|
| `./start`  | Iniciar los contenedores  | |
| `./stop`  | Detener los contenedores  | |
| `./kill`  | Detener los contendores y eliminar contenedores, networks, volumes, e images creadas para el proyecto  | |
| `./shell`  | Aceder al contenedor  | `./shell root` | |

## Extras para usar ngrok y probar en dominio virtual especialmente para emular producción

1.- Ejecutar ngrok y obtener la url dada por ngrok en `Forwarding` http

    ngrok http 80

2.- Ir al admin de magento2 sección (Stores / Web / Base URLs)

    - Modificar `Base URL` y `Base Link URL` estableciendo la url entregada por ngrok

    Ej: http://acd877c2.ngrok.io/

### Licencia

MIT © 2018

Basado en: [Rafael Corrêa Gomes](https://github.com/rafaelstz/) and [contributors](https://github.com/clean-docker/Magento2/graphs/contributors).
