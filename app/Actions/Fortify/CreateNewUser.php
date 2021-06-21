<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Mail;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/^.*@psuti\.ru/i'],
            'terms_1' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'terms_2' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        $chars = '0123456789QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm!#';
        $max = rand(12, 20);

        $size = strlen($chars)-1;

        $password = '';

        while($max--)
            $password.= $chars[rand(0,$size)];
            
        $email = $input['email'];
        
        $result = Mail::send(['text' => 'mail.mail'], [$email, 'password' => $password], function($message) use ($email){
            $message->to($email);
            $message->subject('Регистрация на сайте '. $_SERVER['SERVER_NAME']);
        });
        
        return User::create([
           // 'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($password),
        ]);
    }
}
