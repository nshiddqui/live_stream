<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Network\Session;
use Cake\Routing\Router;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Streams Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\StreamDetailsTable&\Cake\ORM\Association\HasMany $StreamDetails
 *
 * @method \App\Model\Entity\Stream get($primaryKey, $options = [])
 * @method \App\Model\Entity\Stream newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Stream[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Stream|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Stream saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Stream patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Stream[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Stream findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StreamsTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->setTable('streams');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('StreamDetails', [
            'foreignKey' => 'id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
                ->integer('id')
                ->allowEmptyString('id', null, 'create');

        $validator
                ->scalar('title')
                ->maxLength('title', 255)
                ->requirePresence('title', 'create')
                ->notEmptyString('title');

        $validator
                ->dateTime('start_time')
                ->requirePresence('start_time', 'create')
                ->notEmptyDateTime('start_time');
        $validator
                ->dateTime('end_time')
                ->requirePresence('end_time', 'create')
                ->notEmptyDateTime('end_time');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    protected function _insert($entity, $data) {
        $session = new Session();
        $userId = $session->read('Auth.User.id');
        $data['user_id'] = $userId;
        $data['ip_address'] = Router::getRequest()->clientIp();
        $data['request_token'] = (new DefaultPasswordHasher)->hash(date('Y-m-d'));
        $data['verify_token'] = (new DefaultPasswordHasher)->hash($userId);
        return parent::_insert($entity, $data);
    }

}
