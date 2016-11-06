<?php

/*
 * User class for loggin-in funcionality
 */

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of User
 *
 * @author dciecior
 */
class User extends BaseUser{
    protected $id;
    
    public function __construct()
    {
        parent::__construct();
    }
}

?>
