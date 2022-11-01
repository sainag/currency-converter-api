<?php

namespace Tests\Unit;

use Tests\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * Test currency list
     *
     * @return void
     */
    public function test_currency_list()
    {
        $response = $this->get('/api/currency/');

        $response->assertStatus(500);
    }

    /**
     * Test currency convert route
     *
     * @return void
     */
    public function test_currency_convert()
    {
        $response = $this->get('/api/currency/convert');

        $response->assertStatus(500);
    }

    /**
     * Test currency history report
     *
     * @return void
     */
    public function test_currency_report()
    {
        $response = $this->get('/api/currency/report');

        $response->assertStatus(500);
    }

    /**
     * Test user currency history report
     *
     * @return void
     */
    public function test_user_currency_report()
    {
        $response = $this->get('/api/currency/1/report');

        $response->assertStatus(500);
    }

    /**
     * Test user currency history report by id
     *
     * @return void
     */
    public function test_user_currency_report_id()
    {
        $response = $this->get('/api/currency/1/report/1');

        $response->assertStatus(500);
    }
}
