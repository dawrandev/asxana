<?php

namespace App\Services;

use App\Repositories\ClientRepository;

class ClientService
{
    public function __construct(
        protected ClientRepository $clientRepository
    ) {
        //
    }
}
