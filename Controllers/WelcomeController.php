<?php

namespace Api\Controllers;

use Api\Bases\Controller;
use Api\Models\WelcomeModel;

class WelcomeController extends Controller
{
    public function get()
    {
        $model = new WelcomeModel();
        $model->get();
        $model->response();
    }
}
