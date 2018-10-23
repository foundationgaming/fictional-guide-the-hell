<?php

namespace quotemaker\services;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserProvider implements UserProviderInterface
{
    private $userDAO;

    public function __construct($dao)
    {
        $this->userDAO = $dao;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userDAO->loadUserByUsername($username);

        if ( !$user ) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return new User($user->username, $user->password, explode(',', $user->roles), true, true, true, true);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}