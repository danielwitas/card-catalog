<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CardTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testAddCardWithEmptyJson(): void
    {
        $client = self::createClient();
        $client->request('POST', 'api/cards', [
            'json' => [],
        ]);
        self::assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be blank.',
                ],
                [
                    'propertyPath' => 'power',
                    'message' => 'This value should not be blank.',
                ]
            ]
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testAddCardWithMissingPower(): void
    {
        $client = self::createClient();
        $client->request('POST', 'api/cards', [
            'json' => [
                'name' => 'Geralt'
            ],
        ]);
        self::assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'power',
                    'message' => 'This value should not be blank.',
                ]
            ]
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testAddCardWithMissingName()
    {
        $client = self::createClient();
        $client->request('POST', 'api/cards', [
            'json' => [
                'power' => 1
            ],
        ]);
        self::assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be blank.',
                ]
            ]
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testAddCard()
    {
        $client = self::createClient();
        $client->request('POST', 'api/cards', [
            'json' => [
                'power' => 1,
                'name' => 'Geralt'
            ],
        ]);
        self::assertJsonContains([
            'id' => 1,
            'name' => 'Geralt',
            'power' => 1,
        ]);
        self::assertResponseStatusCodeSame(201);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testAddCardWithNameAlreadyTaken(): void
    {
        $client = self::createClient();
        $this->createCard();
        $client->request('POST', 'api/cards', [
            'json' => [
                'power' => 1,
                'name' => 'Geralt'
            ],
        ]);
        self::assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value is already used.',
                ]
            ]
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCollectionReturnsThreeElements(): void
    {
        $client = self::createClient();
        $em = $this->getEntityManager();
        for ($i = 0; $i <= 9; $i++) {
            $card = new Card();
            $card
                ->setName("test_name_$i")
                ->setPower($i);
            $em->persist($card);
        }
        $em->flush();
        $client->request('GET', 'api/cards');
        $content = $client->getResponse()->toArray();
        $this->assertCount(3, $content['hydra:member']);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetNonExistingCard(): void
    {
        $client = self::createClient();
        $client->request('GET', 'api/cards/1');
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetExistingCard(): void
    {
        $client = self::createClient();
        $this->createCard();
        $client->request('GET', 'api/cards/1');
        self::assertJsonContains([
            'id' => 1,
            'name' => 'Geralt',
            'power' => 1,
        ]);
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteNonExistingCard(): void
    {
        $client = self::createClient();
        $client->request('DELETE', 'api/cards/1');
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteExistingCard(): void
    {
        $client = self::createClient();
        $this->createCard();
        $client->request('DELETE', 'api/cards/1');
        self::assertResponseStatusCodeSame(204);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testUpdateNonExistingCardName(): void
    {
        $client = self::createClient();
        $client->request('PUT', 'api/cards/1', [
            'json' => [
                'name' => 'Ciri'
            ],
        ]);
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testUpdateNonExistingCardPower()
    {
        $client = self::createClient();
        $client->request('PUT', 'api/cards/1', [
            'json' => [
                'power' => 2
            ],
        ]);
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateExistingCardName(): void
    {
        $client = self::createClient();
        $this->createCard();
        $client->request('PUT', 'api/cards/1', [
            'json' => [
                'name' => 'Ciri'
            ],
        ]);
        self::assertJsonContains([
            'id' => 1,
            'name' => 'Ciri'
        ]);
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateExistingCardPower(): void
    {
        $client = self::createClient();
        $this->createCard();
        $client->request('PUT', 'api/cards/1', [
            'json' => [
                'power' => 2
            ],
        ]);
        self::assertJsonContains([
            'id' => 1,
            'power' => 2
        ]);
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateCardNameToAlreadyExistingCardName(): void
    {
        $client = self::createClient();
        $this->createCard();
        $this->createCard('Ciri', 2);
        $client->request('PUT', 'api/cards/2', [
            'json' => [
                'name' => 'Geralt'
            ],
        ]);
        self::assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value is already used.',
                ]
            ]
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    public function createCard(string $name = 'Geralt', int $power = 1): Card
    {
        $em = $this->getEntityManager();
        $card = new Card();
        $card
            ->setName($name)
            ->setPower($power);
        $em->persist($card);
        $em->flush();
        return $card;
    }


}