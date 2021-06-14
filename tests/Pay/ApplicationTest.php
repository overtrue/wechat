<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\UriBuilder;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\HttpClient;
use EasyWeChat\Pay\Merchant;
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function test_get_merchant()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(Merchant::class, $app->getMerchant());
        $this->assertSame($app->getMerchant(), $app->getMerchant());
    }

    public function test_get_client()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(HttpClient::class, $app->getHttpClient());
        $this->assertSame($app->getHttpClient(), $app->getHttpClient());
    }

    public function test_get_get_client()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(UriBuilder::class, $app->getClient());
        $this->assertSame($app->getClient(), $app->getClient());

        $this->assertSame('/v3/', $app->getClient()->getUri());
    }

    public function test_get_get_v2_client()
    {
        $app = new Application(
            [
                'mch_id' => 101111111,
                'secret_key' => 'mock-secret-key',
                'private_key' => 'mock-private-key',
                'certificate' => '/path/to/certificate.cert',
                'certificate_serial_no' => 'MOCK-CERTIFICATE-SERIAL-NO',
            ]
        );

        $this->assertInstanceOf(UriBuilder::class, $app->getV2Client());
        $this->assertSame($app->getV2Client(), $app->getV2Client());

        $this->assertSame('/', $app->getV2Client()->getUri());
    }
}
