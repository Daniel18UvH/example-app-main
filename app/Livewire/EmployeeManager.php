<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmployeeMail;

class EmployeeManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Variables para Empleado
    public $employeeId, $full_name, $email, $position, $phone, $tags, $status = 'Activo';
    public $isOpen = false;
    public $isViewing = false;

    // Variables para Asignar Proyecto
    public $isAssigning = false;
    public $projectName, $projectDescription;

    protected $adminEmails = ['satafykerplay@gmail.com', 'admin@prueba.com'];

    public function render()
    {
        // Cargamos 'user' (encargado) y contamos 'projects'
        $query = Employee::with('user')->withCount('projects'); 
        
        if (!in_array(auth()->user()->email, $this->adminEmails)) {
            $query->where('email', auth()->user()->email);
        }

        return view('livewire.employee-manager', [
            'employees' => $query->where(function($q) {
                    $q->where('full_name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%')
                      ->orWhere('tags', 'like', '%'.$this->search.'%');
                })
                ->latest()
                ->paginate(10)
        ]);
    }

    // Función para guardar o actualizar empleado
    public function store()
    {
        if (!in_array(auth()->user()->email, $this->adminEmails)) return;

        $this->validate([
            'full_name' => 'required|min:3',
            'email' => 'required|email',
            'position' => 'required',
        ]);

        DB::transaction(function () {
            if (is_null($this->employeeId)) {
                if (!User::where('email', $this->email)->exists()) {
                    User::create([
                        'name' => $this->full_name,
                        'email' => $this->email,
                        'password' => Hash::make('12345678'),
                    ]);
                }
            }

            Employee::updateOrCreate(['id' => $this->employeeId], [
                'full_name' => $this->full_name,
                'email' => $this->email,
                'position' => $this->position,
                'phone' => $this->phone,
                'tags' => $this->tags,
                'status' => $this->status,
                'user_id' => auth()->id(),
            ]);
        });

        session()->flash('message', 'Empleado procesado correctamente.');
        $this->closeModal();
    }

    // NUEVA FUNCIÓN: Asignar Proyecto
    public function openAssignModal($id)
    {
        $this->employeeId = $id;
        $this->projectName = '';
        $this->projectDescription = '';
        $this->isAssigning = true;
    }

    public function saveProject()
    {
        $this->validate([
            'projectName' => 'required|min:3',
        ]);

        Project::create([
            'name' => $this->projectName,
            'description' => $this->projectDescription,
            'status' => 'En proceso',
            'employee_id' => $this->employeeId,
        ]);

        session()->flash('message', 'Proyecto asignado con éxito.');
        $this->isAssigning = false;
        $this->reset(['projectName', 'projectDescription']);
    }

    public function delete($id)
    {
        if (!in_array(auth()->user()->email, $this->adminEmails)) return;
        $employee = Employee::findOrFail($id);
        User::where('email', $employee->email)->delete();
        $employee->delete();
        session()->flash('message', 'Empleado eliminado.');
    }

    public function create() { $this->resetInput(); $this->isOpen = true; }
    public function edit($id) { $this->isViewing = false; $this->loadEmployee($id); }
    public function show($id) { $this->isViewing = true; $this->loadEmployee($id); }
    public function closeModal() { $this->isOpen = false; $this->isAssigning = false; $this->resetInput(); }

    private function loadEmployee($id) {
        $employee = Employee::findOrFail($id);
        $this->employeeId = $id;
        $this->full_name = $employee->full_name;
        $this->email = $employee->email;
        $this->position = $employee->position;
        $this->phone = $employee->phone;
        $this->tags = $employee->tags;
        $this->status = $employee->status;
        $this->isOpen = true;
    }

    private function resetInput() {
        $this->reset(['full_name', 'email', 'position', 'phone', 'tags', 'employeeId', 'isViewing', 'isAssigning']);
        $this->status = 'Activo';
    }
}