<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize() {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');

        $this->loadComponent('Auth', [
            'loginRedirect' => [
                'controller' => 'Dashboard',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'username', 'password' => 'password'],
                    'finder' => 'auth'
                ]
            ]
        ]);

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        $this->loadComponent('Security');
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->loadModel('Users');
        if ($this->Auth->user()) {
            $this->updateUser($this->Auth->user());
            $this->set('liveUser', $this->Users->find('all', ['conditions' => ['last_update >=' => 'DATA_ADD(SECOUNDS, +1, GETDATE())']])->count());
        }
        $this->set('Auth', $this->Auth);
    }

    public function dateFormatSql($date) {
        if (!empty($date) && ($date != '0000-00-00 00:00:00')) {
            return date("Y-m-d H:i:s", strtotime($date));
        }
        return null;
    }

    /**
     * Refreshes the Auth session
     * @param string $field
     * @param string $value
     * @return void
     */
    public function _refreshAuth($field = '', $value = '') {
        if (!empty($field) && !empty($value)) { //Update just a single field in the Auth session data
            $this->Session->write(AuthComponent::$sessionKey . '.' . $field, $value);
        } else {
            if (!isset($this->Users)) {
                $this->loadModel('Users'); //Load the User model, if it is not already loaded
            }
            $user = $this->Users->find('all', [
                        'conditions' => [
                            'id' => $this->Auth->user('id')
                        ],
                        'contain' => ['UserFriends' => ['Friends']]
                    ])->first(); //Get the user's data
            $this->Auth->setUser($user); //Must have user data at top level of array that is passed to login method
        }
    }

    public function updateUser($user) {
        if (isset($user['id']) && !empty($user['id'])) {
            $this->Users->updateAll([
                'last_update' => date('Y-m-d h:i:s')
                    ], [
                'id' => $user['id']
            ]);
        }
    }

}
