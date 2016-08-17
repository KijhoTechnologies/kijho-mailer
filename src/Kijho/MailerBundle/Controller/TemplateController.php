<?php

namespace Kijho\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\MailerBundle\Form\EmailTemplateType;
use Symfony\Component\HttpFoundation\Request;
use Kijho\MailerBundle\Util\Util;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class TemplateController extends Controller {

    /**
     * Permite visualizar el listado de todos los templates creados en el sistema
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 11/11/2015
     * @return type
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];

        $templates = $em->getRepository($templateStorage)->findAll();

        return $this->render('KijhoMailerBundle:Template:index.html.twig', array(
                    'templates' => $templates,
                    'menu' => 'templates'
        ));
    }

    /**
     * Permite desplegar el formulario de creacion de templates
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 11/11/2015
     * @return Response formulario de creacion del template
     */
    public function newAction(Request $request) {

        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
        $template = new $templateStorage;

        $templateId = $request->get('templateId');
        if ($templateId) {
            $em = $this->getDoctrine()->getManager();
            $template = $em->getRepository($templateStorage)->find($templateId);
        }

        $entities = $this->getReflectedProjectEntities();

        $form = $this->createForm(EmailTemplateType::class, $template, array(
            'translator' => $this->get('translator'),
            'entities' => $entities['instances'],
        ));

        return $this->render('KijhoMailerBundle:Template:new.html.twig', array(
                    'template' => $template,
                    'form' => $form->createView(),
                    'entities' => $entities,
                    'menu' => 'templates',
                    'templateId' => $templateId
        ));
    }

    /**
     * Permite validar y almacenar la informacion de un template
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 11/11/2015
     * @param Request $request datos de la solicitud
     * @return Response en caso de exito redirecciona al listado de templates, 
     * en caso de error despliega nuevamente el formulario de creacion de templates
     */
    public function saveAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
        $template = new $templateStorage;

        $entities = $this->getReflectedProjectEntities();
        $form = $this->createForm(EmailTemplateType::class, $template, array(
            'translator' => $this->get('translator'),
            'entities' => $entities['instances'],
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $template->setCreationDate(Util::getCurrentDate());
            $em->persist($template);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageSuccessTemplate', $this->get('translator')->trans('kijho_mailer.template.creation_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_template'));
        }

        return $this->render('KijhoMailerBundle:Template:new.html.twig', array(
                    'template' => $template,
                    'form' => $form->createView(),
                    'entities' => $entities,
                    'menu' => 'templates'
        ));
    }

    /**
     * Permite desplegar el formulario de edicion de templates
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 11/11/2015
     * @param integer $templateId identificador del template a editar
     * @return Response formulario de edicion del template
     */
    public function editAction($templateId) {

        $em = $this->getDoctrine()->getManager();

        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];

        $template = $em->getRepository($templateStorage)->find($templateId);

        $entities = $this->getReflectedProjectEntities();

        $form = $this->createForm(EmailTemplateType::class, $template, array(
            'translator' => $this->get('translator'),
            'entities' => $entities['instances'],
        ));

        return $this->render('KijhoMailerBundle:Template:edit.html.twig', array(
                    'template' => $template,
                    'form' => $form->createView(),
                    'entities' => $entities,
                    'menu' => 'templates'
        ));
    }

    /**
     * Permite validar y almacenar los cambios en la informacion de un template
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 11/11/2015
     * @param Request $request datos de la solicitud
     * @param integer $templateId identificador del template a editar
     * @return Response en caso de exito redirecciona al listado de templates, 
     * en caso de error despliega nuevamente el formulario de creacion de templates
     */
    public function updateAction(Request $request, $templateId) {
        $em = $this->getDoctrine()->getManager();

        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
        $template = $em->getRepository($templateStorage)->find($templateId);
        $entities = $this->getReflectedProjectEntities();
        $form = $this->createForm(EmailTemplateType::class, $template, array(
            'translator' => $this->get('translator'),
            'entities' => $entities['instances'],
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($template);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageSuccessTemplate', $this->get('translator')->trans('kijho_mailer.template.update_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_template'));
        }

        return $this->render('KijhoMailerBundle:Template:edit.html.twig', array(
                    'template' => $template,
                    'form' => $form->createView(),
                    'entities' => $entities,
                    'menu' => 'templates'
        ));
    }

    /**
     * Permite desplegar el preview de un template
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 11/11/2015
     * @param integer $templateId identificador del template
     * @return Response pagina con el preview del template
     */
    public function previewAction($templateId) {

        $em = $this->getDoctrine()->getManager();

        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];

        $template = $em->getRepository($templateStorage)->find($templateId);


        return $this->render('KijhoMailerBundle:Template:preview.html.twig', array(
                    'template' => $template,
                    'menu' => 'templates'
        ));
    }

    /**
     * Permite eliminar un template del sistema
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 11/11/2015
     * @param Request $request datos de la solicitud
     * @return Response Json con mensaje de respuesta
     */
    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $templateId = $request->request->get('templateId');
        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
        $template = $em->getRepository($templateStorage)->find($templateId);

        $response['result'] = '__OK__';
        $response['msg'] = '';

        try {
            $em->remove($template);
            $em->flush();
        } catch (\Exception $exc) {
            $response['result'] = '__KO__';
            $response['msg'] = $this->get('translator')->trans('kijho_mailer.global.cant_delete_message');
        }
        return new JsonResponse($response);
    }

    /**
     * Esta funcion permite obtener la estructura de todas las entidades ubicadas 
     * en los paquetes de entidades del proyecto
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 12/11/2015
     * @return array(\ReflectionClass)
     */
    public function getReflectedProjectEntities() {
        //escaneamos el contenido de las carpetas que contienen las entidades del proyecto
        $entityDirectories = $this->container->getParameter('kijho_mailer.entity_directories');
        $instances = array();

        foreach ($entityDirectories as $entityDirectory) {

            if (file_exists($entityDirectory)) {

                $files = scandir($entityDirectory, 2);

                //guardamos en un arreglo los nombres de las entidades
                $entities = array();
                foreach ($files as $file) {
                    $position = strpos($file, '.php');
                    if ($position) {
                        $fileName = substr($file, 0, $position);
                        $position = strpos($file, 'Repository');
                        if ($position === false) {
                            array_push($entities, $fileName);
                        }
                    }
                }

                //encontramos el namespace del paquete de entidades a partir de uno de los parametros de strorage
                $entityNamespace = $this->getEntityNamespace();

                //instanciamos cada una de las entidades y le aplicamos Reflection para conocer su estructura
                foreach ($entities as $entity) {
                    $path = $entityNamespace . $entity;
                    try {
                        $instance = new \ReflectionClass(new $path);
                        array_push($instances, $instance);
                    } catch (\Exception $exc) {
                        //No era una clase PHP
                    }
                }
            }
        }

        $relationships = $this->getEntityRelationships($instances);

        return array('instances' => $instances,
            'relationships' => $relationships);
    }

    private function getEntityNamespace() {
        //encontramos el namespace del paquete de entidades a partir de uno de los parametros de strorage
        $entityNamespace = $this->container->getParameter('kijho_mailer.entity_namespace') . "\\";
        return $entityNamespace;
    }

    /**
     * Permite hallar las relaciones que tiene una entidad con otras entidades
     * a partir de la informacion en las anotaciones de sus atributos
     * Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 17/11/2015
     * @param array[\ReflectionClass] $instances arreglo de instancias a analizar
     * @return \ReflectionClass
     */
    function getEntityRelationships($instances) {
        $relationships = array();
        foreach ($instances as $instance) {
            foreach ($instance->getProperties() as $property) {
                //$property = new \ReflectionProperty;
                $docComment = $property->getDocComment();
                $position = strpos($docComment, "targetEntity=");
                if ($position) {
                    $docComment = substr($docComment, $position);

                    $entityName = Util::getBetween($docComment, 'targetEntity="', '"');
                    if (empty($entityName)) {
                        $entityName = Util::getBetween($docComment, "targetEntity='", "'");
                    }
                    try {
                        $entityNamespace = $this->getEntityNamespace();
                        //verificamos que la entidad este escrita con el namespace completo
                        $positionNamespace = strpos($entityName, $entityNamespace);
                        if ($positionNamespace === false) {
                            $entityName = $entityNamespace . $entityName;
                        }
                        $object = new \ReflectionClass(new $entityName);
                        $relationships[$instance->getName()][$property->getName()] = $object;
                    } catch (\Exception $exc) {
                        // El namespace no esta escrito con ruta absoluta
                        //echo $exc->getTraceAsString();
                    }
                }
            }
        }
        return $relationships;
    }

    /**
     * Esta funcion permite realizar el envio de un correo electronico utilizando
     * los datos encontrados en el template seleccionado por el usuario
     * Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 17/11/2015
     * @param Request $request datos de la solicitud
     * @return JsonResponse Json con mensaje de respuesta
     */
    public function sendExampleEmailAction(Request $request) {

        $templateId = (int) $request->request->get('templateId');
        $emailAddress = trim($request->request->get('email'));

        $em = $this->getDoctrine()->getManager();
        $templateStorage = $this->container->getParameter('kijho_mailer.storage')['template'];
        $template = $em->getRepository($templateStorage)->find($templateId);

        $response = array('result' => '__OK__',
            'msg' => $this->get('translator')->trans('kijho_mailer.email.sent_success'));

        //creamos el correo y lo almacenamos en base de datos
        $email = $this->get('email_manager')->composeEmail($template, $emailAddress);
        $em->persist($email);
        $em->flush($email);

        $dataToTemplate = array();
        if ($template->getEntityName()) {
            try {
                $entityName = $template->getEntityName();
                $dataToTemplate['entity'] = new $entityName;
            } catch (\Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }

        try {
            //verificamos el metodo de envio
            if ($this->get('email_manager')->isSendInstantaneous()) {
                $this->get('email_manager')->send($email, $dataToTemplate);
            } else {
                $this->get('email_manager')->verifyPendingEmails();
            }
        } catch (\Exception $exc) {
            $response = array('result' => '__KO__',
                'msg' => $this->get('translator')->trans('kijho_mailer.email.sent_error'));
        }

        return new JsonResponse($response);
    }

}
