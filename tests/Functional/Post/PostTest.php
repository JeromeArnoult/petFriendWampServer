<?php

namespace App\Tests\Fonctional\Post;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostTest extends WebTestCase 
{
    public function testBlogPageWorks() : void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET,'/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertSelectorExists('h1');
        $this->assertSelectorTextContains('h1', 'Pet Friend : Le site dédié à nos amis les animaux !');
    }

    public function testPaginationWorks(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET,'/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $posts = $crawler->filter('div.card');
        $this->assertEquals(6, count($posts));

        $link = $crawler->selectLink('2')->extract(['href'])[0];
        $client->request(Request::METHOD_GET, $link);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $posts = $crawler->filter('div.card');
        $this->assertGreaterThanOrEqual(1, count($posts));
    }
}