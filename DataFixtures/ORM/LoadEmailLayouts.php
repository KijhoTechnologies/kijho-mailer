<?php

namespace Kijho\MailerBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kijho\MailerBundle\Entity\EmailLayout;
use Kijho\MailerBundle\Util\Util;

class LoadMailInfoData extends AbstractFixture implements OrderedFixtureInterface, FixtureInterface, ContainerAwareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager) {

        //Estos valores sólo se volcarán en el entorno de test
        if ($this->container->get('kernel')->getEnvironment() == 'test') {

            $emailLayout = new EmailLayout();

            $emailLayout->setCreationDate(Util::getCurrentDate());
            $emailLayout->setHeader('Test Header');
            $emailLayout->setFooter('Test Footer');
            $emailLayout->setName('Layout Test 1');

            $manager->persist($emailLayout, $manager);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }

    private function persist($object, $manager) {
        $manager->persist($object);
        return $manager->flush();
    }

}
