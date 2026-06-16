<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User; // Pastikan model User sudah sesuai

class UserHydrationTable extends Component
{
    use WithPagination;

    // State untuk input pencarian dan filter dropdown
    public $search = '';
    public $status = 'all';

    // Otomatis reset halaman pagination ke angka 1 jika user mengetik/mengubah filter
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Jalankan query dasar (Sesuaikan kolom total_drunk & target dengan schema DB kamu)
        $query = User::query();

        // 1. Logika Pencarian Nama / Email
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // 2. Logika Filter Status Hidrasi
        if ($this->status === 'achieved') {
            // Target tercapai jika total air minum >= target harian
            $query->whereRaw('total_drunk >= target');
        } elseif ($this->status === 'low') {
            // Kurang minum jika total air minum < target harian
            $query->whereRaw('total_drunk < target');
        }

        return view('livewire.admin.user-hydration-table', [
            // Menggunakan pagination bawaan Livewire agar berpindah halaman tanpa reload
            'users' => $query->latest()->paginate(10)
        ]);
    }
}