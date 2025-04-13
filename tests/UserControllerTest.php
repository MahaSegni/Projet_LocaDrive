<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    public function testCreateAdmin()
    {
        // Créer un client pour envoyer la requête
        $client = self::createClient();

        // Simuler la requête GET à la route /create-admin
        $crawler = $client->request('GET', '/create-admin');

        // Vérifier que le code de statut est 200 OK
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifier que la réponse contient le message attendu
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Admin created', $responseData['message']);

        // Vérifier que l'admin a bien été créé dans la base de données
        $em = self::getContainer()->get('doctrine')->getManager();
        $admin = $em->getRepository(User::class)->findOneBy(['email' => 'admintest@example.com']);

        // Vérifier que l'admin existe
        $this->assertNotNull($admin);
        $this->assertEquals('admintest@example.com', $admin->getEmail());
        $this->assertContains('ROLE_ADMIN', $admin->getRoles());
    }
}
