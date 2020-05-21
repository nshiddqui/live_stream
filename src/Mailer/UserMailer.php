<?php

namespace App\Mailer;

use Cake\Mailer\Mailer;

class UserMailer extends Mailer {

    public function welcome($user) {
        $this
                ->to($user->email)
                ->emailFormat('html')
                ->subject(sprintf('Welcome %s', $user->name))
                ->template('welcome_mail')
                ->set(['token' => $user->token, 'email' => $user->email]);
    }

    public function resetPassword($user) {
        $this
                ->to($user->email)
                ->emailFormat('html')
                ->subject('Reset password')
                ->template('forgot_password')
                ->set(['token' => $user->token, 'id' => $user->id]);
    }

}
