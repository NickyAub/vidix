<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Movie;
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
            $manager->persist($movie);
        }

        $manager->flush();
    }
}
