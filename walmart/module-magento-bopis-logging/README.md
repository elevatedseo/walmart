# Magento 2 module - Bopis Logging

- Compatible for Adobe Commerce 2.4.x

## Overview

This module is responsible to register all log record inside BOPIS implementation.
The module can be used from others Adobe Commerce modules and _**walmart/bopis-sdk**_ if It's necessary  

For now, we have 4 levels:

- Error - the operation was not possible to finish
- Warning - non critical error which has to be logged
- Log - informational messages - start/end of operation, number of records processed
- Debug - logs detailed information like payload which is being uploaded through API

All record are saved on **var/log/walmart-bopis.log**

## Configuration

Adobe Commerce Admin panel:

* Go to STORES -> Configuration
* Go to SERVICES tab
* Choose Walmart BOPIS

On this section will see the follow options:

* **Logging**
    * Debug


### Composer Installation

``` 
composer require walmart/module-magento-bopis-logging
```

### How to use

Include _Walmart\BopisLogging\Service\Logger_ as dependency in your **__construct** method.
Initialize the object, and you can use on this way:
```
use Walmart\BopisLogging\Service\Logger as BopisLogger;

public function __construct( BopisLogger $bopisLogger )
{
    $this->logger = $logger;
    $this->bopisLogger = $bopisLogger;
}
    
$this->bopisLogger->info($message,[]);
$this->bopisLogger->error($message,[]);
$this->bopisLogger->warning($message,[]);
$this->bopisLogger->debug($message,[]);
```

### Version changes
```
@version    = 0.0.1
@author     = josecarlos.filho@blueacornici.com
@date       = Oct 27, 2021

Module creation with a space in the admin panel to configure data Enable/Disable.
```
