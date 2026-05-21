<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoLoginController extends Controller
{
    private const DEMO_USERS = [
        'admin' => ['email' => 'admin@edunexus.test', 'dashboard' => 'admin.dashboard'],
        'member' => ['email' => 'ana.reyes@edunexus.test', 'dashboard' => 'member.dashboard'],
        'merchant' => ['email' => 'lipa.supplies@edunexus.test', 'dashboard' => 'merchant.dashboard'],
        'auditor' => ['email' => 'auditor@edunexus.test', 'dashboard' => 'auditor.dashboard'],
    ];

    public function __invoke(Request $request, string $role): RedirectResponse
    {
        abort_unless(config('app.demo_mode'), 403);
        abort_unless(array_key_exists($role, self::DEMO_USERS), 404);

        $demoUser = self::DEMO_USERS[$role];

        $user = User::query()
            ->where('email', $demoUser['email'])
            ->where('role', $role)
            ->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route($demoUser['dashboard'])
            ->with('success', ucfirst($role) . ' demo portal ready for judging.');
    }
}
