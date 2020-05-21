<?php

namespace App\Controller;

use App\Controller\AppController;

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
        $this->DataTables->createConfig('Streams')
                ->queryOptions([
                    'contain' => ['Users', 'StreamDetails'],
                    'conditions' => [
                        'or' => [
                            'Streams.user_id' => $auth_user_id,
                            'StreamDetails.user_id' => $auth_user_id
                        ],
                        'Streams.end_time > NOW()'
                    ]
                ])
                ->databaseColumn('Users.id')
                ->databaseColumn('StreamDetails.id')
                ->column('Streams.title', ['label' => 'Title', 'orderable' => false])
                ->column('Streams.start_time', ['label' => 'Start Time'])
                ->column('Streams.end_time', ['label' => 'End Time'])
                ->column('Users.name', ['label' => 'Schedule By', 'orderable' => false])
                ->column('actions', ['label' => 'Actions', 'database' => false]);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index() {
        $this->paginate = [
            'contain' => ['Users', 'StreamDetails'],
        ];
        $streams = $this->paginate($this->Streams);

        $this->set(compact('streams'));
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
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
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
