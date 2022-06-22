# Magento 2 module - Bopis Preferred Location Frontend

- Compatible for Adobe Commerce 2.4.x

## Overview

This module is used to request the user access to the geolocation.
With the tool https://developer.mozilla.org/en-US/docs/Web/API/Geolocation_API
we are doing the request, the user decide if he wants to allow or deny.

Also, the module control bopis-cookie-handler, the idea is to have 
the possibility to setLocation and getLocation from a cookie.

Location saved in cookie will be important for guests customers.

## Configuration


### Composer Installation

``` 
composer require walmart/module-magento-bopis-preferred-location-frontend
```

### Version changes
```
@version    = 0.0.1
@author     = josecarlos.filho@blueacornici.com
@date       = Nov 3, 2021

Module creation.
```
```
@version    = 0.0.3
@author     = josecarlos.filho@blueacornici.com
@date       = Nov 29, 2021

Fix javascript and HTML knockout to support search location with inventory stock.
```
