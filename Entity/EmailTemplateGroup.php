<?php

namespace Kijho\MailerBundle\Entity;

use Kijho\MailerBundle\Model\TemplateGroup as BaseTemplateGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email Template Group
 * @ORM\Table(name="email_template_group")
 * @ORM\Entity
 */
class EmailTemplateGroup extends BaseTemplateGroup {

    /**
     * @ORM\Id
     * @ORM\Column(name="tgro_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    function getId() {
        return $this->id;
    }

}
