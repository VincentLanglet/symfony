<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Messenger\Bridge\AmazonSqs\Transport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

class AmazonSqsSerializer extends PhpSerializer
{
    public function encode(Envelope $envelope): array
    {
        $encoded = parent::encode($envelope);
        if (preg_match('/[^\x20-\x{D7FF}\xA\xD\x9\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', $encoded['body'])) {
            $encoded['body'] = base64_encode($encoded['body']);
        }

        return $encoded;
    }
}
