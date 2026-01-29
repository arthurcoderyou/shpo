<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CustomEncryptor;

class EncryptValueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:encrypt-value-command
                                {value : The custom value}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt the custom value added';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $value = $this->argument('value');
        $crypt = app(CustomEncryptor::class);

        $encrypted = $crypt->encrypt($value);

        $this->info("Encrypted: {$encrypted}");
    }
}
