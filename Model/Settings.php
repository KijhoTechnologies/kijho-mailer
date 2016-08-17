<?php
namespace Kijho\MailerBundle\Model;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Settings implements SettingsInterface {

    /**
     * Constantes para diferenciar el comportamiento de envio de correos electronicos
     */
    const SEND_MODE_INSTANTANEOUS = 1;
    const SEND_MODE_PERIODIC = 2;
    const SEND_MODE_BY_EMAIL_AMOUNT = 3;
    
    /**
     * Constante para definir el numero de correos limite para enviar correos
     */
    const DEFAULT_EMAIL_AMOUNT = 5;
    
    /**
     * Constante para definir por defecto el tiempo periodico de envio de correos
     */
    const DEFAULT_TIME_SEND = 5;
    
    /**
     * @var integer
     * @ORM\Column(name="sett_send_mode", type="integer")
     * @Assert\NotBlank()
     */
    protected $sendMode;
    
    /**
     * @var integer
     * @ORM\Column(name="sett_limit_email_amount", type="integer", nullable=true)
     */
    protected $limitEmailAmount;
    
    /**
     * Intervalo en minutos para el envio de correos electronicos mediante un cron
     * @var integer
     * @ORM\Column(name="sett_interval_to_send", type="integer", nullable=true)
     */
    protected $intervalToSend;
    
    public function __toString() {
        return $this->getSendModeDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function getIntervalToSend() {
        return $this->intervalToSend;
    }

    /**
     * {@inheritDoc}
     */
    public function getLimitEmailAmount() {
        return $this->limitEmailAmount;
    }

    /**
     * {@inheritDoc}
     */
    public function getSendMode() {
        return $this->sendMode;
    }

    /**
     * Permite obtener en modo texto la descripcion del envio de correos
     * @param integer $sendMode numero de modo de envio
     * @return string texto con el modo de envio
     */
    public function getSendModeDescription($sendMode = null) {
        if(!$sendMode){
            $sendMode = $this->getSendMode();
        }
        $text = '';
        switch ($sendMode) {
            case self::SEND_MODE_INSTANTANEOUS:
                $text = 'kijho_mailer.setting.send_mode_instantaneus';
                break;
            case self::SEND_MODE_PERIODIC:
                $text = 'kijho_mailer.setting.send_mode_periodic';
                break;
            case self::SEND_MODE_BY_EMAIL_AMOUNT:
                $text = 'kijho_mailer.setting.send_mode_email_amount';
                break;
            default:
                break;
        }
        return $text;
    }
    /**
     * @param integer $sendMode
     */
    public function setSendMode($sendMode) {
        $this->sendMode = $sendMode;
    }

    /**
     * @param integer $limitEmailAmount
     */
    public function setLimitEmailAmount($limitEmailAmount) {
        $this->limitEmailAmount = $limitEmailAmount;
    }

    /**
     * @param integer $intervalToSend
     */
    public function setIntervalToSend($intervalToSend) {
        $this->intervalToSend = $intervalToSend;
    }
    
    
}
