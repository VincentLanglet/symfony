<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Normalizer;

/**
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 *
 * @template T of DenormalizerInterface
 */
interface DenormalizerAwareInterface
{
    /**
     * Sets the owning Denormalizer object.
     *
     * @psalm-param T $denormalizer
     */
    public function setDenormalizer(DenormalizerInterface $denormalizer);
}
