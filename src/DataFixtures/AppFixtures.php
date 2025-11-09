<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Entity\InfoUser;

use App\Entity\Chevaux;
use App\Entity\Itineraires;

use App\Entity\Messagerie\Discussions;
use App\Entity\Messagerie\Messages;

use DateTime;
use DateTimeInterface;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Time;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

        public function __construct(UserPasswordHasherInterface $hasher)
        {
            $this->hasher = $hasher;        
        }

        public function load(ObjectManager $manager): void
        {
            $faker = Faker\Factory::create('fr_FR');

            $package = new Package(new EmptyVersionStrategy());
            $imgArray = [$package->getUrl('assets/img/thmb-user-1.jpg'),$package->getUrl('assets/img/thmb-user-2.jpg'),$package->getUrl('assets/img/thmb-user-3.jpg'),$package->getUrl('assets/img/thmb-user-4.jpg')];
            $acceptesArr = ['Jument', 'Hongre', 'Entier'];
            $alluresArr = ['Pas', 'Trot', 'Galop'];
            $niveauArr = ['Facile', 'Intermediaire', 'Difficile'];


            $admin = new Utilisateur();
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setUsername('admin');
            $pwd = $this->hasher->hashPassword($admin, 'mdp123');
            $admin->setPassword($pwd);
            $manager->persist($admin);

            for ($i = 0; $i < 20; $i++) {
                $utilisateur = new Utilisateur();
                $utilisateur->setUsername($faker->userName());
                $password = $this->hasher->hashPassword($utilisateur, 'mdp123');
                $utilisateur->setPassword($password);                

                $info_user = new InfoUser();
                $info_user->setUserId($utilisateur);
                $info_user->setNom($faker->firstName());
                $info_user->setPrenom($faker->lastName());
                $info_user->setVille($faker->city());
                $info_user->setRegion($faker->country());
                $info_user->setMiniature($faker->randomElement($imgArray));

                $all_user = [$utilisateur];

                $chevaux = new Chevaux();
                $chevaux->setNom($faker->firstName());
                $chevaux->setProprietaire($utilisateur);
                $chevaux->setRace($faker->word());
                $chevaux->setSexe($faker->randomElement(['Jument', 'Hongre', 'Entier']));

                $itineraires = new Itineraires();
                $itineraires->addUtilisateur($utilisateur);
                $itineraires->setCreateur($utilisateur);
                $itineraires->setTitre($faker->sentence());
                $itineraires->setValidation($faker->randomElement([true, false]));
                $itineraires->setPublie($faker->randomElement([true, false]));
                $itineraires->setNiveau($faker->randomElements(new \ArrayIterator($niveauArr)));
                $itineraires->setAllures($faker->randomElements($alluresArr));
                $itineraires->setDepart($faker->city());
                $itineraires->setDistance($faker->numberBetween(10, 100));
                $itineraires->setDuree($faker->numberBetween(0, 100));
                $itineraires->setAccepte($faker->randomElements($acceptesArr));
                $itineraires->setDescription($faker->text());
                
                $manager->persist($utilisateur);
                $manager->persist($chevaux);
                $manager->persist($info_user);
                $manager->persist($itineraires);
            }

            $manager->flush();
            $this->setReference('user_id', $utilisateur);
        }

}
