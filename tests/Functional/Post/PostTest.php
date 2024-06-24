<?php


namespace App\Tests\Fonctional\Post;


use App\Entity\Post; 
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase; 
use Symfony\Component\Routing\Generator\UrlGeneratorInterface; 


class PostTest extends WebTestCase 
{
    // Test pour vérifier que la page d'un post fonctionne correctement
    public function testPostPageWorks(): void
    {
        // Création d'un client HTTP pour simuler des requêtes
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router'); // Récupération du générateur d'URL

        /** @var EntityManagerInterface */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager'); // Récupération du gestionnaire d'entités

        // Récupération du repository de l'entité Post
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var Post */
        $post = $postRepository->findOneBy([]); // Récupération d'un post quelconque

        // Envoi d'une requête GET à l'URL générée pour afficher le post
        $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('post.show', ['slug' => $post->getSlug()])
        );

        // Vérification que la réponse est réussie (code 2xx)
        $this->assertResponseIsSuccessful();

        // Vérification que le code de statut de la réponse est HTTP 200 (OK)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérification qu'un élément <h1> existe dans la réponse HTML
        $this->assertSelectorExists('h1');

        // Vérification que le texte de l'élément <h1> contient la chaîne spécifiée
        $this->assertSelectorTextContains('h1', ucfirst($post->getTitle()));
    }

    // Test pour vérifier que le lien de retour au blog fonctionne correctement
    public function testToReturnToBlogWorks():void 
    {
        // Création d'un client HTTP pour simuler des requêtes
        $client = static::createClient();

        /** @var UrlGeneratorInterface */
        $urlGeneratorInterface = $client->getContainer()->get('router'); // Récupération du générateur d'URL

        /** @var EntityManagerInterface */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager'); // Récupération du gestionnaire d'entités

        // Récupération du repository de l'entité Post
        $postRepository = $entityManager->getRepository(Post::class);

        /** @var Post */
        $post = $postRepository->findOneBy([]); // Récupération d'un post quelconque

        // Envoi d'une requête GET à l'URL générée pour afficher le post et récupération du crawler
        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGeneratorInterface->generate('post.show', ['slug' => $post->getSlug()])
        );

        // Vérification que la réponse est réussie (code 2xx)
        $this->assertResponseIsSuccessful();
        // Vérification que le code de statut de la réponse est HTTP 200 (OK)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Sélection du lien avec le texte 'Retourner au blog' et extraction de son URL
        $link = $crawler = $crawler->selectLink('Retourner au blog')->link()->getUri();

        // Envoi d'une requête GET à l'URL du lien extrait
        $crawler = $client->request(Request::METHOD_GET, $link);

        // Vérification que la réponse est réussie (code 2xx)
        $this->assertResponseIsSuccessful();
        // Vérification que le code de statut de la réponse est HTTP 200 (OK)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérification que la route actuelle est 'post.index'
        $this->assertRouteSame('post.index');
    }
}
