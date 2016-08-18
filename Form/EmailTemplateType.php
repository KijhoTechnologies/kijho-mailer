<?php

namespace Kijho\MailerBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Kijho\MailerBundle\Model\Template;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Kernel;

class EmailTemplateType extends AbstractType {

    protected $storageEntity;
    protected $container;
    protected $entityNames;

    public function __construct($container) {
        $this->container = $container;
        $this->storageEntity = $this->container->getParameter('kijho_mailer.storage')['template'];
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->translator = $options['translator'];
        $version = "3.0.0";
        $symfonyVersion = Kernel::VERSION;
        //incluimos en el formulario las entidades que se identificaron en el proyecto
        if (!empty($options['entities'])) {
            $this->entityNames = array();
            foreach ($options['entities'] as $entity) {
                /**
                 * for symfony 2.8 symfonyVersion < version
                 */
                if (version_compare($symfonyVersion, $version) == '-1') {
                    $this->entityNames[$entity->getName()] = $entity->getShortName();
                } else {
                    $this->entityNames[$entity->getShortName()] = $entity->getName();
                }
            }
        }
        $template = new Template();
        $status = array(
            Template::STATUS_ENABLED => $this->translator->trans($template->getStatusDescription(Template::STATUS_ENABLED)),
            Template::STATUS_DISABLED => $this->translator->trans($template->getStatusDescription(Template::STATUS_DISABLED))
        );
        if (version_compare($symfonyVersion, $version) == '-1') {
            $statusArray = $status;
        } else {
            $statusArray = array_flip($status);
        }




        if (version_compare($symfonyVersion, $version) == '-1') {
            $mailers = $this->container->get('email_manager')->getMailers();
        } else {
            $mailers = $flipped = array_flip($this->container->get('email_manager')->getMailers());
        }

        $defaultMailer = $this->container->getParameter('swiftmailer.default_mailer');

        $builder
                ->add('layout', EntityType::class, array(
                    'class' => $this->container->getParameter('kijho_mailer.storage')['layout'],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                                ->orderBy('l.name', 'ASC');
                    },
                    'label' => $this->translator->trans('kijho_mailer.template.layout'),
                    'required' => false,
                    'placeholder' => $this->translator->trans('kijho_mailer.template.no_layout'),
                    'attr' => array('class' => 'form-control')))
                ->add('group', EntityType::class, array(
                    'class' => $this->container->getParameter('kijho_mailer.storage')['template_group'],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('g')
                                ->orderBy('g.name', 'ASC');
                    },
                    'label' => $this->translator->trans('kijho_mailer.template.group'),
                    'required' => false,
                    'placeholder' => $this->translator->trans('kijho_mailer.template.no_group'),
                    'attr' => array('class' => 'form-control')))
                ->add('name', TextType::class, array('required' => true,
                    'label' => $this->translator->trans('kijho_mailer.template.name'),
                    'attr' => array('class' => 'form-control')))
                ->add('slug', TextType::class, array('required' => true,
                    'label' => $this->translator->trans('kijho_mailer.global.slug'),
                    'attr' => array('class' => 'form-control')))
                ->add('fromName', TextType::class, array('required' => false,
                    'label' => $this->translator->trans('kijho_mailer.template.from_name'),
                    'attr' => array('class' => 'form-control')))
                ->add('fromMail', EmailType::class, array('required' => false,
                    'label' => $this->translator->trans('kijho_mailer.template.from_mail'),
                    'attr' => array('class' => 'form-control',
                        'placeholder' => $this->translator->trans('kijho_mailer.global.email_example'))))
                ->add('copyTo', EmailType::class, array('required' => false,
                    'label' => $this->translator->trans('kijho_mailer.template.copy_to'),
                    'attr' => array('class' => 'form-control',
                        'placeholder' => $this->translator->trans('kijho_mailer.global.email_example'))))
                ->add('subject', TextType::class, array('required' => false,
                    'label' => $this->translator->trans('kijho_mailer.template.subject'),
                    'attr' => array('class' => 'form-control')))
                ->add('contentMessage', TextareaType::class, array('required' => false,
                    'label' => $this->translator->trans('kijho_mailer.layout.content'),
                    'attr' => array('class' => 'form-control')))
                ->add('status', ChoiceType::class, array('required' => true,
                    'choices' => $statusArray,
                    'label' => $this->translator->trans('kijho_mailer.template.status'),
                    'attr' => array('class' => 'form-control')))
                ->add('mailerSettings', ChoiceType::class, array('required' => true,
                    'choices' => $mailers,
                    'preferred_choices' => array($defaultMailer),
                    'label' => $this->translator->trans('kijho_mailer.global.smtp'),
                    'attr' => array('class' => 'form-control')))
                ->add('entityName', ChoiceType::class, array('required' => false,
                    'choices' => $this->entityNames,
                    'label' => $this->translator->trans('kijho_mailer.template.select_entity'),
                    'placeholder' => $this->translator->trans('kijho_mailer.global.select'),
                    'attr' => array('class' => 'form-control')))
                ->add('languageCode', LanguageType::class, array('required' => true,
                    'placeholder' => $this->translator->trans('kijho_mailer.global.select'),
                    'label' => $this->translator->trans('kijho_mailer.global.language'),
                    'attr' => array('class' => 'form-control')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('translator');
        $resolver->setRequired('entities');

        $resolver->setDefaults(array(
            'data_class' => $this->storageEntity
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'kijho_mailerbundle_template_type';
    }

    public function getBlockPrefix() {
        return $this->getName();
    }

}
