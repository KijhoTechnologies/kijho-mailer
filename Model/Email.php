<?php

namespace Kijho\MailerBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Email implements EmailInterface {

    /**
     * Constantes para los posibles estados de un correo electronico
     */
    const STATUS_PENDING = 0;
    const STATUS_SENT = 1;
    const STATUS_FORWARDED = 2;

    /**
     * var \DateTime
     * @ORM\Column(name="emai_generated_date", type="datetime", nullable=true)
     */
    protected $generatedDate;

    /**
     * @var string
     * @ORM\Column(name="emai_mail_to", type="text")
     */
    protected $mailTo;

    /**
     * @var string
     * @ORM\Column(name="emai_copy_to", type="text", nullable=true)
     */
    protected $mailCopyTo;

    /**
     * @var string
     * @ORM\Column(name="emai_recipient_name", type="string", nullable=true)
     */
    protected $recipientName;

    /**
     * @var string
     * @ORM\Column(name="emai_from_name", type="string", nullable=true)
     */
    protected $fromName;

    /**
     * @var string
     * @ORM\Column(name="emai_mail_from", type="string", nullable=true)
     */
    protected $mailFrom;

    /**
     * @var string
     * @ORM\Column(name="emai_subject", type="string", nullable=true)
     */
    protected $subject;

    /**
     * @var string
     * @ORM\Column(name="emai_content", type="text", nullable=true)
     */
    protected $content;

    /**
     * @var \DateTime
     * @ORM\Column(name="emai_sent_date", type="datetime", nullable=true)
     */
    protected $sentDate;

    /**
     * @var integer
     * @ORM\Column(name="emai_status", type="integer", nullable=true)
     */
    protected $status;

    /**
     * @var string
     * @ORM\Column(name="emai_user_id", type="text", nullable=true)
     */
    protected $userId;

    /**
     * {@inheritDoc}
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     */
    public function getFromName() {
        return $this->fromName;
    }

    /**
     * {@inheritDoc}
     */
    public function getGeneratedDate() {
        return $this->generatedDate;
    }

    /**
     * {@inheritDoc}
     */
    public function getMailCopyTo() {
        return $this->mailCopyTo;
    }

    /**
     * {@inheritDoc}
     */
    public function getMailFrom() {
        return $this->mailFrom;
    }

    /**
     * {@inheritDoc}
     */
    public function getMailTo() {
        return $this->mailTo;
    }

    /**
     * {@inheritDoc}
     */
    public function getSentDate() {
        return $this->sentDate;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplate() {
        
    }

    /**
     * 
     * @return string
     */
    function getUserId() {
        return $this->userId;
    }

    /**
     * 
     * @param string $userId
     */
    function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * @param \DateTime $generatedDate
     */
    function setGeneratedDate(\DateTime $generatedDate) {
        $this->generatedDate = $generatedDate;
    }

    /**
     * @param string $mailTo
     */
    function setMailTo($mailTo) {
        $this->mailTo = $mailTo;
    }

    /**
     * @param string $mailCopyTo
     */
    function setMailCopyTo($mailCopyTo) {
        $this->mailCopyTo = $mailCopyTo;
    }

    /**
     * @param string $fromName
     */
    function setFromName($fromName) {
        $this->fromName = $fromName;
    }

    /**
     * @param string $mailFrom
     */
    function setMailFrom($mailFrom) {
        $this->mailFrom = $mailFrom;
    }

    /**
     * @param string $subject
     */
    function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * @param string $content
     */
    function setContent($content) {
        $this->content = $content;
    }

    /**
     * @param \DateTime $sentDate
     */
    function setSentDate(\DateTime $sentDate) {
        $this->sentDate = $sentDate;
    }

    /**
     * @param integer $status
     */
    function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @param string $recipientName
     */
    function setRecipientName($recipientName) {
        $this->recipientName = $recipientName;
    }

    /**
     * {@inheritDoc}
     */
    public function getRecipientName() {
        return $this->recipientName;
    }

    public function getTextMailTo() {
        $mails = (array) json_decode($this->mailTo);
        $text = "";
        foreach ($mails as $mail) {
            $text .= $mail . ", ";
        }
        return $text;
    }
    
    public function __toString() {
        return $this->subject;
    }
}
