# Transbank Magento2 Onepay Plugin

## Descripción

Este plugin de Magento2 implementa el [SDK PHP de Onepay](https://github.com/TransbankDevelopers/transbank-sdk-php) en modalidad checkout. 

## Dependencias

- Requiere [Composer](https://getcomposer.org)

* transbank/transbank-sdk
* setasign/fpdf

## Nota  
- La versión del sdk de php se encuentra en el archivo `composer.json`
- La versión del sdk de javascript se encuentra en el archivo `view/frontend/layout/checkout_index_index.xml`

## Instalación

El manual de instalación para el usuario final se encuentra disponible [acá](docs/INSTALLATION.md) o en PDF [acá](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay/raw/master/docs/INSTALLATION.pdf
)

**NOTA**: El plugin se puede instalar de dos formas desde packagist.org o directamente desde el repositorio git.

1. Ir a la carpeta base de Magento2

2. [Opción 1] Ejecutar los siguientes comandos para instalar el plugin directamente desde packagist.org:

    ```bash
	composer require transbank/onepay-magento2
    ```
   Esperar mientras las dependencias son actualizadas.

3. [Opción 2] Ejecutar los siguientes comandos para instalar el plugin directamente desde git:

    ```bash
    composer config repositories.transbankonepay vcs https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay.git
	composer require transbank/onepay-magento2:dev-master
    ```
   Esperar mientras las dependencias son actualizadas.

4. Ejecutar los siguientes comandos para habilitar el modulo:

    ```bash
    magento module:enable Transbank_Onepay --clear-static-content
	magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy
    ```
5. Habilitar y configurar el plugin Onepay en la sección de administración de magento2 bajo  Stores/Configuration/Payment Methods/Onepay

6. Configurar APIkey y Shared Secret para ambos ambientes (Producción e Integración)

## Actualización

1. Ir a la carpeta base de Magento2

2. Ejecutar los siguientes comandos para actualizar el plugin

```bash
magento module:disable Transbank_Onepay --clear-static-content
composer update
magento module:enable Transbank_Onepay --clear-static-content
magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy
```

# Otras Notas

Onepay solo trabaja con CLP! Si CLP no es tu moneda principal, no podrás usar este plugin en el proceso de checkout. Esto se encuentra en duro en [payment model](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay/blob/master/Model/Onepay.php)

Si no sabes como realizar esta configuracion puedes verlo en [este documento](docs/INSTALLATION.md)

## Reinstalación

1. Ir a la carpeta base de Magento2

2. Ejecutar los siguientes comandos para deshabilitar y eliminar el plugin:

```bash
magento module:disable Transbank_Onepay --clear-static-content
composer remove transbank/onepay-magento2:dev-master
rm -rf vendor/transbank/onepay*
rm -rf app/code/Transbank/Onepay*
```

3. Seguir el proceso de instalación descrito anteriormente.

## Desarrollo

Para apoyar el levantamiento rápido de un ambiente de desarrollo, hemos creado la especificación de contenedores a través de Docker Compose.

Para usarlo seguir el siguiente [README Magento 2](./docker-magento3)
