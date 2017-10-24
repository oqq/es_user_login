<?php

declare(strict_types=1);

namespace Oqq\EsUserLogin\Domain;

use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

abstract class Command extends \Prooph\Common\Messaging\Command implements PayloadConstructable
{
    use PayloadTrait;
}
