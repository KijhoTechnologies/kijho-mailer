<?php

namespace Kijho\MailerBundle\Entity;

use Kijho\MailerBundle\Model\EmailEvent as BaseEmailEvent;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email Event
 * @ORM\Table(name="email_event")
 * @ORM\Entity
 */
class EmailEvent extends BaseEmailEvent {

    /**
     * @ORM\Id
     * @ORM\Column(name="emev_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    function getId() {
        return $this->id;
    }  

}
