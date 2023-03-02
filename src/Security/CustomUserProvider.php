<?php
namespace App\Security;

use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomUserProvider extends EntityUserProvider
{
    public function loadUserByUsername($username)
    {
        // Retrieve the user from the database based on the provided username
        $user = $this->repository->findOneBy(['email' => $username]);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Email "%s" does not exist.', $username));
        }

        // Add the user's roles to the user object
        $roles = $user->getRoles();
        $user->setRoles($roles);

        return $user;
    }
}

