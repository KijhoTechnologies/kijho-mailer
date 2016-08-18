<?php

namespace Kijho\MailerBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Kijho\MailerBundle\Model\Settings;
use Symfony\Component\HttpKernel\Kernel;

class EmailSettingsType extends AbstractType {

    protected $storageEntity;
    protected $container;

    public function __construct($container) {
        $this->container = $container;
        $this->storageEntity = $this->container->getParameter('kijho_mailer.storage')['settings'];
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $this->translator = $options['translator'];
        $version = "3.0.0";
        $symfonyVersion = Kernel::VERSION;

        $settings = new Settings();

        $sendMode = array(Settings::SEND_MODE_INSTANTANEOUS => $this->translator->trans($settings->getSendModeDescription(Settings::SEND_MODE_INSTANTANEOUS)),
            Settings::SEND_MODE_PERIODIC => $this->translator->trans($settings->getSendModeDescription(Settings::SEND_MODE_PERIODIC)),
            Settings::SEND_MODE_BY_EMAIL_AMOUNT => $this->translator->trans($settings->getSendModeDescription(Settings::SEND_MODE_BY_EMAIL_AMOUNT)));

        if (version_compare($symfonyVersion, $version) == '-1') {
            $sendModeArray = $sendMode;
        } else {
            $sendModeArray = array_flip($sendMode);
        }
        $builder
                ->add('sendMode', ChoiceType::class, array('required' => true,
                    'choices' => $sendModeArray,
                    'label' => $this->translator->trans('kijho_mailer.setting.send_mode'),
                    'placeholder' => $this->translator->trans('kijho_mailer.global.select'),
                    'attr' => array('class' => 'form-control')))
                ->add('limitEmailAmount', NumberType::class, array('required' => true,
                    'label' => $this->translator->trans('kijho_mailer.setting.limit_email_amount'),
                    'attr' => array('class' => 'form-control only_numbers',
                        'maxlength' => 3)))
                ->add('intervalToSend', NumberType::class, array('required' => true,
                    'label' => $this->translator->trans('kijho_mailer.setting.interval_to_send'),
                    'attr' => array('class' => 'form-control only_numbers',
                        'maxlength' => 3)))
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
        return 'kijho_mailerbundle_settings_type';
    }

    public function getBlockPrefix() {
        return $this->getName();
    }

}
