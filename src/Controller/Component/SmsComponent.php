<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Sms component
 */
class SmsComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'username' => 'EdCreate',
        'password' => 'Ed123',
        'type' => 'TEXT',
        'sender' => 'EDCRTT',
        'mobile',
        'message',
    ];
    protected $genratedUrl;
    protected $base_url = 'http://sms.prowtext.com/sendsms/bulksms.php?';

    public function setBaseURL($base_url) {
        $this->base_url = $base_url;
    }

    public function setUsername($username) {
        $this->_defaultConfig['username'] = $username;
    }

    public function setPassword($password) {
        $this->_defaultConfig['password'] = $password;
    }

    public function setType($type) {
        $this->_defaultConfig['type'] = $type;
    }

    public function setSender($sender) {
        $this->_defaultConfig['sender'] = $sender;
    }

    public function setMobileNumber($mobile) {
        $this->_defaultConfig['mobile'] = $mobile;
    }

    public function setMessage($text) {
        $this->_defaultConfig['message'] = $text;
    }

    public function getgenratedUrl() {
        $this->genratedUrl;
    }

    public function genrateUrl() {
        $this->genratedUrl = $this->base_url . http_build_query($this->_defaultConfig);
    }

    public function send($multiple = true) {
        if (empty($this->genratedUrl) || $multiple) {
            $this->genrateUrl();
        }
        return file_get_contents($this->genratedUrl);
    }

}
