<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Mailer\MailerAwareTrait;
use Cake\Mailer\Email;
use Cake\Log\Log;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController {

    public function initialize() {
        parent::initialize();
        $this->Auth->allow(['logout', 'register', 'forgotPassword', 'reset', 'activateByEmail', 'sendmail']);
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null
     */
    public function login() {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $users_table = TableRegistry::get('users');
                $users = $users_table->get($user['id']);
                if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
                    $users->password = $this->request->getData('password');
                }
                $users->ip_address = $this->request->clientIp();
                if ($users_table->save($users)) {
                    $this->Auth->setUser($user);
                    return $this->redirect($this->Auth->redirectUrl());
                }
            }
            $this->Flash->error('Your username or password is incorrect.');
        }
        $this->viewBuilder()->setLayout('public');
    }

    public function sendmail() {
        ini_set('max_execution_time', 0); //0=NOLIMIT
        $email = new Email('default');
        $emails = file_get_contents(WWW_ROOT . 'sheet.csv');
        $emails = preg_split('/\r\n|\r|\n/', $emails);
        foreach ($emails as $email_id) {
            $email_id = trim(str_replace(' ', '', $email_id));
            if (empty($email_id)) {
                continue;
            }
            if (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
                Log::write('debug', 'Email Not Valida : '.$email_id);
                continue;
            }
            $email->setFrom(['rohit.rodia@purplewave.in' => 'Rohit Rodia'])
                    ->setTo($email_id)
//                    ->setCc('sarwarrahman123@yahoo.com')
                    ->setCc('pwipl.govtcare@gmail.com')
                    ->setSubject('Infrared Thermometer for your Institution')
                    ->setEmailFormat('html')
                    ->setAttachments([
                        'pn.png' => [
                            'file' => WWW_ROOT . '/img/pn.png',
                            'mimetype' => 'image/png',
                            'contentId' => 'unique-id'
                        ],
                        WWW_ROOT . '/FH-IR-10B Infrared_Thermometer.pdf',
                        WWW_ROOT . '/WM-IR20A Wall Mount Infrared Digital Thermometer_compressed.pdf'
                    ])
                    ->setTemplate('sarwar')
                    ->send();
            sleep(3);
            Log::write('debug', $email_id);
        }
        $this->autoRender = false;
        $this->viewBuilder()->setLayout(false);
    }

    /**
     * Register method
     *
     * @return \Cake\Http\Response|null
     */
    public function register() {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('We have sent link on your registered mobile number and email to activate your account.'));
                return $this->redirect('/');
            }
            $this->Flash->error(__('Registeration could not be successfull. Please, try again.'));
        }
        $this->set(compact('user'));
        $this->viewBuilder()->setLayout('public');
    }

/**
     * Forgot Password method
     *
     * @return \Cake\Http\Response|null
     */
    use MailerAwareTrait;

    public function forgotPassword() {
        if ($this->request->is('post')) {
            $emailId = $this->request->getData('email');
            if (!empty($emailId)) {
                $userData = $this->Users->findByEmail($emailId);
                if ($userData->count()) {
                    $userDetails = $userData->first();
                    $userDetails->token = $this->Users->tokenUpdate($emailId);
                    $emailObj = $this->getMailer('User');
                    if ($emailObj->send('resetPassword', [$userDetails])) {
                        $this->Flash->success(__('Password reset link has been sent on your email.It will expire in 1 hour'));
                        $this->redirect($this->referer());
                    } else {
                        $this->Flash->error(__('Reset Password could not be sent on your email. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(__('Provided Email ID not exists.'));
                }
            } else {
                $this->Flash->error(__('Please enter Email ID.'));
            }
        }
        $this->viewBuilder()->setLayout('public');
    }

    /**
     * Reset Password method
     *
     * @return \Cake\Http\Response|null
     */
    public function reset($encodedId, ...$tokenParam) {
        $token = implode('/', $tokenParam);
        $id = base64_decode(base64_decode($encodedId));
        $userDetails = $this->Users->find('all', [
            'conditions' => [
                'id' => $id,
                'token' => $token,
                'token_expire >' => date('Y-m-d h:i:s', strtotime('-1 hour'))
            ]
        ]);
        if (!$userDetails->count()) {
            $this->Flash->error(__('Token expire or not authorized.'));
            $this->redirect('/');
        }
        if ($this->request->is(['post', 'put'])) {
            $password = $this->request->getData('password');
            if (strlen($password) > 5) {
                $userDetails = $userDetails->first();
                $userDetails->password = $password;
                $userDetails->token = null;
                $userDetails->token_expire = null;
                $this->Users->save($userDetails);
                $this->Flash->success(__('Password change successfull.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Provided password length is less than 6.'));
            }
        }
        $this->set(compact('userDetails'));
        $this->viewBuilder()->setLayout('public');
    }

    /**
     * activateByEmail method
     *
     * @return Redirect()
     */
    function activateByEmail($encodedEmail, ...$tokenParam) {
        $token = implode('/', $tokenParam);
        $email = base64_decode(base64_decode($encodedEmail));
        $userDetails = $this->Users->find('all', [
            'conditions' => [
                'email' => $email,
                'token' => $token,
            ]
        ]);
        if (!$userDetails->count()) {
            $this->Flash->error(__('Token expire or not authorized.'));
            $this->redirect('/');
        }
        $userDetails = $userDetails->first();
        $userDetails->is_active = 1;
        $userDetails->token = null;
        $userDetails->token_expire = null;
        if ($this->Users->save($userDetails)) {
            $this->Flash->success(__('Your account is activate, please login.'));
        } else {
            $this->Flash->error(__('Unable to activate your account, please try again later.'));
        }
        $this->redirect('/');
    }

    /**
     * Logout method
     *
     * @return Redirect()
     */
    public function logout() {
        $this->Flash->success('You are now logged out.');
        return $this->redirect($this->Auth->logout());
    }

}
