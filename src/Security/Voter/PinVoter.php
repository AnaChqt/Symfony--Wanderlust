<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PinVoter extends Voter
{
    public const PIN_MANAGE = 'PIN_MANAGE';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::PIN_MANAGE && $subject instanceof \App\Entity\Pin;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::PIN_MANAGE:
                return $user->isVerified() && $user == $subject->getUser();
        }

        return false;
    }
}
