[< table of contents](../README.md)

# Dependency Injection

Starbug uses [PHP-DI](http://php-di.org) as a dependency injection container. This container implements [Psr\Container\ContainerInterface](https://github.com/container-interop/container-interop) and provides a robust [PHP configuration format](http://php-di.org/doc/php-definitions.html).

In most cases you should not instantiate classes manually, but rather allow them to be instantiated by the container. The container will recursively resolve the dependencies of any class it is instantiating. Type hint your method parameters where possible, especially in your constructors. The container will be able to resolve those parameters by that class or interface name. To map interfaces to concrete classes or further control how classes are instantiated and wired up, you can put PHP-DI definitions in `etc/di.php`.

## Custom classes

If you create a custom class, the container will try to resolve your constructor parameters automatically.

```php
<?php
namespace Starbug\App;
use Starbug\Db\DatabaseInterface;
class MyCustomClass {
  public function __construct(DatabaseInterface $db, $timezone, $name) {
    $this->db = $db;
    $this->timezone = $timezone;
    $this->name = $name;
  }
}
```

In the above example, the container will try to instantiate a `DatabaseInterface` for the `$db` parameter. Since you can't instantiate an interface, this would have to be mapped to a concrete class to succeed, and it is indeed mapped to a concrete class. The container will not be able to resolve the `$timezone` and `$name` parameters. For these, we would have to do some manual configuration.

```php
<?php
return [
  'Starbug\App\MyCustomClass' => DI\autowire()
    ->constructorParameter('timezone', DI\get('time_zone'))
    ->constructorParameter('name', 'SomeName')
];
```

Then it can be automatically injected into other classes when they are constructed.

```php
<?php
namespace Starbug\App;
class MyOtherClass {
  public function __construct(MyCustomClass $custom) {
    $this->custom = $custom;
  }
}
```

## Controllers

`Starbug\Routing\Controller` has no constructor injected dependencies so you can freely define a constructor to inject dependencies.

```php
<?php
namespace Starbug\App;

use Starbug\Routing\Controller;

class HomeController extends Controller {
  public function __construct(MyOtherClass $abdul) {
    $this->abdul = $abdul;
  }
}
```

## Displays

Displays already have a significant number of dependencies. It is therefore recommended that you use method injection to get additional dependencies into a display. First be sure the display doesn't already have that dependency and then define a method to inject it.

```php
<?php
namespace Starbug\App;
use Starbug\Core\FormDisplay;
class MyForm extends FormDisplay {
  public function setAbdul(MyOtherClass $abdul) {
    $this->abdul = $abdul;
  }
}
```

In this case, you must also configure the container to perform the injection automatically when this class is instantiated.

```php
<?php
return [
  'Starbug\App\MyForm' => DI\autowire()
    ->method('setAbdul', DI\get('Starbug\App\MyOtherClass'))
]
```

## Others

In general, you should check the ancestor classes so you can see what dependencies they have and decide how to proceed. **If you are replacing the constructor, you must include all the dependencies that are included in the parent constructor**.
