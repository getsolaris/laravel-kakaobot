<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeyboardController extends Controller
{
    public function index() {
        return response()->json([
            'type' => 'buttons',
            'buttons' => ['급식', '학교 일정']
        ]);
    }
}
