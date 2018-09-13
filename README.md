# Transbank Magento2 Onepay Plugin

## Descripción

Este plugin de Magento2 implementa el [SDK PHP de Onepay](https://github.com/TransbankDevelopers/transbank-sdk-php) en modalidad checkout. 

## Dependencias

- Requiere [Composer](https://getcomposer.org)

* transbank/transbank-sdk
* setasign/fpdf

## Instalación

El manual de instalación para el usuario final se encuentra disponible [acá](docs/INSTALLATION.md) o en PDF [acá](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay/raw/master/docs/INSTALLATION.pdf
)

1. Ir a la carpeta base de Magento2

2. Ingresar los siguientes comandos para instalar el plugin:

    ```bash
    composer config repositories.transbankonepay vcs https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay.git
	composer require transbank/onepay:dev-master
    ```
   Esperar mientras las dependencias son actualizadas.

3. Ingresar los siguientes comandos para habilitar el modulo:

    ```bash
    magento module:enable Transbank_Onepay --clear-static-content
	magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy
    ```
4. Habilitar y configurar el plugin OnePay en la sección de administración de magento2 bajo  Stores/Configuration/Payment Methods/OnePay

5. Configurar APIkey y Shared Secret para ambos ambientes (Producción e Integración)

## Actualización

1. Ir a la carpeta base de Magento2

2. Ingresar los siguientes comandos para actualizar el plugin:

```bash
magento module:disable Transbank_Onepay --clear-static-content
composer remove transbank/onepay:dev-master
rm -rf vendor/transbank/onepay/
rm -rf app/code/Transbank
composer require transbank/onepay:dev-master
magento module:enable Transbank_Onepay --clear-static-content
magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy
```
## Otras Notas

Onepay solo trabaja con CLP! Si CLP no es tu modena principal, no podrás usar este plugin en el proceso de checkout. Esto se encuentra en duro en [payment model](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay/blob/master/Model/Onepay.php)