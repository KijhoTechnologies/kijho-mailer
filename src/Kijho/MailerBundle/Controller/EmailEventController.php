<?php

namespace Kijho\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\MailerBundle\Form\EmailEventType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmailEventController extends Controller {

    /**
     * Permite visualizar el listado de todos los eventos de emails creados en el sistema
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     * @return type
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $emailEventStorage = $this->container->getParameter('kijho_mailer.storage')['email_event'];

        $emailEvents = $em->getRepository($emailEventStorage)->findAll();

        return $this->render('KijhoMailerBundle:EmailEvent:index.html.twig', array(
                    'emailEvents' => $emailEvents,
                    'menu' => 'emailEvents'
        ));
    }

    /**
     * Permite desplegar el formulario de creacion de email events
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     * @return Response formulario de creacion del evento de correo
     */
    public function newAction() {

        $emailEventStorage = $this->container->getParameter('kijho_mailer.storage')['email_event'];
        $emailEvent = new $emailEventStorage;

        $form = $this->createForm(EmailEventType::class, $emailEvent, array(
            'translator' => $this->get('translator')
        ));

        return $this->render('KijhoMailerBundle:EmailEvent:new.html.twig', array(
                    'emailEvent' => $emailEvent,
                    'form' => $form->createView(),
                    'menu' => 'emailEvents'
        ));
    }

    /**
     * Permite validar y almacenar la informacion de un email event
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     * @param Request $request datos de la solicitud
     * @return Response en caso de exito redirecciona al listado de emailEvents, 
     * en caso de error despliega nuevamente el formulario de creacion de emailEvents
     */
    public function saveAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $emailEventStorage = $this->container->getParameter('kijho_mailer.storage')['email_event'];
        $emailEvent = new $emailEventStorage;

        $form = $this->createForm(EmailEventType::class, $emailEvent, array(
            'translator' => $this->get('translator')
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {

            $parameters = $request->request->get('kijho_mailerbundle_email_event_type');
            if (isset($parameters['template'])) {
                $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
                $template = $em->getRepository($templateStorage)->find($parameters['template']);
                if ($template) {
                    $emailEvent->setTemplateSlug($template->getSlug());
                }
            }

            $em->persist($emailEvent);
            $em->flush();

            //Seteamos el slug del evento
            $slug = trim(strtolower(str_replace(' ', '_', $emailEvent->getName()))) . "_" . $emailEvent->getId();
            $emailEvent->setSlug($slug);
            $em->persist($emailEvent);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageSuccessEmailEvent', $this->get('translator')->trans('kijho_mailer.email_event.creation_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_email_event'));
        }

        return $this->render('KijhoMailerBundle:EmailEvent:new.html.twig', array(
                    'emailEvent' => $emailEvent,
                    'form' => $form->createView(),
                    'menu' => 'emailEvents'
        ));
    }

    /**
     * Permite desplegar el formulario de edicion de emailEvents
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     * @param integer $emailEventId identificador del emailEvent a editar
     * @return Response formulario de edicion del emailEvent
     */
    public function editAction($emailEventId) {

        $em = $this->getDoctrine()->getManager();

        $emailEventStorage = $this->container->getParameter('kijho_mailer.storage')['email_event'];

        $emailEvent = $em->getRepository($emailEventStorage)->find($emailEventId);

        $form = $this->createForm(EmailEventType::class, $emailEvent, array(
            'translator' => $this->get('translator')
        ));

        return $this->render('KijhoMailerBundle:EmailEvent:edit.html.twig', array(
                    'emailEvent' => $emailEvent,
                    'form' => $form->createView(),
                    'menu' => 'emailEvents'
        ));
    }

    /**
     * Permite validar y almacenar los cambios en la informacion de un mail event
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     * @param Request $request datos de la solicitud
     * @param integer $emailEventId identificador del emailEvent a editar
     * @return Response en caso de exito redirecciona al listado de emailEvents, 
     * en caso de error despliega nuevamente el formulario de creacion de emailEvents
     */
    public function updateAction(Request $request, $emailEventId) {
        $em = $this->getDoctrine()->getManager();

        $emailEventStorage = $this->container->getParameter('kijho_mailer.storage')['email_event'];
        $emailEvent = $em->getRepository($emailEventStorage)->find($emailEventId);
        $form = $this->createForm(EmailEventType::class, $emailEvent, array(
            'translator' => $this->get('translator')
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {

            $parameters = $request->request->get('kijho_mailerbundle_email_event_type');
            if (isset($parameters['template'])) {
                $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
                $template = $em->getRepository($templateStorage)->find($parameters['template']);
                if ($template) {
                    $emailEvent->setTemplateSlug($template->getSlug());
                }
            }

            $em->persist($emailEvent);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageSuccessEmailEvent', $this->get('translator')->trans('kijho_mailer.email_event.update_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_email_event'));
        }

        return $this->render('KijhoMailerBundle:EmailEvent:edit.html.twig', array(
                    'emailEvent' => $emailEvent,
                    'form' => $form->createView(),
                    'menu' => 'emailEvents'
        ));
    }

    /**
     * Permite eliminar un emailEvent del sistema
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 26/11/2015
     * @param Request $request datos de la solicitud
     * @return Response Json con mensaje de respuesta
     */
    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $emailEventId = $request->request->get('emailEventId');
        $emailEventStorage = $this->container->getParameter('kijho_mailer.storage')['email_event'];
        $emailEvent = $em->getRepository($emailEventStorage)->find($emailEventId);

        $response['result'] = '__OK__';
        $response['msg'] = '';

        try {
            $em->remove($emailEvent);
            $em->flush();
        } catch (\Exception $exc) {
            $response['result'] = '__KO__';
            $response['msg'] = $this->get('translator')->trans('kijho_mailer.global.cant_delete_message');
        }
        return new JsonResponse($response);
    }

}
