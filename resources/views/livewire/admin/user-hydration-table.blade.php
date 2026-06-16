<div class="min-h-screen bg-slate-50/50 p-8" wire:poll.3s>
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau performa hidrasi harian dan data log semua pengguna secara real-time.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <svg wire:loading.remove wire:target="search" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <svg wire:loading wire:target="search" class="animate-spin w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari pengguna..." class="w-64 pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-400 text-slate-700 shadow-sm">
            </div>

            <select wire:model.live="status" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-600 shadow-sm cursor-pointer">
                <option value="all">Semua Status</option>
                <option value="achieved">Target Tercapai ✨</option>
                <option value="low">Kurang Hidrasi 💧</option>
            </select>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-100/40 overflow-hidden relative">
        
        <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center transition-all"></div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Target Harian</th>
                        <th class="px-6 py-4">Diminum Hari Ini</th>
                        <th class="px-6 py-4">Progres Air</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700 font-medium">
                    @forelse($users as $user)
                        @php
                            // Hitung persentase hidrasi dan amankan dari pembagian dengan nol (division by zero)
                            $totalDrunk = $user->total_drunk ?? 0;
                            $target = $user->target ?? 2000;
                            $calcPercent = $target > 0 ? round(($totalDrunk / $target) * 100) : 0;
                            $isAchieved = $totalDrunk >= $target;
                            
                            // Inisial Nama untuk Avatar Backup
                            $initials = collect(explode(' ', $user->name))->map(fn($n) => mb_substr($n, 0, 1))->take(2)->join('');
                        @endphp

                        <tr class="hover:bg-slate-50/50 transition-colors" wire:key="user-row-{{ $user->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($isAchieved)
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-cyan-400 flex items-center justify-center text-white font-bold text-sm shadow-sm shadow-blue-500/20">
                                            {{ strtoupper($initials) }}
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-sm">
                                            {{ strtoupper($initials) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="block text-slate-800 font-bold">{{ $user->name }}</span>
                                        <span class="block text-xs text-slate-400 font-normal">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ number_format($target) }} ml</td>
                            <td class="px-6 py-4 {{ $isAchieved ? 'text-blue-600 font-bold' : 'text-slate-700 font-bold' }}">
                                {{ number_format($totalDrunk) }} ml
                            </td>
                            <td class="px-6 py-4 w-64">
                                <div class="flex items-center gap-3">
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $isAchieved ? 'bg-gradient-to-r from-cyan-400 to-blue-500' : 'bg-amber-400' }}" 
                                             style="width: {{ min($calcPercent, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 font-bold">{{ $calcPercent }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($isAchieved)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Tercapai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 ring-1 ring-amber-600/10">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Kurang Minum
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="#" class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Tidak ada data pengguna yang cocok dengan kriteria.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $users->links() }}
        </div>
    </div>
</div>