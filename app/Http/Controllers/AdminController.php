<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsCache;
use App\Models\Port;
use App\Models\Country;
use App\Models\Watchlist;

class AdminController extends Controller
{
    // Dashboard Admin
    public function index()
    {
        $stats = [
            'countries' => Country::count(),
            'news' => NewsCache::count(),
            'ports' => Port::count(),
            'watchlists' => Watchlist::count(),
        ];
        
        return view('admin.index', compact('stats'));
    }

    // ===== NEWS MANAGEMENT =====
    public function news()
    {
        $news = NewsCache::with('country')->latest('published_at')->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function createNews()
    {
        $countries = Country::all();
        return view('admin.news.create', compact('countries'));
    }

    public function storeNews(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source' => 'required|string',
            'published_at' => 'required|date',
            'sentiment' => 'required|in:Positive,Negative,Neutral',
            'country_id' => 'nullable|exists:countries,id'
        ]);

        NewsCache::create($request->all());

        return redirect()->route('admin.news')->with('success', 'News added successfully!');
    }

    public function editNews($id)
    {
        $news = NewsCache::findOrFail($id);
        $countries = Country::all();
        return view('admin.news.edit', compact('news', 'countries'));
    }

    public function updateNews(Request $request, $id)
    {
        $news = NewsCache::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source' => 'required|string',
            'published_at' => 'required|date',
            'sentiment' => 'required|in:Positive,Negative,Neutral',
            'country_id' => 'nullable|exists:countries,id'
        ]);

        $news->update($request->all());

        return redirect()->route('admin.news')->with('success', 'News updated successfully!');
    }

    public function deleteNews($id)
    {
        $news = NewsCache::findOrFail($id);
        $news->delete();

        return redirect()->route('admin.news')->with('success', 'News deleted successfully!');
    }

    // ===== PORT MANAGEMENT =====
    public function ports()
    {
        $ports = Port::with('country')->latest()->paginate(20);
        return view('admin.ports.index', compact('ports'));
    }

    public function createPort()
    {
        $countries = Country::all();
        return view('admin.ports.create', compact('countries'));
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:Active,Inactive,Maintenance'
        ]);

        Port::create($request->all());

        return redirect()->route('admin.ports')->with('success', 'Port added successfully!');
    }

    public function editPort($id)
    {
        $port = Port::findOrFail($id);
        $countries = Country::all();
        return view('admin.ports.edit', compact('port', 'countries'));
    }

    public function updatePort(Request $request, $id)
    {
        $port = Port::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:Active,Inactive,Maintenance'
        ]);

        $port->update($request->all());

        return redirect()->route('admin.ports')->with('success', 'Port updated successfully!');
    }

    public function deletePort($id)
    {
        $port = Port::findOrFail($id);
        $port->delete();

        return redirect()->route('admin.ports')->with('success', 'Port deleted successfully!');
    }
        // ============ USER MANAGEMENT ============
    
    public function users()
    {
        $users = \App\Models\User::all();
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user'
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,user'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('admin.users')->with('success', 'User berhasil diupdate!');
    }

    public function deleteUser($id)
    {
        \App\Models\User::findOrFail($id)->delete();
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus!');
    }
}