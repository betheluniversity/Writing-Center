<?php

namespace Bethel\TutorLabsAPIBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Bethel\UserBundle\Entity\User;
use BeSimple\SsoAuthBundle\Security\Core\User\UserFactoryInterface;

class UserProvider implements UserProviderInterface, UserFactoryInterface {
    public function loadUserByUsername($username) {
        // make a call to your webservice here
        $userData = '...';
        // pretend it returns an array on success, false if there is no user

        if ($userData) {

            // ...

            return new User($username, $password, $salt, $roles);
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    /**
     * Creates a new user for the given username
     *
     * @param string $username The username
     * @param array $roles Roles assigned to user
     * @param array $attributes Attributes provided by SSO server
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function createUser($username, array $roles, array $attributes) {

        return $this;
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'Bethel\UserBundle\User';
    }

    public function getUsernameForApiKey($apiKey) {
        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        if($apiKey == $this->bethelApiKey) {
            $username = 'apiuser';
        } else {
            $username = null;
        }

        return $username;
    }
}