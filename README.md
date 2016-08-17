<html>
 <body>
<h1>Instalación</h1>


<h3>composer.json</h3>

Colocar esta linea en el archivo composer.json del proyecto:

<pre style="font-family: Courier New;">composer require kijho/kijho-mailer 1.0.0</pre>


Es necesario tener instalado:

<pre style="font-family: Courier New;">"fkr/cssurlrewrite-bundle": "*"</pre>

<h3>AppKernel.php</h3>

Colocar la siguiente linea en el archivo AppKernel para inicializar el Bundle:

<pre style="font-family: Courier New;">new Kijho\MailerBundle\KijhoMailerBundle()</pre>


<h3>routing.yml</h3>

Colocar la siguiente ruta en el archivo routing.yml del proyecto:

<pre style="font-family: Courier New;">
kijho_mailer:
    resource: "@KijhoMailerBundle/Resources/config/routing.yml"
    prefix:   /{_locale}/kijhoMailer
</pre>


<h3>config.yml</h3>

Habilitar la configuración del lenguaje en el archivo config.yml colocando  el lenguaje por defecto para el vendor. De momento los lenguajes disponibles son ingles y español (en, es)

<pre style="font-family: Courier New;">
framework:
    translator:      { fallbacks: [en] }
</pre>


Kijho Mailer requiere el vendor de assets, una vez instalado colocar el nombre del bundle en los bundles que usan assets:

<pre style="font-family: Courier New;">
assetic:
    debug:          "%kernel.debug%"
    use_controller: false
    bundles:        [AcmeDemoBundle, KijhoMailerBundle]
</pre>

Poner en el archivo config.yml las configuraciones propias del vendor:
<pre style="font-family: Courier New;">

# Twig Configuration
twig:
    globals:
        email_manager: "@email_manager"

kijho_mailer:
    entity_directories: ["%kernel.root_dir%/../src/Acme/DemoBundle/Entity/"]
    entity_namespace: "Acme\DemoBundle\Entity"
    storage:
        layout:         "Kijho\MailerBundle\Entity\EmailLayout"
        template_group: "Kijho\MailerBundle\Entity\EmailTemplateGroup"
        template:       "Kijho\MailerBundle\Entity\EmailTemplate"
        settings:       "Kijho\MailerBundle\Entity\EmailSettings"
        email:          "Kijho\MailerBundle\Entity\Email"
        email_event:    "Kijho\MailerBundle\Entity\EmailEvent"
</pre>

Instanciar las siguientes extensiones de twig en la seccion de servicios:
<pre>
services:
    twig.extension.evaluate:
      class: Kijho\MailerBundle\Twig\Extension\EvaluateExtension
      tags:
          - { name: twig.extension }
</pre>

Asegurarse de configurar los parametros del swiftmailer, los parametros van en el archivo parameters.yml:

<pre style="font-family: Courier New;">
swiftmailer:
    transport: "%mailer_transport%"
    host:      "%mailer_host%"
    username:  "%mailer_user%"
    password:  "%mailer_password%"
    spool:     { type: memory }
</pre>


<h4>composer update</h4>
Ejecute en su proyecto el comando <pre style="font-family: Courier New;">composer update</pre> 

<h4>doctrine schema update</h4>
Ejecute en su proyecto el comando <pre style="font-family: Courier New;">php app/console d:s:u --force</pre> 

<h4>Enlace</h4>
Ahora para acceder a las funcionalidades del vendor instalado, coloque en cualquier parte de su proyecto un enlace con la ruta:
<pre style="font-family: Courier New;">{{path('kijho_mailer_homepage')}}</pre>

<script>
    $( function() { $("PRE").prettyPre(); } );
</script>

</body>
</html>