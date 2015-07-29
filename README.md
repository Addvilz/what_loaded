# WhatLoaded?!

WhatLoaded is essentially a autoloader hook that listens for autoload calls and registerall classes that is loaded via autoloader.
It is later possible to analyze this data to get insight on dependencies and whatever othedata you might extract from this.

## Installation

`composer require addvilz/what_loaded`

## Usage

**IMPORTANT:** `WhatLoaded::start` must be invoked **AFTER any other autoload handlers** are registered.
For example, if you are using composer, you must invoke `WhatLoaded::start` after `require '/vendor/autoload.php'`.

### Rendering stats manually

```php
    \Addvilz\WhatLoaded\WhatLoaded::start();
    // ... code
    \Addvilz\WhatLoaded\WhatLoaded::render();
```

### Retrieving collected data

```php
    \Addvilz\WhatLoaded\WhatLoaded::start();
    // ... code
    $data = \Addvilz\WhatLoaded\WhatLoaded::collect();
```

### Rendering stats on shutdown

```php
    \Addvilz\WhatLoaded\WhatLoaded::start();
    \Addvilz\WhatLoaded\WhatLoaded::renderOnShutdown();
    // ... code
```

## License
Licensed under terms and conditions of Apache 2.0 license.