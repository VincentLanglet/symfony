<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Messenger\Bridge\AmazonSqs\Tests\Transport;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Bridge\AmazonSqs\Tests\Fixtures\DummyMessage;
use Symfony\Component\Messenger\Bridge\AmazonSqs\Transport\AmazonSqsSerializer;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

class AmazonSqsSerializerTest extends TestCase
{
    public function testEncodedIsDecodable()
    {
        $serializer = new AmazonSqsSerializer();

        $envelope = new Envelope(new DummyMessage('Hello'));

        $encoded = $serializer->encode($envelope);
        $this->assertStringNotContainsString("\0", $encoded['body'], 'Does not contain the binary characters');
        $this->assertEquals($envelope, $serializer->decode($encoded));
    }

    public function testDecodingFailsWithMissingBodyKey()
    {
        $serializer = new AmazonSqsSerializer();

        $this->expectException(MessageDecodingFailedException::class);
        $this->expectExceptionMessage('Encoded envelope should have at least a "body"');

        $serializer->decode([]);
    }

    public function testDecodingFailsWithBadFormat()
    {
        $serializer = new AmazonSqsSerializer();

        $this->expectException(MessageDecodingFailedException::class);
        $this->expectExceptionMessageMatches('/Could not decode/');

        $serializer->decode([
            'body' => '{"message": "bar"}',
        ]);
    }

    public function testDecodingFailsWithBadClass()
    {
        $serializer = new AmazonSqsSerializer();

        $this->expectException(MessageDecodingFailedException::class);
        $this->expectExceptionMessageMatches('/class "ReceivedSt0mp" not found/');

        $serializer->decode([
            'body' => 'O:13:"ReceivedSt0mp":0:{}',
        ]);
    }

    public function testEncodedSkipsNonEncodeableStamps()
    {
        $serializer = new AmazonSqsSerializer();

        $envelope = new Envelope(new DummyMessage('Hello'), [
            new DummyPhpSerializerNonSendableStamp(),
        ]);

        $encoded = $serializer->encode($envelope);
        $this->assertStringNotContainsString('DummyPhpSerializerNonSendableStamp', $encoded['body']);
    }

    public function testNonUtf8IsBase64Encoded()
    {
        $serializer = new AmazonSqsSerializer();

        $envelope = new Envelope(new DummyMessage("\xE9"));

        $encoded = $serializer->encode($envelope);
        $this->assertTrue((bool) preg_match('//u', $encoded['body']), 'Encodes non-UTF8 payloads');
        $this->assertEquals($envelope, $serializer->decode($encoded));
    }

    public function testSomeUtf8IsBase64Encoded()
    {
        $serializer = new AmazonSqsSerializer();

        $envelope = new Envelope(new DummyMessage("\x7"));
        $this->assertTrue((bool) preg_match('//u', $envelope->getMessage()->getMessage()));

        $encoded = $serializer->encode($envelope);
        $this->assertNotFalse(base64_decode($encoded['body'], true));
        $this->assertEquals($envelope, $serializer->decode($encoded));
    }
}

class DummyPhpSerializerNonSendableStamp implements NonSendableStampInterface
{
}
