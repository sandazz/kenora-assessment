<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Admin sees only user.* events; manager sees workshop.* and registration.*
        if ($user->role === Role::Admin) {
            $prefixes = ['user.'];
        } elseif ($user->role === Role::Manager) {
            $prefixes = ['workshop.', 'registration.'];
        } else {
            abort(403);
        }

        $query = AuditLog::with('user')->orderByDesc('created_at');

        $query->where(function ($q) use ($prefixes) {
            foreach ($prefixes as $prefix) {
                $q->orWhere('action', 'like', $prefix.'%');
            }
        });

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('audit', compact('logs'));
    }
}
