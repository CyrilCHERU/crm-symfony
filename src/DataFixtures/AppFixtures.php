<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::Create("fr_FR");
        $faker->addProvider(new \Metrakit\EddyMalou\EddyMalouProvider($faker));
        $faker->addProvider(new \Metrakit\EddyMalou\TextProvider($faker));
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Avatar($faker));

        for ($c = 0; $c < 30; $c++) {
            $customer = new Customer;
            $customer->setAvatar($faker->avatar)
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setComment($faker->paragraph(2));

            $manager->persist($customer);

            for ($i = 0; $i < mt_rand(1, 5); $i++) {
                $invoice = new Invoice;
                $invoice->setCustomer($customer)
                    ->setTitle($faker->sentence())
                    ->setAmount($faker->price(250, 10000) * 100)
                    ->setSentAt($faker->dateTimeBetween("- 6 months"))
                    ->setStatus($faker->randomElement([Invoice::STATUS_PAID, Invoice::STAUT_SENT, Invoice::STATUS_CANCELLED]));

                $manager->persist($invoice);
            }
        }
        // $manager->persist($product);

        $manager->flush();
    }
}
