<?php

namespace Kijho\MailerBundle\Entity;

use Kijho\MailerBundle\Model\Settings as BaseSettings;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email Settings
 * @ORM\Table(name="email_settings")
 * @ORM\Entity
 */
class EmailSettings extends BaseSettings {

    /**
     * @ORM\Id
     * @ORM\Column(name="sett_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    function getId() {
        return $this->id;
    }

}
