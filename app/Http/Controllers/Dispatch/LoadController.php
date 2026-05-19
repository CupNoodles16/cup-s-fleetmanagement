<?php

namespace App\Http\Controllers\Dispatch;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LoadController extends Controller
{
    public function index(): View
    {
        return view('dispatch.index');
    }
}
