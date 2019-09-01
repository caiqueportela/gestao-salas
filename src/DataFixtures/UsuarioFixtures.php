<?php

namespace App\DataFixtures;

use App\Entity\Usuarios;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UsuarioFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $usuario = new Usuarios();
        $usuario->setUsername('4linux');
        $usuario->setPassword('$argon2id$v=19$m=65536,t=4,p=1$j0FkjOliIWo8Ef+xhT0ghg$LEKJ95S9TdlquSo24+r+WZiv4FWLf0hl3Bo4qH0SOvI');

        $manager->persist($usuario);

        $manager->flush();
    }
}
