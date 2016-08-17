<?php
namespace Kijho\MailerBundle\Model;

/**
 */
interface EmailInterface
{
    /**
     * @return \DateTime
     */
    public function getGeneratedDate();
    
    /**
     * @return string
     */
    public function getRecipientName();
    
    /**
     * @return string
     */
    public function getMailTo();
    
    /**
     * @return string
     */
    public function getMailCopyTo();
    
    /**
     * @return string
     */
    public function getFromName();
    
    /**
     * @return string
     */
    public function getMailFrom();
    
    /**
     * @return string
     */
    public function getSubject();

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return \DateTime
     */
    public function getSentDate();

    /**
     * @return integer
     */
    public function getStatus();
    
    /**
     * @return Template
     */
    public function getTemplate();
}
