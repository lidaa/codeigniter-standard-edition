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

    public function testEmailValidation() {
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

    public function testRequest() 
    {
        $base_url = sprintf('http://%s', rtrim(trim($this->CI->config->item('base_url'), 'http://'), '/')); 

        $url = sprintf('%s/index.php/welcome/index', $base_url);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($curl);
        
        $this->assertContains('Welcome to CodeIgniter', $html);
        $this->assertEquals('200', curl_getinfo($curl, CURLINFO_HTTP_CODE));
                
        curl_close($curl);
    }

}
