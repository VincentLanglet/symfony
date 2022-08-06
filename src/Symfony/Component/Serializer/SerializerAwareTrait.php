<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer;

/**
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 *
 * @template T of SerializerInterface
 */
trait SerializerAwareTrait
{
    /**
     * @var SerializerInterface
     * @psalm-var T
     */
    protected $serializer;

    /**
     * @psalm-param T $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
}
