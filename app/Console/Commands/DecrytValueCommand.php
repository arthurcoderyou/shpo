<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CustomEncryptor;

class DecrytValueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:decrypt-value-command
                                {value : The custom value}
                            ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decrypt the custom value added';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $value = $this->argument('value');
        $crypt = app(CustomEncryptor::class);

        $decrypted = $crypt->decrypt($value);

        $this->info("Decrypted: {$decrypted}");
    }
}
