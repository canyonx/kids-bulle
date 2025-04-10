<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Child;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures
{
    private $faker;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasherInterface,
    ) {
        $this->faker = Factory::create('fr_BE');
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
                ->setFirstname($this->faker->firstName())
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
                ->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName())
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
        for ($i = 0; $i < rand(1, 4); $i++) {
            $child = new Child;
            $child->setFirstname($this->faker->firstName())
                ->setLastname($user->getLastname())
                ->setBirthAt(new \DateTimeImmutable($this->faker->dateTimeThisDecade()->format('y-M-d')))
                ->setUser($user);

            $this->entityManager->persist($child);
        }
    }
}
