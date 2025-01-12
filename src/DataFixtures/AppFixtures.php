<?php

namespace App\DataFixtures;

use App\Entity\Character;
use App\Entity\Movie;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct() {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Create a set of fake movie data
        for ($i=1; $i <= 200; $i++) {
            $movie = new Movie();
            $movie->setTitle(ucwords($this->faker->words(mt_rand(1, 5), true)))
                ->setDescription($this->faker->text(500))
                ->setReleaseDate(new \DateTimeImmutable($this->faker->date('Y-m-d')));
         
            // Create a set of fake character data for movie
            for ($j=0; $j < mt_rand(1, 7); $j++) { 
                $character = new Character();
                $character->setName(ucwords($this->faker->words(mt_rand(1, 2), true)));

                $movie->addCharacter($character);

                $manager->persist($character);
            }
   
            $manager->persist($movie);
        }

        $manager->flush();
    }
}
