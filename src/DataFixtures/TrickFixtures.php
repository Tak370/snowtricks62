<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /** @var array<array-key, Category> $categories */
        $categories = $manager->getRepository(Category::class)->findAll();

        /** @var array<array-key, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            foreach ($categories as $category) {
                for ($i = 1; $i <= 5; ++$i) {
                    $trick = new Trick();
                    $trick->setCategory($category);
                    $trick->setUser($user);
                    $trick->setCreatedAt(new DateTimeImmutable());
                    $trick->setName($faker->words(3, true));
                    $trick->setDescription($faker->paragraphs(2, true));
                    $trick->setSlug($faker->words(3, true));
                    $trick->setFeaturedText($faker->words(3, true));
                    $trick->setImage('jess-bailey-l3N9Q27zULw-unsplash.jpg');

                    $manager->persist($trick);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            UserFixtures::class
        ];
    }
}
