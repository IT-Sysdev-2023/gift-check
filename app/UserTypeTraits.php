<?php

namespace App;
use Illuminate\Http\Request;

trait UserTypeTraits
{

    private $request;
    public function __construct(Request $request) {
        $this->request = $request;
    }
    public function userType(string $type)
    {
        return $this->request->user()->usertype === $type; 
    }

    public function userRole(int $role)
    {
        return $this->request->user()->user_role === $role;
    }
}
