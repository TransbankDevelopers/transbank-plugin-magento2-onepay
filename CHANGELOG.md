# Changelog
Todos los cambios notables a este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
y este proyecto adhiere a [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.0.6] - 2018-12-17
### Fixed
- Actualiza sdk php a 1.4.4 que corrige problema de carga de clases Linux.
- Mejora proceso de pago con Onepay.

## [1.0.5] - 2018-12-04
### Fixed
- Corrige lectura de configuraciones.
- Corrige configuraciones internas.
### Added
- Agrega uso de `transactionDescription` cuando el carro tiene un item.

## [1.0.4] - 2018-11-28
### Fixed
- Corrige visualización errónea del botón de instalación de Onepay desde el App Store, que impedía que los usuarios pudieran descargar la aplicación si no la tenían instalada

## [1.0.3] - 2018-11-15
### Changed
- Mejora el comportamiento para usuarios iOS que no poseen la aplicación Onepay instalada

## [1.0.2] - 2018-10-29
### Changed
- Corrige un problema de comunicación entre la ventana de pago de Onepay y el servicio de pago de Onepay
- Corrige un problema al abrir la aplicación instalada de Onepay desde el browser de Android.

## [1.0.1] - 2018-09-14
### Changed
- Se actualiza verión del SDK de Javascript a la 1.5.3

## [1.0.0] - 2018-09-14
### Added
- Primera versión funcional del plugin Magento2 para Onepay
- Implementa pago y reembolso online
