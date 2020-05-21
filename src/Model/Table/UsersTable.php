<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Mailer\MailerAwareTrait;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        $this->hasMany('UserFriends', [
            'foreignKey' => 'user_id',
        ]);

        $this->addBehavior('Timestamp');
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
                ->scalar('role')
                ->notEmptyString('role');

        $validator
                ->scalar('name')
                ->maxLength('name', 255)
                ->requirePresence('name', 'create')
                ->notEmptyString('name');

        $validator
                ->email('email')
                ->requirePresence('email', 'create')
                ->notEmptyString('email')
                ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'Provided email already registered.']);

        $validator
                ->add('mobile_number', 'custom_phone_validate', [
                    'rule' => 'numeric',
                    'message' => 'Phone number should be number'
                ])
                ->requirePresence('mobile_number', 'create')
                ->notEmptyString('mobile_number')
                ->add('mobile_number', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'Provided mobile number already registered.']);

        $validator
                ->scalar('password')
                ->maxLength('password', 255)
                ->requirePresence('password', 'create')
                ->minLength('password', 8, 'Length must be minimum 8 characters.')
                ->add('password', 'custom', [
                    'rule' => function ($value) {
                        $uppercase = preg_match('@[A-Z]@', $value);
                        $lowercase = preg_match('@[a-z]@', $value);
                        $number = preg_match('@[0-9]@', $value);
                        $specialChars = preg_match('@[^\w]@', $value);

                        if (!$uppercase || !$lowercase || !$number || !$specialChars) {
                            return false;
                        } else {
                            return true;
                        }
                    },
                    'message' => 'Password Does Not Meet Requirements.'
                ])
                ->notEmptyString('password');



        $validator->add('confirm_password', 'custom', [
            'rule' => function ($value, $context) {
                return !strcmp($value, $context['data']['password']);
            },
            'message' => 'Passwords does not match.'
        ]);

        $validator
                ->scalar('token')
                ->maxLength('token', 255)
                ->allowEmptyString('token');

        $validator
                ->scalar('ip_address')
                ->maxLength('ip_address', 255)
                ->allowEmptyString('ip_address');

        $validator
                ->scalar('is_active')
                ->notEmptyString('is_active');

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
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }

    public function findAuth(\Cake\ORM\Query $query, array $options) {
        $query->contain(['UserFriends' => ['Friends']])
                ->where(['Users.is_active' => 1]);

        return $query;
    }

    public function tokenUpdate($emailId) {
        $token = (new DefaultPasswordHasher)->hash(date('Y-m-d'));
        $this->updateAll([
            'token' => $token,
            'token_expire' => date('Y-m-d h:i:s')
                ], [
            'email' => $emailId
        ]);
        return $token;
    }

    public function implementedEvents() {
        return [
            'Model.afterSave' => 'onRegistration'
        ];
    }

    use MailerAwareTrait;

    public function onRegistration($event, $entity, $options) {
        if ($entity->isNew()) {
            $entity->token = $this->tokenUpdate($entity->email);
            $this->getMailer('User')->send('welcome', [$entity]);
        }
    }

    protected function _insert($entity, $data) {
        $data['stream_token'] = (new DefaultPasswordHasher)->hash(date('Y-m-d'));
        return parent::_insert($entity, $data);
    }

    protected function _update($entity, $data) {
//        $data['stream_token'] = (new DefaultPasswordHasher)->hash(date('Y-m-d'));
        return parent::_update($entity, $data);
    }

}
