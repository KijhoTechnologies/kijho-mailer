<?php

namespace Kijho\MailerBundle\Entity;

use Kijho\MailerBundle\Model\Layout as BaseLayout;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email Layout
 * @ORM\Table(name="email_layout")
 * @ORM\Entity
 */
class EmailLayout extends BaseLayout {

    /**
     * @ORM\Id
     * @ORM\Column(name="layo_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    function getId() {
        return $this->id;
    }

}
