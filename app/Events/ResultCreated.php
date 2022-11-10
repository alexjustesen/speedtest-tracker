<?php

namespace App\Events;

use App\Models\Result;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResultCreated
{
    use Dispatchable, SerializesModels;

    public $result;

    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Result $result)
    {
        $this->result = $result;

        $this->user = User::find(1); // find the admin user, this isn't ideal but oh well
    }
}
