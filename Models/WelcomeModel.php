<?php

namespace Api\Models;

use Api\Bases\Model;

class WelcomeModel extends Model
{
    public function get()
    {
        $this->data['data'] = "test_data";
    }
}
