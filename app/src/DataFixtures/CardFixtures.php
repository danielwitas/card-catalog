<?php

namespace App\DataFixtures;

use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CardFixtures extends Fixture implements FixtureGroupInterface
{


    public function load(ObjectManager $manager)
    {
        $cards = [
            [
                'name' => 'Geralt',
                'power' => 10,
            ],
            [
                'name' => 'Ciri',
                'power' => 9,
            ],
            [
                'name' => 'Vessemir',
                'power' => 5,
            ],
            [
                'name' => 'Triss',
                'power' => 3,
            ],
            [
                'name' => 'Aard sign',
                'power' => 0,
            ],

        ];

        foreach ($cards as $card) {
            $exists = $manager->getRepository(Card::class)->findOneBy([
                'name' => $card['name'],
                'power' => $card['power']
            ]);
            if (!$exists) {
                $newCard = new Card();
                $newCard->setName($card['name'])->setPower($card['power']);
                $manager->persist($newCard);
            }
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['card_fixtures'];
    }

}