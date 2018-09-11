# Manual de instalación para Plugin Magento2

## Descripción

Este plugin oficial ha sido creado para que puedas integrar Onepay fácilmente en tu comercio, basado en Magento2.

## Requisitos

Ud. debe tener instalado previamente Magento2 o usar esta guia para instalar uno basado en docker.

Además debe crear una cuenta en Magento Marketplace siguiendo este tutorial oficial: [https://devdocs.magento.com/guides/v2.2/install-gde/prereq/connect-auth.html](https://devdocs.magento.com/guides/v2.2/install-gde/prereq/connect-auth.html)

Luego de crear la cuenta y crear la llave de acceso debe respaldar "Public Key" y "Private Key" dado que pueden ser requeridas durante el proceso de instalación de magento2.

## Instalación de la imagen docker de Magento2 para probar el plugin

1. Diríjase a [https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay-example](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay-example) y clone el repositorio.

  Una vez clonado el repositorio puede seguir el README o seguir esta guia para proceder con la instalación de Magento2.

  Ingrese a la carpeta "transbank-plugin-magento2-onepay-example" y ejecute el comando:

    ./init

  ![Paso 1](img/paso1.png)

  ![Paso 2](img/paso2.png)

  Cuando finalice, ejecutar el comando:

    ./shell

  ![Paso 3](img/paso3.png)

  Al ingresar al contenedor, ejecutar el comando (Si el proceso de instalación pide autenticarse ingrese como username el valor de su "Public key" y como password el valor de su "Private Key" obtenidos anteriormente):

    install-magento2

  ![Paso 4](img/paso4.png)

  Cuando finalice, ejecutar los comandos:

    magento sampledata:deploy && magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy

  ![Paso 5](img/paso5.png)

  Con esto se ha instalado Magento2 y ya puede ser usado

    - Sitio: http://localhost
    - Admin: http://localhost/admin
      - usuario: admin
      - clave: admin123

## Instalación del Plugin

1. Diríjase a [https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay](https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay) para ver el repositorio del plugin.

  Solamente si ha salido del contenedor, ejecutar el comando:

    ./shell

  ![Paso 3](img/paso3.png)

  Dentro del contenedor, ejecutar el comando:

    composer config repositories.transbankonepay vcs https://github.com/TransbankDevelopers/transbank-plugin-magento2-onepay.git

  ![Paso 6](img/paso6.png)

  Cuando finalice, ejecutar el comando:

    composer require transbank/onepay:dev-master 

  ![Paso 7](img/paso7.png)

  Cuando finalice, ejecutar el comando:

    magento module:enable Transbank_Onepay --clear-static-content

  ![Paso 8](img/paso8.png)

  Cuando finalice, ejecutar el comando:

    magento setup:upgrade && magento setup:di:compile && magento setup:static-content:deploy

  ![Paso 9](img/paso9.png)
  
2. Una vez realizado el proceso anterior, Magento2 debe haber instalado el plugin Onepay. Cuando finalice, debe activar el plugin en el administrador de Magento2.

## Configuración

Este plugin posee un sitio de configuración que te permitirá ingresar credenciales que Transbank te otorgará, y además podrás generar un documento de diagnóstico en caso que Transbank te lo pida.

Para acceder a la configuración, debes seguir los siguientes pasos:

1. Dirígete a la página de administración de Magento2 (usualmente en http://misitio.com/admin, http://localhost/admin) e ingrese usuario y clave.

  ![Paso 10](img/paso10.png)
  
2. Dentro del sitio de administración dirigirse a (Stores / Configuration).

  ![Paso 11](img/paso11.png)

3. Luego a sección (Sales / Payments Methods).

  ![Paso 12](img/paso12.png)

4. Elejir país Chile

  ![Paso 13](img/paso13.png)

5. Bajando al listado de metodos de pagos verá OnePay

  ![Paso 14](img/paso14.png)

6. ¡Ya está! Estás en la pantalla de configuración del plugin, debes ingresar la siguiente información:
  * **Enable**: Al activarlo, Onepay estará disponible como medio de pago. Ten la precaución de que se encuentre marcada esta opción cuando quieras que los usuarios paguen con Onepay.
  * **Endpoint**: Ambiente hacia donde se realiza la transacción. 
  * **APIKey**: Es lo que te identifica como comercio.
  * **Shared Secret**: Llave secreta que te autoriza y valida a hacer transacciones.
  
  Las opciones disponibles para _Endpoint_ son: "Integración" para realizar pruebas y certificar la instalación con Transbank, y "Producción" para hacer transacciones reales una vez que Transbank ha aprobado el comercio. Dependiendo de cual Endpoint se ha seleccionado el plugin usará uno de los dos set de APIKey y Shared Secret según corresponda. 
  
### Credenciales de Prueba

Para el ambiente de Integración, puedes utilizar las siguientes credenciales para realizar pruebas:

* APIKey: `dKVhq1WGt_XapIYirTXNyUKoWTDFfxaEV63-O5jcsdw`
* Shared Secret: `?XW#WOLG##FBAGEAYSNQ5APD#JF@$AYZ`

7. Guardar los cambios presionando el botón [Save Config]

  ![Paso 15](img/paso15.png)

8. Además, puedes generar un documento de diagnóstico en caso que Transbank te lo pida. Para ello, haz click en "Generar PDF de Diagnóstico", y automáticamente se descargará dicho documento.

  ![Paso 17](img/paso17.png)

## Prueba de instalación con transacción

En ambiente de integración es posible realizar una prueba de transacción utilizando un emulador de pagos online.

* Ingrese al comercio, puede usar los datos de prueba

  - Email: roni_cost@example.com
  - Password: roni_cost3@example.com

  ![Paso 1](img/paso18.png)

* Ya con la sesión iniciada, ingrese a cualquier sección para agregar productos

  ![Paso 2](img/paso19.png)

* Agregue al carro de compras un producto:

  ![Paso 3](img/paso20.png)

* Seleccione el carro de compras y luego presione el botón [Proceed to Checkout]:

  ![Paso 4](img/paso21.png)

* Seleccione método de envío y presione el botón [Next]

  ![Paso 5](img/paso22.png)

* Seleccione método de pago Transbank Onepay, luego precione el botón [Place Order]

  ![Paso 6](img/paso23.png)

* Una vez presionado el botón para iniciar la compra, se mostrará la ventana de pago Onepay, tal como se ve en la imagen. Toma nota del número que aparece como "Código de compra", ya que lo necesitarás para emular el pago en el siguiente paso:
  
  ![Paso 7](img/paso24.png)
  
* En otra ventana del navegador, ingresa al emulador de pagos desde [https://onepay.ionix.cl/mobile-payment-emulator/](https://onepay.ionix.cl/mobile-payment-emulator/), utiliza test@onepay.cl como correo electrónico, y el código de compra obtenido desde la pantalla anterior. Una vez ingresado los datos solicitados, presiona el botón "Iniciar Pago":
* 
  ![Paso 8](img/paso25.png)
  
* Si todo va bien, el emulador mostrará opciones para simular situaciones distintas. Para simular un pago exitoso, presiona el botón `PRE_AUTHORIZED`. En caso de querer simular un pago fallido, presiona le botón `REJECTED`. Simularemos un pago exitóso presionando el botón `PRE_AUTHORIZED`.

  ![Paso 9](img/paso26.png)
  
* Vuelve a la ventana del navegador donde se encuentra Magento2 y podrás comprobar que el pago ha sido exitoso.

 ![Paso 10](img/paso27.png)

* Además si accedes al sitio de administración seccion (Sales / Ordes) se podra ver la orden creada y el detalle de los datos entregados por OnePay.

 ![Paso 11](img/paso28.png)

 ![Paso 12](img/paso29.png)

 ![Paso 13](img/paso30.png)