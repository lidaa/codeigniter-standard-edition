<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example_Test
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Example_Test extends PHPUnit_Framework_TestCase
{
    private $CI;

    public function setUp()
    {
        $this->CI = &get_instance();
    }

    public function testEmailValidation()
    {
        $this->CI->load->helper('email');

        $this->assertTrue(valid_email('test@test.com'));
        $this->assertFalse(valid_email('test#testcom'));
    }

    public function testGetPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['foo'] = 'bar';
        $this->assertEquals('bar', $this->CI->input->get_post('foo'));
    }

    public function testPOST()
    {
        $base_url = sprintf('http://%s', rtrim(trim($this->CI->config->item('base_url'), 'http://'), '/')); 

        // create our http client (Guzzle)
        $client = new \Guzzle\Http\Client($base_url);

        $data = array();

        $request = $client->get('/welcome/index', null, json_encode($data));

        $response = $request->send();

        $this->assertContains('Welcome to CodeIgniter', $response->getBody(true));
        $this->assertEquals('200', $response->getStatusCode());
    }
}
