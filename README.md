magento2-Transbank_Onepay
======================

Transkbank Onepay payment gateway Magento2 extension

Install
=======

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

5. Configure APIkey and Shared Secret for both environments
