<?php

namespace App\Tests\Fonctional\Blog;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogTest extends WebTestCase 
{
    public function testBlogPageWorks() : void
    {
        // Création d'un client HTTP pour simuler des requêtes
        $client = static::createClient();

        // Envoi d'une requête GET à la racine du site
        $client->request(Request::METHOD_GET, '/');

        // Vérification que la réponse est réussie (code 2xx)
        $this->assertResponseIsSuccessful();

        // Vérification que le code de statut de la réponse est HTTP 200 (OK)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérification qu'un élément <h1> existe dans la réponse HTML
        $this->assertSelectorExists('h1');

        // Vérification que le texte de l'élément <h1> contient la chaîne spécifiée
        $this->assertSelectorTextContains('h1', 'Pet Friend : Le site dédié à nos amis les animaux !');
    }

    public function testPaginationWorks(): void
    {
        // Création d'un client HTTP pour simuler des requêtes
        $client = static::createClient();

        // Envoi d'une requête GET à la racine du site et récupération du crawler (outil de navigation dans le DOM)
        $crawler = $client->request(Request::METHOD_GET, '/');

        // Vérification que la réponse est réussie (code 2xx)
        $this->assertResponseIsSuccessful();

        // Vérification que le code de statut de la réponse est HTTP 200 (OK)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Sélection des éléments div avec la classe 'card' et comptage du nombre d'éléments
        $posts = $crawler->filter('div.card');
        $this->assertEquals(6, count($posts));

        // Sélection du lien contenant le texte '2' (lien de pagination) et extraction de son href
        $link = $crawler->selectLink('2')->extract(['href'])[0];

        // Envoi d'une requête GET au lien extrait
        $client->request(Request::METHOD_GET, $link);

        // Vérification que la réponse est réussie (code 2xx)
        $this->assertResponseIsSuccessful();

        // Vérification que le code de statut de la réponse est HTTP 200 (OK)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Sélection des éléments div avec la classe 'card' après la navigation
        $posts = $crawler->filter('div.card');

        // Vérification qu'il y a au moins un élément 'card' sur la nouvelle page
        $this->assertGreaterThanOrEqual(1, count($posts));
    }
}