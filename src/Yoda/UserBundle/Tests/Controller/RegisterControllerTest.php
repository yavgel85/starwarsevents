<?php

namespace Yoda\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class RegisterControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();

        // TEST_6: empty the user table before running the test
        $container = self::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();
        $userRepo = $em->getRepository('UserBundle:User');
        $userRepo->createQueryBuilder('u')
            ->delete()
            ->getQuery()
            ->execute();

        // TEST_1: register page returns a 200 status & word “Register” appears somewhere
        $crawler = $client->request('GET', '/register');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Register', $response->getContent());

        // TEST_2: username should be equal to “Chewbacca”
        $usernameVal = $crawler
            ->filter('#user_register_username')
            ->attr('value');
        $this->assertEquals('Chewbacca', $usernameVal);

        //  TEST_3: the name of our button is "Register"
        $form = $crawler->selectButton('Register')->form();
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegexp(
            '/This value should not be blank/',
            $client->getResponse()->getContent()
        );

        // TEST_4: submit the form again
        $form = $crawler->selectButton('Register')->form();
        $form['user_register[username]'] = 'user5';
        $form['user_register[email]'] = 'user5@user.com';
        $form['user_register[plainPassword][first]'] = 'P3ssword';
        $form['user_register[plainPassword][second]'] = 'P3ssword';
        $crawler = $client->submit($form);

        // TEST_5: after submit we'll be redirected & make sure that our success flash message shows up after the redirect
        /*$crawler = $client->submit($form);*/
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
        $this->assertContains(
            'Welcome to the Death Star, have a magical day!',
            $client->getResponse()->getContent()
        );

    }
}
