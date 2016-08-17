<?php

namespace Kijho\MailerBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Kijho\MailerBundle\Model\Email;
use Kijho\MailerBundle\Model\Template;
use Kijho\MailerBundle\Model\Settings;
use Kijho\MailerBundle\Util\Util;

/*
 * EmailManager
 * Esta clase implementa metodos generalizados para la construccion y 
 * envio de correos electronicos en la aplicacion, los cuales pueden ser utilizados
 * como un servicio
 */

class EmailManager {

    protected $mailer;
    protected $request;
    protected $container;
    protected $em;

    public function __construct(RequestStack $requestStack, ContainerInterface $container, $em) {
        $this->request = $requestStack->getCurrentRequest();
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * Esta funcion permite realizar el envio del un correo electronico
     * @author Cesar Giraldo <cnaranjo@kijho.com> 23/11/2015
     * @param Email $email instancia del correo electronico a enviar
     */
    public function send(Email $email, $dataToTemplate = array(), $userId = null) {

        //verificamos que mailer se debe usar para el envio del correo
        $mailer = 'swiftmailer.mailer';
        $this->mailer = $this->container->get($mailer);

        $template = $email->getTemplate();
        if (!empty($template->getMailerSettings()) && $template->getMailerSettings() != 'default') {

            $mailer = 'swiftmailer.mailer.' . $template->getMailerSettings();
            $parameterName = $mailer . ".transport.name";
            if ($this->container->hasParameter($parameterName)) {
                $this->mailer = $this->container->get($mailer);
            }
        } else {
            $mailer = 'swiftmailer.mailer.default';
        }

        //instanciamos el mensaje (asunto, destinatario, contenido, etc)
        $subject = $email->getSubject();
        $recipientName = $email->getRecipientName();
        $recipientEmail = (array) json_decode($email->getMailTo());

        if (!$this->mailer->getTransport()->isStarted()) {
            $this->mailer->getTransport()->start();
        }

        $message = $this->mailer->createMessage();
        $message->setSubject($subject);

        if ($email->getMailCopyTo()) {
            $message->setBcc($email->getMailCopyTo());
        }

        $html = $this->renderEmail($email, $dataToTemplate);
        $message->setBody($html, 'text/html', 'UTF8');
        $email->setContent($html);
        
        
        //seteamos el o los destinatarios a quin va dirigido el correo
        if (!is_array($recipientEmail)) {
            $message->addTo($recipientEmail, $recipientName);
        } else {
            foreach ($recipientEmail as $recipient) {
                $message->addTo($recipient, $recipientName);
            }
        }

        //verificamos si el template tiene un email from, de lo contrario ponemos el del mailer
        if (!empty($email->getMailFrom())) {
            $message->setFrom(array($email->getMailFrom() => $email->getFromName()));
        } elseif ($this->container->hasParameter($mailer . '.transport.smtp.username')) {
            $message->setFrom(array($this->container->getParameter($mailer . '.transport.smtp.username') => $email->getFromName()));
        }

        //enviamos el correo
        $this->mailer->send($message);
        $this->mailer->getTransport()->stop();

        //marcamos el correo como enviado
        $email->setStatus(Email::STATUS_SENT);
        $email->setSentDate(Util::getCurrentDate());

        if($userId){
         $email->setUserId($userId);
        }

        $this->em->persist($email);
        $this->em->flush();
    }

    /**
     * Esta funcion permite verificar si en una direccion de correo vienen
     * varios correos en linea, y permite construir el arreglo respectivo con
     * la lista de correos, si solo es uno, retorna el mismo correo
     * @author Cesar Giraldo <cnaranjo@kijho.com> 23/11/2015
     * @param string $emailText correo o correos a los que se quiere enviar
     * @return type emails filtrados, pueder ser un string o un arreglo de strings
     */
    private function buildArrayEmails($emailText) {

        $pos = strpos($emailText, ',');

        //buscamos si hay varios correos
        if ($pos !== false) {

            $emails = explode(',', $emailText);

            $emailText = array();
            foreach ($emails as $email) {
                if (!empty($email)) {
                    $emailText[trim($email)] = trim($email);
                }
            }
        }
        return $emailText;
    }

    /**
     * Esta funcion permite inicializar los datos de un correo electronico
     * @author Cesar Giraldo <cnaranjo@kijho.com> 23/11/2015
     * @param Template $template instancia del template medianete el cual se envia el correo
     * @param string $emailAddress direcion(es) de correo electronico
     * @return \Kijho\MailerBundle\Model\Email
     */
    public function composeEmail(Template $template, $emailAddress) {
        $emailStorage = $this->container->getParameter('kijho_mailer.storage')['email'];
        $email = new $emailStorage;
        $email->setTemplate($template);
        $email->setContent($template->getContentMessage());
        $email->setFromName($template->getFromName());
        $email->setGeneratedDate(Util::getCurrentDate());
        $email->setMailCopyTo($template->getCopyTo());
        $email->setMailFrom($template->getFromMail());

        //filtramos la direccion de correo para saber si vienen varias direcciones en una
        $emailAddress = $this->buildArrayEmails($emailAddress);
        $email->setMailTo(json_encode($emailAddress));

        $email->setStatus(Email::STATUS_PENDING);
        $email->setSubject($template->getSubject());

        return $email;
    }

    /**
     * Esta funcion permite obtener los mailers de la aplicacion
     * @author Cesar Giraldo <cnaranjo@kijho.com> 24/11/2015
     * @return array[string]
     */
    public function getMailers() {
        $mailers = array();
        if ($this->container->hasParameter('swiftmailer.mailers')) {
            $mailers = $this->container->getParameter('swiftmailer.mailers');
        }
        return $mailers;
    }

    /**
     * Esta funcion permite analizar la bolsa de parametros del swiftmailer
     * y obtener los valores de dichos parametros
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 19/11/2015
     * @return array[string] arreglo con los parametros del swiftmailer
     */
    public function getSwiftMailerSettings() {

        $bag = $this->container->getParameterBag();

        $basicParameters = array(
            "transport.name",
            "delivery.enabled",
            "transport.smtp.encryption",
            "transport.smtp.port",
            "transport.smtp.host",
            "transport.smtp.username",
            "transport.smtp.password",
            "transport.smtp.auth_mode",
            "transport.smtp.timeout",
            "transport.smtp.source_ip",
            "memory.path",
            "spool.enabled",
            "plugin.impersonate",
            "single_address");

        $mailers = $this->getMailers();

        //aca recorremos los mailers y armamos el arreglo reemplazando el nombre del mailer
        $swiftParameters = array();
        foreach ($mailers as $mailer) {
            foreach ($basicParameters as $parameter) {
                array_push($swiftParameters, $mailer . "." . $parameter);
            }
        }

        $otherParameters = array(
            "swiftmailer.spool.enabled",
            "swiftmailer.delivery.enabled",
            "swiftmailer.single_address",
            "swiftmailer.mailers",
            "swiftmailer.default_mailer");

        $swiftParameters = array_merge($swiftParameters, $otherParameters);

        $mailerSettings = array();
        foreach ($swiftParameters as $swiftParameter) {
            if ($bag->has($swiftParameter)) {
                $mailerSettings[$swiftParameter] = $bag->get($swiftParameter);
            }
        }

        return $mailerSettings;
    }

    /**
     * Esta funcion permite obtener la instancia con las configuraciones
     * generales del envio de correos, si no se han creado, las crea atomaticamente
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     * @return Settings objeto con las configuraciones del modulo de correos
     */
    public function getMailerSettings() {
        $settingsStorage = $this->container->getParameter('kijho_mailer.storage')['settings'];

        $settings = $this->em->getRepository($settingsStorage)->findAll();
        if (!$settings) {
            $settings = new $settingsStorage;
            $settings->setSendMode(Settings::SEND_MODE_INSTANTANEOUS);
            $settings->setLimitEmailAmount(Settings::DEFAULT_EMAIL_AMOUNT);
            $settings->setIntervalToSend(Settings::DEFAULT_TIME_SEND);
            $this->em->persist($settings);
            $this->em->flush();
        } else {
            $settings = $settings[0];
        }
        return $settings;
    }

    /**
     * Permite establecer si el envio de correos debe ser de manera instantanea o no
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     * @return boolean indicando si el envio debe ser instantaneo o no
     */
    public function isSendInstantaneous() {
        $settings = $this->getMailerSettings();

        if ($settings->getSendMode() == Settings::SEND_MODE_INSTANTANEOUS) {
            return true;
        }
        return false;
    }

    /**
     * Esta funcion permite verificar el metodo de envio de correos (numero limite)
     * para realizar el envio si ya se cumplio el tope establecido en las
     * configuraciones del proyecto
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     */
    public function verifyPendingEmails() {
        $settings = $this->getMailerSettings();

        if ($settings->getSendMode() == Settings::SEND_MODE_BY_EMAIL_AMOUNT) {
            $emailAmount = $settings->getLimitEmailAmount();

            $emailStorage = $this->container->getParameter('kijho_mailer.storage')['email'];

            $search = array('status' => Email::STATUS_PENDING);
            $order = array('generatedDate' => 'ASC');
            $pendingEmails = $this->em->getRepository($emailStorage)->findBy($search, $order);

            if (count($pendingEmails) >= $emailAmount) {
                foreach ($pendingEmails as $email) {
                    $this->send($email);
                }
            }
        }
    }

    /**
     * Esta funcion permite consultar cualquier evento de emails indicando
     * su identificador (slug) unico
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 27/11/2015
     * @param string $slug
     * @return \Kijho\MailerBundle\Model\EmailEvent instancia del evento de email
     */
    public function getEmailEvent($slug) {
        $emailEventStorage = $this->container->getParameter('kijho_mailer.storage')['email_event'];
        $emailEvent = $this->em->getRepository($emailEventStorage)->findOneBySlug($slug);
        return $emailEvent;
    }

    /**
     * Esta funcion permite consultar el listado de templates de forma general
     * o de acuerdo a un grupo especifico
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 3/12/2015
     * @param string $groupSlug
     * @param string $templateSlug
     * @param string $entityName
     * @return array[Template] listado de templates que coinciden con la busqueda
     */
    public function getTemplates($groupSlug = null, $templateSlug = null, $entityName = null, $languageCode = null) {
        $search = array();
        if ($groupSlug) {
            $groupStorage = $this->container->getParameter('kijho_mailer.storage')['template_group'];
            $group = $this->em->getRepository($groupStorage)->findOneBySlug($groupSlug);
            if ($group) {
                $search['group'] = $group->getId();
            }
        }

        if ($languageCode) {
            $search['languageCode'] = $languageCode;
        }

        if ($entityName) {
            $search['entityName'] = $entityName;
        }
        if ($templateSlug) {
            $search['slug'] = $templateSlug;
        }
        $order = array('group' => 'ASC', 'name' => 'ASC');

        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
        $templates = $this->em->getRepository($templateStorage)->findBy($search, $order);
        return $templates;
    }

    /**
     * Esta funcion permite consultar un template que coincida con un slug 
     * asignado y con el idioma respectivo
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 06/01/2016
     * @param string $slug
     * @param string $languageCode
     * @return array[Template] template que coincide con la busqueda
     */
    public function getTemplate($slug, $languageCode = null) {

        if (!$languageCode) {
            $languageCode = $this->request->getLocale();
        }

        $search = array('slug' => $slug, 'languageCode' => $languageCode);
        $order = array('languageCode' => 'ASC', 'name' => 'ASC');
        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
        $templates = $this->em->getRepository($templateStorage)->findBy($search, $order);

        if (!empty($templates)) {
            return $templates[0];
        }
        return null;
    }

    /**
     * Esta funcion permite obtener el html del correo electronico que sera enviado
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 27/11/2015
     * @param Email $email instancia del correo que sera enviado
     * @param array[mixed] $dataToTemplate datos solicitados por el correo
     * @return string texto con el html del correo
     */
    public function renderEmail($email, $dataToTemplate) {

        $entity = null;
        if ($email->getTemplate()) {
            $entityName = $email->getTemplate()->getEntityName();
            //validamos que el correo este recibiendo la instancia correcta en entity
            if ($entityName != '') {
                $messageException = $this->container->get('translator')->trans('kijho_mailer.email.error_instance_message_1') . $entityName;
                if (isset($dataToTemplate['entity'])) {

                    $instance = new $entityName;
                    if ($dataToTemplate['entity'] instanceof $instance) {
                        $entity = $dataToTemplate['entity'];
                    } else {
                        throw new \ErrorException($messageException);
                    }
                } else {
                    throw new \ErrorException($messageException);
                }
            }
        }

        $dataToTemplate['kijhoEmail'] = $email;

        $nullVars = $this->filterTwigVars($email, $dataToTemplate);
        $dataToTemplate['nullVars'] = $nullVars;

        $html = $this->container->get('templating')->render('KijhoMailerBundle:Email:emailView.html.twig', $dataToTemplate);

        return $html;
    }

    /**
     * Esta funcion permite conocer las variables que se encuentran en el contenido
     * de los correos electronicos, para luego validar si el usuario proporciona 
     * o no cada una de las variables a la hora de enviar el correo
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 30/11/2015
     * @param Email $email
     * @param array[mixed] $dataToTemplate
     * @return array[string] arreglo con las variables que faltan por enviar al twig
     */
    private function filterTwigVars(Email $email, $dataToTemplate) {
        $content = $email->getContent();
        $lastPos = strpos($content, '}}');

        $nullVars = array();
        if ($lastPos > 0) {
            $var = trim(Util::getBetween($content, '{{', '}}'));
            if (!$this->isParameterInData($var, $dataToTemplate)) {
                array_push($nullVars, $var);
            }

            $noMoreParameters = false;
            while (!$noMoreParameters) {
                $content = substr($content, $lastPos + 2);
                $lastPos = strpos($content, '}}');
                if ($lastPos > 0) {
                    $var = trim(Util::getBetween($content, '{{', '}}'));
                    if (!$this->isParameterInData($var, $dataToTemplate)) {
                        array_push($nullVars, $var);
                    }
                } else {
                    $noMoreParameters = true;
                }
            }
        }

        return $nullVars;
    }

    /**
     * Esta funcion permite validar si una variable se encuentra declarada
     * en un conjunto de datos
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 30/11/2015
     * @param string $var nombre de la variable
     * @param array[mixed] $dataToTemplate arreglod e datos
     * @return boolean
     */
    private function isParameterInData($var, $dataToTemplate) {
        if (isset($dataToTemplate[$var])) {
            return true;
        } else {

            $items = explode('.', $var);
            $countItems = count($items);

            if ($countItems > 1 && isset($dataToTemplate[$items[0]])) {

                return true;
            }
        }
        return false;
    }

}
