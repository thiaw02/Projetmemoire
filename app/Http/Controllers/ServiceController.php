<?php
namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->paginate(15);
        return view('admin.services.index', compact('services'));
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
}
