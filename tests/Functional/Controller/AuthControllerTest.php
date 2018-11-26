<?php
namespace App\Tests\Functional\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    /**
     * @var Client $client
     */
    private $client = null;

    public function setUp()
    {
        $this->client = $this->makeClient();
    }

    public function testRegister(){

        $credentials = [
            'email' => 'register_test_user@example.com',
            'plainPassword' => [
                'pass' => 'someValidPassword12',
                'pass2' => 'someValidPassword12'
            ]
        ];

        $this->client->request(
            'POST',
            "/guest/register",
            $credentials,
            [],
            ["CONTENT-TYPE" => "application/x-www-form-urlencoded"]

        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }


    public function testRegister_invalidEmail(){

        $credentials = [
            'email' => 'register_test_user',
            'plainPassword' => [
                'pass' => 'someValidPassword12',
                'pass2' => 'someValidPassword12'
            ]
        ];

        $this->client->request(
            'POST',
            "/guest/register",
            $credentials,
            [],
            ["CONTENT-TYPE" => "application/x-www-form-urlencoded"]
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testRegister_emptyPassword(){

        $credentials = [
            'email' => 'register_test_user@example.com',
            'plainPassword' => [
                'pass' => '',
                'pass2' => ''
            ]
        ];

        $this->client->request(
            'POST',
            "/guest/register",
            $credentials,
            [],
            ["CONTENT-TYPE" => "application/x-www-form-urlencoded"]

        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }


    public function testRegister_differentPasswordAndConfirmPassword(){

        $credentials = [
            'email' => 'register_test_user@example.com',
            'plainPassword' => [
                'pass' => 'this_pass_1',
                'pass2' => 'this_pass_2'
            ]
        ];

        $this->client->request(
            'POST',
            "/guest/register",
            $credentials,
            [],
            ["CONTENT-TYPE" => "application/x-www-form-urlencoded"]

        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testRegister_extraFieldNotAccepted(){

        $credentials = [
            'email' => 'register_test_user@example.com',
            'plainPassword' => [
                'pass' => 'someValidPassword12',
                'pass2' => 'someValidPassword12'
            ],
            'extra_field' => 'let us see'
        ];

        $this->client->request(
            'POST',
            "/guest/register",
            $credentials,
            [],
            ["CONTENT-TYPE" => "application/x-www-form-urlencoded"]

        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * This method doesn't test an actual controller method
     * But it tests login_check URL provided by LexikJWTauthenticationBundle
     */
    public function testLoginCheck_validData(){

        $this->loadFixtures(array(
            'App\DataFixtures\UserFixtures'
        ));

        $credentials = [
            'email' => 'arslanafzal321@gmail.com',
            'passw' => '3489hteur43xw21@1'
        ];

        $this->client->request(
            'POST',
            "/api/login_check",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($credentials)
        );

        $content_array = json_decode($this->client->getResponse()->getContent());

        $token = $content_array->token;

        $decoded_token = $this->client->getContainer()
            ->get('lexik_jwt_authentication.encoder')
            ->decode($token);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "HTTP Response Unexpected");
        $this->assertEquals($credentials['email'], $decoded_token['username']);
    }

    /**
     * This method doesn't test an actual controller method
     * But it tests login_check URL provided by LexikJWTauthenticationBundle
     */
    public function testLoginCheck_invalidData(){

        $this->loadFixtures(array(
            'App\DataFixtures\UserFixtures'
        ));

        $credentials = [
            'email' => 'arslanafzal321@gmail.com',
            'passw' => 'wrong_password'
        ];

        $this->client->request(
            'POST',
            "/api/login_check",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($credentials)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode(), "HTTP Response Unexpected");
    }
}