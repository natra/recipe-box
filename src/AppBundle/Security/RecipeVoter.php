<?php

namespace AppBundle\Security;

use AppBundle\Entity\Recipe;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class RecipeVoter extends Voter
{
    // these strings are just invented: you can use anything
    
    const EDIT = 'edit';
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Recipe) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Recipe $recipe */
        $recipe = $subject;

        switch($attribute) {
            case self::EDIT:
                return $this->canEdit($recipe, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    

    private function canEdit(Recipe $recipe, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $recipe->getUser();
    }
}

?>