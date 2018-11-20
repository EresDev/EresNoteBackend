<?php
namespace App\Tests\Functional\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

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

        $this->markTestSkipped('must be revisited.');

        $credentials = [
            'email' => 'arslan',
            'passw' => 'cdss'
        ];

        $crawler = $this->client->request(
            'POST',
            "/api/guest/register",
            $credentials,
            [],
            ["CONTENT-TYPE" => "application/x-www-form-urlencoded", "CONTENT_TYPE" => "application/x-www-form-urlencoded"]

        );
        //print_r($this->client->getResponse());

//        if (!$this->client->getResponse()->isSuccessful()) {
//            $block = $crawler->filter('h1.exception-message');
//            if ($block->count()) {
//                $error = $block->text();
//            }
//            echo $error;
//        }

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * This method doesn't test an actual controller method
     * But it tests login_check URL provided by LexikJWTauthenticationBundle
     */
    public function testLoginCheck_validData(){
        $this->markTestSkipped('must be revisited.');
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
            ['CONTENT_TYPE' => 'application/json', 'CONTENT-TYPE' => 'application/json'],
            json_encode($credentials)
        );

        $content_array = json_decode($this->client->getResponse()->getContent());
        print_r($content_array); exit;
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
            ['CONTENT_TYPE' => 'application/json', 'CONTENT-TYPE' => 'application/json'],
            json_encode($credentials)
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode(), "HTTP Response Unexpected");
    }
}