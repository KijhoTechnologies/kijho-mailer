<?php

namespace Kijho\MailerBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailLayoutType extends AbstractType {

    protected $storageEntity;
    protected $container;

    public function __construct($container) {
        $this->container = $container;
        $this->storageEntity = $this->container->getParameter('kijho_mailer.storage')['layout'];
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->translator = $options['translator'];

        $builder
                ->add('name', TextType::class, array('required' => true,
                    'label' => $this->translator->trans('kijho_mailer.global.name'),
                    'attr' => array('class' => 'form-control')))
                ->add('header', TextareaType::class, array('required' => false,
                    'label' => $this->translator->trans('kijho_mailer.layout.header'),
                    'attr' => array('class' => 'form-control')))
                ->add('footer', TextareaType::class, array('required' => false,
                    'label' => $this->translator->trans('kijho_mailer.layout.footer'),
                    'attr' => array('class' => 'form-control')))
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
        return 'kijho_mailerbundle_layout_type';
    }

    public function getBlockPrefix() {
        return $this->getName();
    }

}
