<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Services\CustomEncryptor;
use Illuminate\Support\Facades\Hash;

class CipherUserWithId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cipher-user-with-id
                            {id : The custom id of the user}
                            {password : password of the user to encrypt}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cipher user password using id ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $password = $this->argument('password');

        User::unguard();

        $user = User::find($id);

        $crypt = app(CustomEncryptor::class);


        $user->user = $crypt->encrypt($password);
        $user->password = Hash::make($password); 
        $user->save();

        // Restore protection
        User::reguard();

    }
}
