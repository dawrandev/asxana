<?php

namespace App\Repositories;

class ClientRepository
{
    public function getClients(int $perpage)
    {
        return \App\Models\Client::paginate($perpage);
    }
}
