# Magento 2 module - Bopis Api Connector

- Compatible for Adobe Commerce 2.4.x

## Overview

This module was built to have the possibility to configure the necessary information in the Adobe Commerce Admin panel
and connect with **Walmart API** through the module _**walmart/bopis-sdk**_

## Configuration

Adobe Commerce Admin panel:

* Go to STORES -> Configuration
* Go to SERVICES tab
* Choose Walmart BOPIS

On this section will see the follow options:

* **General**
    * Enable
    * Environment
    * Client Id
    * Client Secret


### Composer Installation

``` 
composer require walmart/module-magento-bopis-api-connector
```

### Version changes
```
@version    = 0.0.1
@author     = josecarlos.filho@blueacornici.com
@date       = Oct 20, 2021

Module creation with a space in the admin panel to configure data that will be used
to connect with Walmart API.
```
