<?php
declare(strict_types=1);

namespace Lcobucci\MyApi;

use Chimera\Routing\Application;
use Psr\Container\ContainerInterface;
use function assert;

$container = require __DIR__ . '/../config/container.php';
assert($container instanceof ContainerInterface);

$app = $container->get('my-api.http');
assert($app instanceof Application);

$app->run();
