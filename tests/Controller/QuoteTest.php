<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuoteTest extends WebTestCase
{
    public function showQuoteTest()
    {
        $client = static::createClient();
        $client->request('GET', '/Quote/convert');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}