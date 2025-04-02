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
    public function createUsers(): void
    {
        // create Admin
        $admin = new User;
        $admin->setEmail('kidsbulle31@gmail.com')
            ->setPassword($this->userPasswordHasherInterface->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstname('Administrateur')
            ->setLastname('Kidsbulle')
            ->setPhone('0987654321')
            ->setFullAdress('123 rue du soleil 04567 Astre');

        $this->createChildren($admin);

        $this->entityManager->persist($admin);

        // create Teacher
        for ($i = 0; $i < 3; $i++) {
            $user = new User;
            $user->setEmail('teacher' . $i . '@kidsbulle.fr')
                ->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'teacher'))
                ->setRoles(['ROLE_TEACHER'])
                ->setFirstname('exemple' . $i)
                ->setLastname('teacher' . $i)
                ->setPhone('0987654321')
                ->setFullAdress('123 rue du soleil 04567 Astre');;

            $this->createChildren($user);

            $this->entityManager->persist($user);
        }

        // create User
        for ($i = 0; $i < 10; $i++) {
            $user = new User;
            $user->setEmail('user' . $i . '@kidsbulle.fr')
                ->setPassword($this->userPasswordHasherInterface->hashPassword($user, 'user'))
                ->setFirstname('exemple' . $i)
                ->setLastname('user' . $i)
                ->setPhone('0987654321')
                ->setFullAdress('123 rue du soleil 04567 Astre');;

            $this->createChildren($user);

            $this->entityManager->persist($user);
        }

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
