<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::withCount('users')
            ->with(['users' => function($q){ $q->select('id','name','email','role','service_id')->orderBy('name'); }])
            ->orderBy('name')
            ->paginate(15);
        $totalServices = Service::count();
        $allServices = Service::orderBy('name')->get(['id','name']);
        return view('admin.services.index', compact('services','totalServices','allServices'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:services,name'],
            'description' => ['nullable','string'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = (bool)($data['active'] ?? true);
        Service::create($data);
        if ($request->boolean('redirect_back')) {
            return back()->with('success','Service créé');
        }
        return redirect()->route('admin.services.index')->with('success','Service créé');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:services,name,'.$service->id],
            'description' => ['nullable','string'],
            'active' => ['nullable','boolean'],
        ]);
        $data['active'] = (bool)($data['active'] ?? $service->active);
        $service->update($data);
        if ($request->boolean('redirect_back')) {
            return back()->with('success','Service mis à jour');
        }
        return redirect()->route('admin.services.index')->with('success','Service mis à jour');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success','Service supprimé');
    }

    /**
     * Changer le service d'un utilisateur.
     */
    public function changeUserService(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
            'service_id' => ['nullable','integer','exists:services,id'],
        ]);
        $user = User::findOrFail($data['user_id']);
        $user->update(['service_id' => $data['service_id'] ?? null]);
        return back()->with('success', 'Service de l\'utilisateur mis à jour');
    }
}
