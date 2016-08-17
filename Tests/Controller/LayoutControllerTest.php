<?php

namespace Kijho\MailerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LayoutControllerTest extends WebTestCase {

    protected $baseUrl;

    public function __construct() {
        $this->baseUrl = '/en/layouts/';
    }

    // // Comprueba que 1 === 1 es true
    //$this->assertTrue(1 === 1);
    // 
    //// Comprueba que 1 === 2 es false
    //$this->assertFalse(1 === 2);
    // 
    //// Comprueba que 'Hello' es igual 'Hello'
    //$this->assertEquals('Hello', 'Hello');
    // 
    //// Comprueba que array tiene la clave 'language'
    //$this->assertArrayHasKey('language', array('language' =&gt; 'php', 'size' =&gt; '1024'));
    // 
    //// Comprueba que array contiene el valor 'php'
    //$this->assertContains('php', array('php', 'ruby', 'c++', 'JavaScript'));

    /**
     * Permite verificar que se despliegue correctamente el listado de layouts
     */
    public function testIndex() {
        $client = static::createClient();

        $crawler = $client->request('GET', $this->baseUrl);

        $this->assertTrue($crawler->filter('section.header h2:contains("Layouts")')->count() > 0);
    }

    /**
     * Permite desplegar el listado de layouts, luego ingresar al formulario de
     * creacion de un layout
     */
    public function testCreateLayout() {
        $client = static::createClient();

        $crawler = $client->request('GET', $this->baseUrl);

        //validamos que estemos ubicados en el listado de Layouts
        $this->assertTrue($crawler->filter('section.header h2:contains("Layouts")')->count() > 0);

        $newLayoutLink = $crawler->filter('section.header div a')->first();
        $newLayoutTitle = $newLayoutLink->text();

        $crawler = $client->click($newLayoutLink->link());

        // Validamos si estamos ubicados en la pagina de agregar layouts (New Layout)
        $this->assertEquals(1, $crawler->filter('section.header h2:contains("' . $newLayoutTitle . '")')->count());


        // Llenamos el formulario para crear un Layout
        $form = $crawler->selectButton('Create')->form();

        $form['kijho_mailerbundle_layout_type[name]'] = 'Layout by Test';
        //$form['kijho_mailerbundle_layout_type[languageCode]'] = 'es';
        $form['kijho_mailerbundle_layout_type[header]'] = 'this is the header ..';
        $form['kijho_mailerbundle_layout_type[footer]'] = 'this is the footer';

        $crawler = $client->submit($form);

        // Necesitamos seguir la redirección
        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('div.alert-success:contains("Layout created successfully")')->count() > 0);
    }

}
