<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setPrenom('Tony');
        $user->setNom('Soprano');
        $user->setBio('Je suis le big boss');
        $user->setGithub('mlf-dev');
        $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $password = "123456789";
        // crypter le mot de passe et l'ajouter au User :
        $user->setPassword($this->userPasswordEncoder->encodePassword($user,$password));
        $user->setEmail('tony@gmail.com');
        $manager->persist($user);

        $user2 = new User();
        $user2->setPrenom('Paul');
        $user2->setNom('Le poulpe');
        $user2->setBio('J\'ai tout donné pour la coupe du monde et ensuite je suis mort');
        $user2->setGithub('faites-le');
        $user2->setRoles(['ROLE_USER']);
        $password2 = "123456789";
        // crypter le mot de passe et l'ajouter au User :
        $user2->setPassword($this->userPasswordEncoder->encodePassword($user2,$password2));
        $user2->setEmail('paul@gmail.com');
        $manager->persist($user2);


        $manager->flush();
    }
}
