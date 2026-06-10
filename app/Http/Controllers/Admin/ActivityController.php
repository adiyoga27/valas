<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }

        $activities = $query->paginate(25)->withQueryString();

        return view('admin.activities.index', compact('activities'));
    }
}
