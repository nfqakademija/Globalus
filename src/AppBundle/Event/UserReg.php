<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.11
 * Time: 15.27
 */

namespace AppBundle\Event;
use AppBundle\Entity\User;

class UserReg
{
    private $user;
    private $comfirmation_token;

    /**
     * UserReg constructor.
     * @param $user
     * @param $comfirmation_token
     */
    public function __construct($user, $comfirmation_token)
    {
        $this->user = $user;
        $this->comfirmation_token = $comfirmation_token;
    }

    /**
     * @return User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserReg
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComfirmationToken()
    {
        return $this->comfirmation_token;
    }

    /**
     * @param mixed $comfirmation_token
     * @return UserReg
     */
    public function setComfirmationToken($comfirmation_token)
    {
        $this->comfirmation_token = $comfirmation_token;
        return $this;
    }

}
