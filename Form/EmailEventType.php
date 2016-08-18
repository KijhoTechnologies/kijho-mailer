<?php

namespace Kijho\MailerBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class EmailEventType extends AbstractType {

    protected $storageEntity;
    protected $container;

    public function __construct($container) {
        $this->container = $container;
        $this->storageEntity = $this->container->getParameter('kijho_mailer.storage')['email_event'];
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->translator = $options['translator'];

        $builder
                ->add('name', TextType::class, array('required' => true,
                    'label' => $this->translator->trans('kijho_mailer.email_event.name'),
                    'attr' => array('class' => 'form-control')))
                ->add('template', EntityType::class, array(
                    'class' => $this->container->getParameter('kijho_mailer.storage')['template'],
                    'choice_label' => 'slug',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                                ->groupBy('t.slug')
                                ->orderBy('t.name', 'ASC');
                    },
                    'label' => $this->translator->trans('kijho_mailer.email_event.template'),
                    'required' => true,
                    'mapped' => false,
                    'placeholder' => $this->translator->trans('kijho_mailer.global.select'),
                    'attr' => array('class' => 'form-control')
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('translator');
        $resolver->setDefaults(array(
            'data_class' => $this->storageEntity
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'kijho_mailerbundle_email_event_type';
    }

    public function getBlockPrefix() {
        return $this->getName();
    }

}
