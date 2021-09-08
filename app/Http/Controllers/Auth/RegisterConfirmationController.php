<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterConfirmationController extends Controller
{
    public function index()
    {
        User::where('confirmation_token', request('token'))
            ->firstOrFail()
            ->confirm();

        return redirect('/threads')
            ->with('flash', 'Your account is now confirmed! You can post to the Forum.');
    }
}
