<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserFriend Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $friend_id
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Friend $friend
 */
class UserFriend extends Entity {

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'friend_id' => true,
        'group' => true,
        'created' => true,
        'user' => true,
        'friend' => true,
    ];

}
