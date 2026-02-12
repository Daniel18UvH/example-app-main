<x-layouts::app :title="__('Dashboard')">
    @php
        $user = auth()->user();
        $isAdmin = in_array($user->email, ['satafykerplay@gmail.com', 'admin@prueba.com']);
        $employee = \App\Models\Employee::where('email', $user->email)->first();
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-4">
        
        {{-- ESTADÍSTICAS SUPERIORES --}}
        <div class="grid gap-4 md:grid-cols-3">
            @if($isAdmin)
                <div class="relative aspect-video rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-900/50">
                    <p class="text-xs font-bold uppercase text-zinc-500">Total Clientes</p>
                    <p class="mt-2 text-4xl font-black text-zinc-900 dark:text-white">{{ $totalClients }}</p>
                </div>
                <div class="relative aspect-video rounded-xl border border-zinc-200 p-6 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-900/50">
                    <p class="text-xs font-bold uppercase text-zinc-500">Proyectos Activos</p>
                    <p class="mt-2 text-4xl font-black text-zinc-900 dark:text-white">{{ $activeProjects }}</p>
                </div>
            @else
                <div class="relative aspect-video rounded-xl border border-blue-200 p-6 dark:border-blue-900/30 bg-blue-50/30 dark:bg-blue-900/10">
                    <p class="text-xs font-bold uppercase text-blue-600 dark:text-blue-400">En Proceso</p>
                    <p class="mt-2 text-4xl font-black text-zinc-900 dark:text-white">
                        {{ $employee ? $employee->projects()->where('status', 'En proceso')->count() : 0 }}
                    </p>
                </div>
                <div class="relative aspect-video rounded-xl border border-green-200 p-6 dark:border-green-900/30 bg-green-50/30 dark:bg-green-900/10">
                    <p class="text-xs font-bold uppercase text-green-600 dark:text-green-400">Finalizados</p>
                    <p class="mt-2 text-4xl font-black text-zinc-900 dark:text-white">
                        {{ $employee ? $employee->projects()->where('status', 'Finalizado')->count() : 0 }}
                    </p>
                </div>
            @endif

            <div class="relative aspect-video rounded-xl border border-zinc-200 dark:border-zinc-700 flex items-center justify-center bg-zinc-50/30 dark:bg-zinc-800/30">
                @if($isAdmin)
                    <flux:button href="{{ route('clients.create') }}" icon="plus" variant="primary">Registrar Cliente</flux:button>
                @else
                    <div class="text-center">
                        <p class="text-[10px] uppercase text-zinc-500">Bienvenido al equipo</p>
                        <p class="text-sm font-bold dark:text-zinc-200">{{ $user->name }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- TABLA DE REGISTROS (SIN BG-WHITE) --}}
        <div class="relative flex-1 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="flex h-full flex-col">
                <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-900/50">
                    <h3 class="font-bold text-zinc-900 dark:text-white">
                        {{ $isAdmin ? 'Últimos Registros' : 'Mis Proyectos Asignados' }}
                    </h3>
                </div>

                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-zinc-100/50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400">
                            @if($isAdmin)
                                <tr>
                                    <th class="px-6 py-3 font-medium">Nombre</th>
                                    <th class="px-6 py-3 font-medium">Empresa</th>
                                    <th class="px-6 py-3 font-medium">Estado</th>
                                    <th class="px-6 py-3 font-medium text-right">Fecha</th>
                                </tr>
                            @else
                                <tr>
                                    <th class="px-6 py-3 font-medium">Proyecto</th>
                                    <th class="px-6 py-3 font-medium">Descripción</th>
                                    <th class="px-6 py-3 font-medium">Estado</th>
                                    <th class="px-6 py-3 font-medium text-right">Asignado</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @if($isAdmin)
                                @forelse($recentClients as $client)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                        <td class="px-6 py-3 font-medium dark:text-white">{{ $client->name }}</td>
                                        <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400">{{ $client->company ?? 'Particular' }}</td>
                                        <td class="px-6 py-3"><flux:badge size="sm" :color="$client->status === 'Activo' ? 'green' : 'zinc'">{{ $client->status }}</flux:badge></td>
                                        <td class="px-6 py-3 text-right text-zinc-400">{{ $client->created_at->format('d M') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-12 text-center text-zinc-500 italic">No hay registros.</td></tr>
                                @endforelse
                            @else
                                @if($employee)
                                    @forelse($employee->projects as $project)
                                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                            <td class="px-6 py-3 font-medium dark:text-white">{{ $project->name }}</td>
                                            <td class="px-6 py-3 text-zinc-500 dark:text-zinc-400 text-xs">{{ $project->description }}</td>
                                            <td class="px-6 py-3"><flux:badge size="sm" :color="$project->status === 'Finalizado' ? 'green' : 'zinc'">{{ $project->status }}</flux:badge></td>
                                            <td class="px-6 py-3 text-right text-zinc-400">{{ $project->created_at->format('d M') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="px-6 py-12 text-center text-zinc-500 italic">No tienes proyectos.</td></tr>
                                    @endforelse
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>