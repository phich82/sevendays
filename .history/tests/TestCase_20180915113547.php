<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $test = $this;

        TestResponse::macro('followRedirects', function ($testCase = null) use ($test) {
            $response = $this;
            $testCase = $testCase ?: $test;

            while ($response->isRedirect()) {
                $response = $testCase->get($response->headers->get('Location'));
            }

            return $response;
        });
    }

    /**
     * Call the given URI by Ajax and return the Response.
     *
     * @param string $uri
     * @param string $method
     * @param array $data
     * @param array $headers
     * @return TestResponse
     */
    public function ajax($uri, $method = 'GET', $data = [], array $headers = [])
    {
        $headers = array_merge(['HTTP_X-Requested-With' => 'XMLHttpRequest'], $headers);
        $server = $this->transformHeadersToServerVars($headers);
        return $this->call($method, $uri, $data, [], [], $server);
    }
}
