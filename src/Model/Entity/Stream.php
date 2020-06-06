<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Stream Entity
 *
 * @property int $id
 * @property string $title
 * @property \Cake\I18n\FrozenTime $scheduled
 * @property int $user_id
 * @property string $request_token
 * @property string $verify_token
 * @property \Cake\I18n\FrozenTime $created
 * @property string $ip_address
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\StreamDetail[] $stream_details
 */
class Stream extends Entity {

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
        'title' => true,
        'start_time' => true,
        'end_time' => true,
        'user_id' => true,
        'request_token' => true,
        'verify_token' => true,
        'room_token' => true,
        'broadcaster' => true,
        'created' => true,
        'ip_address' => true,
        'video' => true,
        'audio' => true,
        'screen_share' => true,
        'user' => true,
        'stream_details' => true,
    ];

}
