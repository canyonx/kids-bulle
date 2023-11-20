<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Child;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures
{
    private $userPasswordHasherInterface;
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ) {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
        $this->entityManager = $entityManagerInterface;
    }

    /**
     * Create users
     *
     * @return void
     */
    public function createUser(): void
    {
        // create Admin
        $admin = new User;
        $admin->setEmail('admin@oflix.fr')
            ->setPassword($this->userPasswordHasherInterface->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstname('exemple')
            ->setLastname('admin')
            ->setPhone('09876543')
            ->setFullAdress('123 rue du soleil 04567 Astre');

        $this->createChildren($admin);

        $this->entityManager->persist($admin);

        // create User
        $user = new User;
        $user->setEmail('user@oflix.fr')
            ->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'user'))
            ->setFirstname('exemple')
            ->setLastname('user')
            ->setPhone('09876543')
            ->setFullAdress('123 rue du soleil 04567 Astre');;

        $this->createChildren($user);

        $this->entityManager->persist($user);


        $this->entityManager->flush();
    }


    /**
     * Create Child
     *
     * @param User $user
     * @return void
     */
    public function createChildren(User $user): void
    {
        $faker = Factory::create('fr_BE');

        for ($i = 0; $i < rand(1, 4); $i++) {
            $child = new Child;
            $child->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setBirthAt(new \DateTimeImmutable($faker->dateTimeThisDecade()->format('y-M-d')))
                ->setUser($user);

            $this->entityManager->persist($child);
        }
    }
}
