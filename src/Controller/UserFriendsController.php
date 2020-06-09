<?php

namespace App\Controller;

use App\Controller\AppController;
use DataTables\Controller\DataTablesAjaxRequestTrait;
use Cake\Utility\Security;
use Cake\Core\Configure;

/**
 * UserFriends Controller
 *
 * @property \App\Model\Table\UserFriendsTable $UserFriends
 *
 * @method \App\Model\Entity\UserFriend[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserFriendsController extends AppController {

    use DataTablesAjaxRequestTrait;

    public function initialize() {
        parent::initialize();
        $this->loadComponent('DataTables.DataTables');
        $auth_user_id = $this->request->getSession()->read('Auth.User.id');
        $this->DataTables->createConfig('UserFriends')
                ->queryOptions([
                    'contain' => ['Friends'],
                    'conditions' => [
                        'UserFriends.user_id' => $auth_user_id
                    ]
                ])
                ->databaseColumn('UserFriends.id')
                ->column('Friends.name', ['label' => 'Name'])
                ->column('Friends.email', ['label' => 'Email ID'])
                ->column('UserFriends.group', ['label' => 'Group'])
                ->column('actions', ['label' => 'Actions', 'database' => false]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($id = null) {
        if ($this->request->is('post')) {
            $username = $this->request->getData('username');
            if (!empty($username)) {
                $this->initializeFrind($username);
            }
        }
        $this->DataTables->setViewVars('UserFriends');
    }

    /**
     * Initial Friend method
     *
     * @param string|null $email User Friend email.
     * @return null| Redirects to index.
     */
    protected function initializeFrind($username) {
        $this->loadModel('Users');
        $friendUsername = $this->Users->findByUsername($username);
        if ($friendUsername->count()) {
            $friendUsername = $friendUsername->first();
            if ($friendUsername->id !== $this->Auth->user('id')) {
                $this->addFrind($friendUsername);
            } else {
                $this->Flash->error(__('You cant set your email as your friend.'));
            }
        } else {
            $this->Flash->error(__('Your Friend does not exists in our database.'));
        }
    }

    /**
     * Add Friend method
     *
     * @param string|null $friendEmail User Friend Object.
     * @return null| Redirects to index.
     */
    protected function addFrind($friendUsername) {
        $alreadyExists = $this->UserFriends->find('all', [
            'conditions' => [
                'user_id' => $this->Auth->user('id'),
                'friend_id' => $friendUsername->id
            ]
        ]);
        if (!$alreadyExists->count()) {
            $UserFriend = $this->UserFriends->newEntity();
            $data = [
                'user_id' => $this->Auth->user('id'),
                'group' => $this->request->getData('group'),
                'friend_id' => $friendUsername->id
            ];
            $UserFriends = $this->UserFriends->patchEntity($UserFriend, $data);
            $this->UserFriends->save($UserFriends);
            $this->_refreshAuth();
            $this->Flash->success(__('Your friend added in your friend list.'));
            $this->redirect($this->referer());
        } else {
            $this->Flash->error(__('This user already in your friend list.'));
        }
    }

    public function edit($id = null) {
        $user_friend = $this->UserFriends->get($id, [
            'contain' => ['Friends'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            unset($data['friend']);
            $user_friend = $this->UserFriends->patchEntity($user_friend, $data);
            if ($this->UserFriends->save($user_friend)) {
                $this->_refreshAuth();
                $this->Flash->success(__('Your friend has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Your friend could not be updated. Please, try again.'));
        }
        $this->set(compact('user_friend'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User Friend id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $secureId = Security::decrypt($id, Configure::read('SECURITY_KEY'));
        $this->request->allowMethod(['post', 'delete']);
        $userFriend = $this->UserFriends->get($secureId);
        if ($this->UserFriends->delete($userFriend)) {
            $this->_refreshAuth();
            $this->Flash->success(__('The user friend has been deleted.'));
        } else {
            $this->Flash->error(__('The user friend could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
