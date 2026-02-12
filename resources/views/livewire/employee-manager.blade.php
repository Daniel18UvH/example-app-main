<div class="p-6">
    @php
        $isAdmin = in_array(auth()->user()->email, ['satafykerplay@gmail.com', 'admin@prueba.com']);
    @endphp

    <flux:header>
        <flux:heading size="xl">{{ __('Gestión de Empleados') }}</flux:heading>
        <flux:spacer />
        @if($isAdmin)
            <flux:button wire:click="create" variant="primary" icon="plus">Nuevo Empleado</flux:button>
        @endif
    </flux:header>

    <div class="mt-8 space-y-6">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar..." />

        @if (session()->has('message'))
            <flux:badge variant="success" size="lg" class="w-full justify-center">{{ session('message') }}</flux:badge>
        @endif

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Nombre') }}</flux:table.column>
                <flux:table.column>{{ __('Puesto') }}</flux:table.column>
                <flux:table.column>{{ __('Encargado') }}</flux:table.column>
                <flux:table.column>{{ __('Proyectos') }}</flux:table.column> {{-- COLUMNA DE CONTEO --}}
                <flux:table.column>{{ __('Estado') }}</flux:table.column>
                <flux:table.column align="end">{{ __('Acciones') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($employees as $employee)
                    <flux:table.row :key="$employee->id" class="dark:bg-zinc-900/40">
                        <flux:table.cell class="font-medium dark:text-white">{{ $employee->full_name }}</flux:table.cell>
                        <flux:table.cell class="dark:text-zinc-400">{{ $employee->position }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="text-[10px] uppercase font-bold px-2 py-1 rounded bg-zinc-100 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $employee->user->name ?? 'Sistema' }}
                            </span>
                        </flux:table.cell>

                        {{-- CONTEO DE PROYECTOS --}}
                        <flux:table.cell>
                            <flux:badge color="blue" size="sm" inset="left">{{ $employee->projects_count }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge :color="$employee->status === 'Activo' ? 'success' : 'warning'" size="sm">{{ $employee->status }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:button.group>
                                @if($isAdmin)
                                    {{-- BOTÓN PARA ASIGNAR PROYECTO --}}
                                    <flux:button wire:click="openAssignModal({{ $employee->id }})" variant="ghost" icon="briefcase" size="sm" />
                                    <flux:button wire:click="edit({{ $employee->id }})" variant="ghost" icon="pencil-square" size="sm" />
                                    <flux:button wire:click="delete({{ $employee->id }})" wire:confirm="¿Borrar?" variant="ghost" icon="trash" color="danger" size="sm" />
                                @endif
                                <flux:button wire:click="show({{ $employee->id }})" variant="ghost" icon="eye" size="sm" />
                            </flux:button.group>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-10 italic text-zinc-500">No hay registros.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- MODAL 1: GESTIÓN DE EMPLEADO --}}
    <flux:modal wire:model="isOpen" class="min-w-[30rem]">
        <div class="space-y-6">
            <flux:heading size="lg">{{ $isViewing ? 'Detalle' : 'Empleado' }}</flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <flux:input label="Nombre" wire:model="full_name" :disabled="$isViewing" />
                <flux:input label="Email" wire:model="email" :disabled="$isViewing" />
                <flux:input label="Puesto" wire:model="position" :disabled="$isViewing" />
                <flux:select label="Estado" wire:model="status" :disabled="$isViewing">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </flux:select>
            </div>
            <div class="flex gap-2 justify-end">
                <flux:button wire:click="closeModal" variant="ghost">Cerrar</flux:button>
                @if(!$isViewing && $isAdmin) <flux:button wire:click="store" variant="primary">Guardar</flux:button> @endif
            </div>
        </div>
    </flux:modal>

    {{-- MODAL 2: ASIGNAR PROYECTO --}}
    <flux:modal wire:model="isAssigning" class="min-w-[25rem]">
        <div class="space-y-6">
            <flux:heading size="lg">Asignar Nuevo Proyecto</flux:heading>
            <flux:input label="Nombre del Proyecto" wire:model="projectName" placeholder="Ej: Auditoría Solar" />
            <flux:textarea label="Descripción" wire:model="projectDescription" placeholder="Detalles del proyecto..." />
            <div class="flex gap-2 justify-end">
                <flux:button wire:click="closeModal" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="saveProject" variant="primary">Asignar ahora</flux:button>
            </div>
        </div>
    </flux:modal>
</div>