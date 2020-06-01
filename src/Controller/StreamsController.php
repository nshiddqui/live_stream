<?php

namespace App\Controller;

use App\Controller\AppController;
use DataTables\Controller\DataTablesAjaxRequestTrait;
use Cake\Utility\Hash;

/**
 * Streams Controller
 *
 * @property \App\Model\Table\StreamsTable $Streams
 *
 * @method \App\Model\Entity\Stream[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StreamsController extends AppController {

    use DataTablesAjaxRequestTrait;

    public function initialize() {
        parent::initialize();
        $this->loadComponent('DataTables.DataTables');
        $auth_user_id = $this->request->getSession()->read('Auth.User.id');
        $this->DataTables->createConfig('StreamDetails')
                ->queryOptions([
                    'contain' => ['Streams' => 'Users'],
                    'conditions' => [
                        'StreamDetails.user_id' => $auth_user_id,
                        'Streams.end_time >= NOW()'
                    ]
                ])
                ->databaseColumn('Users.id')
                ->databaseColumn('StreamDetails.stream_id')
                ->databaseColumn('Streams.is_active')
                ->column('Streams.title', ['label' => 'Title', 'orderable' => false])
                ->column('Streams.start_time', ['label' => 'Start Time'])
                ->column('Streams.end_time', ['label' => 'End Time'])
                ->column('Users.name', ['label' => 'Schedule By', 'orderable' => false])
                ->column('actions', ['label' => 'Actions', 'database' => false]);

        $this->DataTables->createConfig('PreviousStreamDetails')
                ->queryOptions([
                    'contain' => ['Streams' => 'Users'],
                    'conditions' => [
                        'StreamDetails.user_id' => $auth_user_id,
                        'Streams.end_time <= NOW()'
                    ]
                ])
                ->table('StreamDetails')
                ->databaseColumn('Users.id')
                ->databaseColumn('StreamDetails.stream_id')
                ->databaseColumn('Streams.is_active')
                ->column('Streams.title', ['label' => 'Title', 'orderable' => false])
                ->column('Streams.start_time', ['label' => 'Start Time'])
                ->column('Streams.end_time', ['label' => 'End Time'])
                ->column('Users.name', ['label' => 'Schedule By', 'orderable' => false]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index() {
        $this->DataTables->setViewVars('StreamDetails');
    }

    public function previous() {
        $this->DataTables->setViewVars('PreviousStreamDetails');
    }

    /**
     * View method
     *
     * @param string|null $id Stream id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $stream = $this->Streams->get($id, [
            'contain' => ['Users', 'StreamDetails'],
        ]);

        $this->set('stream', $stream);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $stream = $this->Streams->newEntity();
        if ($this->request->is('post')) {
            $stream = $this->Streams->patchEntity($stream, $this->request->getData());
            if ($this->Streams->save($stream)) {
                $this->Flash->success(__('The stream has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The stream could not be saved. Please, try again.'));
        }
        $users = $this->Streams->Users->find('list', ['limit' => 200]);
        $streamDetails = $this->Streams->StreamDetails->find('list', ['limit' => 200]);
        $this->set(compact('stream', 'users', 'streamDetails'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Stream id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null) {
        $stream = $this->Streams->get($id, [
            'contain' => ['StreamDetails'],
        ]);
        $user_ids = Hash::extract($this->Auth->user('user_friends'), '{n}.friend_id');
        $selected_users = Hash::extract($stream, 'stream_details.{n}.user_id');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (!empty($data['emails']) && !empty($user_ids)) {
                $data['start_time'] = $this->dateFormatSql($data['start_time']);
                $data['end_time'] = $this->dateFormatSql($data['end_time']);
                $stream = $this->Streams->patchEntity($stream, $data);
                if ($this->Streams->save($stream)) {
                    $this->loadModel('StreamDetails');
                    $this->StreamDetails->deleteAll(['stream_id' => $stream->id]);
                    $streamDetails = ['stream_id' => $stream->id];
                    $streamDetails['user_id'] = $this->Auth->user('id');
                    $EntityStreamDetails = $this->StreamDetails->newEntity($streamDetails);
                    $this->StreamDetails->save($EntityStreamDetails);
                    foreach ($data['emails'] as $userId) {
                        if (!in_array($userId, $user_ids)) {
                            continue;
                        }
                        $streamDetails['user_id'] = $userId;
                        $EntityStreamDetails = $this->StreamDetails->newEntity($streamDetails);
                        $this->StreamDetails->save($EntityStreamDetails);
                    }
                    $this->Flash->success(__('The stream has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The stream could not be saved. Please, try again.'));
            } else {
                $this->Flash->error(__('Please add emails for schedule stream.'));
            }
        }
        $emails = array();
        if (!empty($user_ids)) {
            foreach ($this->Auth->user('user_friends') as $user_id) {
                $emails[$user_id['group']][$user_id['friend']['id']] = $user_id['friend']['email'];
            }
        }
        $this->set(compact('stream', 'emails', 'selected_users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Stream id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $stream = $this->Streams->get($id);
        if ($this->Streams->delete($stream)) {
            $this->Flash->success(__('The stream has been deleted.'));
        } else {
            $this->Flash->error(__('The stream could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
