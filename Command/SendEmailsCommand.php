<?php

namespace Kijho\MailerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Kijho\MailerBundle\Model\Email;

/**
 * Comando encargado de verificar si hay correos electronicos en cola 
 * para realizar el envio de los mismos
 * @author Cesar Giraldo <cnaranjo@kijho.com> 24/09/2015
 */
class SendEmailsCommand extends ContainerAwareCommand {

    protected function configure() {

        $this->setName('kijho-mailer:send-emails')->setDescription('Comando encargado de '
                . 'verificar si hay correos electronicos en cola para realizar el envio de los mismos');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $container = $this->getContainer();

        $output->writeln("Revisando lista de correos ");

        $em = $container->get('doctrine')->getManager();
        $emailStorage = $container->getParameter('kijho_mailer.storage')['email'];

        $search = array('status' => Email::STATUS_PENDING);
        $order = array('generatedDate' => 'ASC');

        $pendingEmails = $em->getRepository($emailStorage)->findBy($search, $order);

        $output->writeln("Se encontraron " . count($pendingEmails) . " correos sin enviar");

        $i = 0;
        foreach ($pendingEmails as $email) {
            try {
                $container->get('email_manager')->send($email);
                $i++;
                $output->writeln("Se ha enviado un correo a " . $email->getTextMailTo());
            } catch (\Exception $exc) {
                $output->writeln("Error enviando uno de los correos...");
            }

            $output->writeln("Proceso finalizado, se han enviado " . $i . " correos con exito");
        }
    }

}
