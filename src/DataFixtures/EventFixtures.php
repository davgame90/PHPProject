<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $event1 = new Event();
        $event1->setTitle('GP Monaco');
        $event1->setDescription('Grand prix de Monaco, evement publique ouvert à tous');
        $event1->setDate(new \DateTime('+10 days'));
        $event1->setMaxParticipants(30);
        $event1->setIsPublic(true);
        $event1->setOwner($this->getReference('user_1'));
        $manager->persist($event1);

        $event2 = new Event();
        $event2->setTitle('GP Monza');
        $event2->setDescription('Grand prix de Monaco, evement privé');
        $event2->setDate(new \DateTime('+20 days'));
        $event2->setMaxParticipants(20);
        $event2->setIsPublic(false);
        $event2->setOwner($this->getReference('user_2'));
        $manager->persist($event2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}