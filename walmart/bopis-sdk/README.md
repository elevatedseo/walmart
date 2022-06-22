# BOPIS SDK for PHP

## Overview
This module is an SDK built to connect Adobe Commerce platform with Walmart API.

The main idea here is that SDK will handle operations towards Walmart API 
with already build data based on SDK specifications which we will build.

### Composer Installation

```
composer require walmart/bopis-sdk
```

### Initialization
```
use BopisSdk\Connection;

$connection = new Connection($server, $consumerId, $consumerSecret);

$token = $connection->getToken();
```

### Version changes
```
@version    = 1.0.0
@author     = maxime.carey@blueacornici.com
@date       = May 15, 2022

PHP module creation with basic PHP class to connect with API Walmart.
```

