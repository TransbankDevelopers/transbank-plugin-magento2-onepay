# Transbank Magento2 Onepay Plugin

## Description

This Magento2 plugin implemented the [SDK PHP de Onepay](https://github.com/TransbankDevelopers/transbank-sdk-php) in checkout modality. 

## Dependencies

- Require [Composer](https://getcomposer.org)

* transbank/transbank-sdk
* setasign/fpdf

## Install

The installation manual for the end user is available [here](docs/INSTALLATION.md) or in PDF [here](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay/raw/master/docs/INSTALLATION.pdf
)

1. Go to Magento2 root folder

2. Enter following commands to install module:

    ```bash
    composer config repositories.transbankonepay vcs https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay.git
	composer require transbank/onepay:dev-master
    ```
   Wait while dependencies are updated.

3. Enter following commands to enable module:

    ```bash
    magento module:enable Transbank_Onepay --clear-static-content
	magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy
    ```
4. Enable and configure Onepay in Magento Admin under Stores/Configuration/Payment Methods/OnePay

5. Configure APIkey and Shared Secret for both environments (Production and Integration)

## Update

1. Go to Magento2 root folder

2. Enter following commands to update module:

```bash
magento module:disable Transbank_Onepay --clear-static-content
composer remove transbank/onepay:dev-master
rm -rf vendor/transbank/onepay/
rm -rf app/code/Transbank
composer require transbank/onepay:dev-master
magento module:enable Transbank_Onepay --clear-static-content
magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy
```
## Other Notes

Onepay works with CLP only! If CLP is not your base currency, you will not see this module on checkout pages. This condition is hardcoded in [payment model](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay/blob/master/Model/Onepay.php)