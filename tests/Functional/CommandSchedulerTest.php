<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Utils\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CommandSchedulerTest extends WebTestCase
{
    /**
     * Test for checking if command scheduler route available only for admin users.
     *
     * @throws Throwable
     */
    public function testCommandSchedulerAvailableOnlyForAdminUsers(): void
    {
        $client = $this->getTestClient();
        $client->request('GET', '/command-scheduler/list');

        /** @var Response $response */
        $response = $client->getResponse();
        static::assertInstanceOf(Response::class, $response);
        // check for 401 due to allow only for user with admin role
        static::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        unset($response, $client);
    }
}
