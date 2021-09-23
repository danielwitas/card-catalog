<?php

namespace App\Tests;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CardTest extends WebTestCase
{
    use ReloadDatabaseTrait;

    /**
     * @throws \JsonException
     */
    public function testAddCardWithEmptyJson(): void
    {
        $client = self::createClient();
        $client->request('POST', 'api/cards', [
            'json' => [],
        ]);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'errors' => 'Request body is empty.',
            'status' => 400,
            'type' => 'about:blank',
            'title' => 'Bad Request',
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws \JsonException
     */
    public function testAddCardWithMissingPower(): void
    {
        $client = self::createClient();
        $data = $this->arrayToJson([
            'name' => 'Geralt'
        ]);
        $client->request('POST', 'api/cards', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'errors' => [
                [
                    'power' => 'This value should not be blank.'
                ]
            ],
            'status' => 400,
            'type' => 'validation_error',
            'title' => 'There was a validation error.',
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws \JsonException
     */
    public function testAddCardWithMissingName(): void
    {
        $client = self::createClient();
        $data = $this->arrayToJson([
            'power' => 1
        ]);
        $client->request('POST', 'api/cards', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'errors' => [
                [
                    'name' => 'This value should not be blank.'
                ]
            ],
            'status' => 400,
            'type' => 'validation_error',
            'title' => 'There was a validation error.',
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws \JsonException
     */
    public function testAddCard(): void
    {
        $client = self::createClient();
        $data = $this->arrayToJson([
            'name' => 'Geralt',
            'power' => 1
        ]);
        $client->request('POST', 'api/cards', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'id' => 1,
            'name' => 'Geralt',
            'power' => 1,
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(201);
    }

    /**
     * @throws \JsonException
     */
    public function testAddCardWithNameAlreadyTaken(): void
    {
        $client = self::createClient();
        $this->createCard();
        $data = $this->arrayToJson([
            'name' => 'Geralt',
            'power' => 1
        ]);
        $client->request('POST', 'api/cards', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'errors' => [
                [
                    'name' => 'This value is already used.'
                ]
            ],
            'status' => 400,
            'type' => 'validation_error',
            'title' => 'There was a validation error.',
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws \JsonException
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
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $this->assertCount(3, $body['items']);
        self::assertResponseStatusCodeSame(200);
    }


    /**
     * @throws \JsonException
     */
    public function testGetNonExistingCard(): void
    {
        $client = self::createClient();
        $client->request('GET', 'api/cards/1');
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'detail' => 'Cannot find card with id 1',
            'status' => 404,
            'type' => 'about:blank',
            'title' => 'Not Found',
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws \JsonException
     */
    public function testGetExistingCard(): void
    {
        $client = self::createClient();
        $this->createCard();
        $client->request('GET', 'api/cards/1');
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'id' => 1,
            'name' => 'Geralt',
            'power' => 1,
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @throws \JsonException
     */
    public function testDeleteNonExistingCard(): void
    {
        $client = self::createClient();
        $client->request('DELETE', 'api/cards/1');
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'detail' => 'Cannot find card with id 1',
            'status' => 404,
            'type' => 'about:blank',
            'title' => 'Not Found',
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(404);
    }

    public function testDeleteExistingCard(): void
    {
        $client = self::createClient();
        $this->createCard();
        $client->request('DELETE', 'api/cards/1');
        $body = $client->getResponse()->getContent();
        $expected = '';
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(204);
    }

    /**
     * @throws \JsonException
     */
    public function testUpdateNonExistingCardName(): void
    {
        $client = self::createClient();
        $data = $this->arrayToJson([
            'name' => 'Geralt',
        ]);
        $client->request('PUT', 'api/cards/1', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'detail' => 'Cannot find card with id 1',
            'status' => 404,
            'type' => 'about:blank',
            'title' => 'Not Found',
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws \JsonException
     */
    public function testUpdateNonExistingCardPower(): void
    {
        $client = self::createClient();
        $data = $this->arrayToJson([
            'power' => 2,
        ]);
        $client->request('PUT', 'api/cards/1', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'detail' => 'Cannot find card with id 1',
            'status' => 404,
            'type' => 'about:blank',
            'title' => 'Not Found',
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @throws \JsonException
     */
    public function testUpdateExistingCardName(): void
    {
        $client = self::createClient();
        $this->createCard();
        $data = $this->arrayToJson([
            'name' => 'Ciri',
        ]);
        $client->request('PUT', 'api/cards/1', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'id' => 1,
            'name' => 'Ciri',
            'power' => 1,
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(200);
    }

    public function testUpdateExistingCardPower(): void
    {
        $client = self::createClient();
        $this->createCard();
        $data = $this->arrayToJson([
            'power' => 2,
        ]);
        $client->request('PUT', 'api/cards/1', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            'id' => 1,
            'name' => 'Geralt',
            'power' => 2,
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @throws \JsonException
     */
    public function testUpdateCardNameToAlreadyExistingCardName(): void
    {
        $client = self::createClient();
        $this->createCard();
        $this->createCard('Ciri', 2);
        $data = $this->arrayToJson([
            'name' => 'Geralt',
        ]);
        $client->request('PUT', 'api/cards/2', [], [], [], $data);
        $body = $this->jsonToArray($client->getResponse()->getContent());
        $expected = [
            "errors" => [
                [
                    "name" => "This value is already used."
                ]
            ],
            "status" => 400,
            "type" => "validation_error",
            "title" => "There was a validation error.",
        ];
        self::assertEqualsCanonicalizing($body, $expected);
        self::assertResponseStatusCodeSame(400);
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

    /**
     * @throws \JsonException
     */
    public function jsonToArray($json)
    {
        return json_decode(
            $json,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws \JsonException
     */
    public function arrayToJson(array $array)
    {
        return json_encode($array, JSON_THROW_ON_ERROR);
    }
}

