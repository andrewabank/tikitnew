<?php


namespace Acme\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\ORM\Mapping as ORM;


class UserBundle extends Bundle
{
    public function getParent() {
        return 'FOSUserBundle';
    }
}