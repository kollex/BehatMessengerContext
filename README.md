Behat Messenger Context Bundle
=================================

| Version | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
| `master`| [![CI][master Build Status Image]][master Build Status] | [![Coverage Status][master Code Coverage Image]][master Code Coverage] |
| `develop`| [![CI][develop Build Status Image]][develop Build Status] | [![Coverage Status][develop Code Coverage Image]][develop Code Coverage] |

Installation
============

Step 1: Download the Bundle
----------------------------------
Open a command console, enter your project directory and execute:

###  Applications that use Symfony Flex [in progress](https://github.com/MacPaw/BehatRedisContext/issues/2)

```console
$ composer require --dev macpaw/behat-messenger-context
```

### Applications that don't use Symfony Flex

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require --dev macpaw/behat-messenger-context
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.


Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            BehatMessengerContext\BehatMessengerContextBundle::class => ['test' => true],
        );

        // ...
    }

    // ...
}
```

Step 2: Update Container config to load Messenger Context
----------------------------------
In the `config/services_test.yaml` file of your project:

```
    BehatMessengerContext\:
        resource: '../vendor/macpaw/behat-messenger-context/src/*'
        arguments:
            $container: '@test.service_container'
            $placeholderPatternMap:
                'datetime_atom': '/\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1])T[0-2]\d:[0-5]\d:[0-5]\d[+-][0-2]\d:[0-5]\d/'
```

Step 3: Configure Messenger 
=============
Copying `config/packages/dev/messenger.yaml` and pasting that into `config/packages/test/`. This gives us messenger configuration that will only be used in the test environment. Uncomment the code, and replace sync with in-memory. Do that for both of the transports.

```yaml
framework:
    messenger:
        transports:
            async: 'in-memory://'
            async_priority_high: 'in-memory://'
            ...
...
```


Step 4: Configure Behat
=============
Go to `behat.yml`

```yaml
...
  contexts:
    - BehatMessengerContext\Context\MessengerContext
...
```

[master Build Status]: https://github.com/macpaw/behat-messenger-context/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/behat-messenger-context/workflows/CI/badge.svg?branch=master
[develop Build Status]: https://github.com/macpaw/behat-messenger-context/actions?query=workflow%3ACI+branch%3Adevelop
[develop Build Status Image]: https://github.com/macpaw/behat-messenger-context/workflows/CI/badge.svg?branch=develop
[master Code Coverage]: https://codecov.io/gh/macpaw/behat-messenger-context/branch/master
[master Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/behat-messenger-context/master?logo=codecov
[develop Code Coverage]: https://codecov.io/gh/macpaw/behat-messenger-context/branch/develop
[develop Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/behat-messenger-context/develop?logo=codecov
