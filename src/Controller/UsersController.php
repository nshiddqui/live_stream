<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Mailer\MailerAwareTrait;
use Cake\Mailer\Email;
use Cake\Log\Log;
use Cake\Routing\Router;

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
     * View method
     *
     * @param string|null $id Stream id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function index() {
        $id = $this->Auth->user('id');
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (empty($data['password'])) {
                unset($data['password']);
            }
            if (isset($data['email'])) {
                unset($data['email']);
            }
            $user = $this->Users->patchEntity($user, $data, ['validate' => false]);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The profile has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null
     */
    public function login() {
        if ($this->Auth->user()) {
            $this->redirect('dashboard');
        }
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
                Log::write('debug', 'Email Not Valida : ' . $email_id);
                continue;
            }
            $email->setFrom(['sarwar786rahman@gmail.com' => 'Sarwar Rahman'])
                    ->setTo($email_id)
//                    ->setCc('sarwarrahman123@yahoo.com')
                    ->setCc('pwipl.bankingcare@gmail.com')
                    ->setSubject('Infrared Thermometer for your Institution')
                    ->setEmailFormat('html')
                    ->setAttachments([
                        'pn.png' => [
                            'file' => WWW_ROOT . '/img/pn.png',
                            'mimetype' => 'image/png',
                            'contentId' => 'unique-id'
                        ],
                        WWW_ROOT . '/TheIVY_FH-IR-10B Infrared_Thermometer.pdf',
                        WWW_ROOT . '/TheIVY_WM-IR20A Wall Mount Infrared Digital Thermometer.pdf'
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
            $data = $this->request->getData();
            if (!empty($data['mobile_number']) || !empty($data['email'])) {
                $user = $this->Users->patchEntity($user, $data);
                if ($this->Users->save($user)) {
                    if (!empty($user->mobile_number)) {
                        $this->loadComponent('Sms');
                        $this->Sms->setMobileNumber($user->mobile_number);
                        $this->Sms->setMessage('Thank you for registration, please click here to activate your account "' . Router::url('/activateEmail/' . base64_encode(base64_encode($user->id)) . '/' . $user->token, true) . '"');
                        $this->Sms->send();
                    }
                    $this->Flash->success(__('We have sent link on your registered mobile number and email to activate your account.'));
                    return $this->redirect('/');
                }
                $this->Flash->error(__('Registeration could not be successfull. Please, try again.'));
            } else {
                $this->Flash->error(__('Email Id or Mobile Number one of them should be presence during registered.'));
            }
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
            $username = $this->request->getData('username');
            if (!empty($username)) {
                $userData = $this->Users->findByUsername($username);
                if ($userData->count()) {
                    $userDetails = $userData->first();
                    $userDetails->token = $this->Users->tokenUpdate($username);
                    $emailObj = $this->getMailer('User');
                    if ($emailObj->send('resetPassword', [$userDetails])) {
                        $this->Flash->success(__('Password reset link has been sent on your email.It will expire in 1 hour'));
                        $this->redirect($this->referer());
                    } else {
                        $this->Flash->error(__('Reset Password could not be sent on your email. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(__('Provided Username not exists.'));
                }
            } else {
                $this->Flash->error(__('Please enter Username.'));
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
            return $this->redirect('/');
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
    function activateByEmail($encodedId, ...$tokenParam) {
        $token = implode('/', $tokenParam);
        $id = base64_decode(base64_decode($encodedId));
        $userDetails = $this->Users->find('all', [
            'conditions' => [
                'id' => $id,
                'token' => $token,
            ]
        ]);
        if (!$userDetails->count()) {
            $this->Flash->error(__('Token expire or not authorized.'));
            return $this->redirect('/');
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
