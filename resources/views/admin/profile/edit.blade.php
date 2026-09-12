@extends('layouts.admin')

@section('title', 'Edit Profil')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Edit Profil</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui nama, informasi akun, dan kata sandi Anda.</p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-soft overflow-hidden">
        <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Informasi Dasar -->
            <div>
                <h2 class="text-base font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Informasi Akun</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                            class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                        @error('username') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Ganti Password & Keamanan -->
            <div>
                <h2 class="text-base font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2"><i class="fa-solid fa-lock mr-2 text-maroon-600"></i> Keamanan Akun</h2>
                <p class="text-xs text-slate-400 mb-4">Kosongkan ketiga kolom di bawah ini jika Anda tidak ingin mengubah kata sandi.</p>
                
                <!-- SATUKAN DALAM SATU GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Kata Sandi Saat Ini -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Kata Sandi Saat Ini</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" placeholder="Masukkan kata sandi lama Anda"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-4 pr-10 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-slate-400 hover:text-maroon-600 transition-colors" onclick="togglePassword('current_password', this)">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                        @error('current_password') <span class="text-xs text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Spacer Kosong (Agar form selanjutnya turun ke bawah) -->
                    <div class="hidden md:block"></div>

                    <!-- Kata Sandi Baru -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Kata Sandi Baru</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-4 pr-10 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-slate-400 hover:text-maroon-600 transition-colors" onclick="togglePassword('password', this)">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                        @error('password') <span class="text-xs text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Konfirmasi Kata Sandi Baru -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Konfirmasi Kata Sandi Baru</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru"
                                class="w-full rounded-lg border border-slate-200 py-2.5 pl-4 pr-10 text-sm focus:border-maroon-400 focus:outline-none focus:ring-2 focus:ring-maroon-100">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-slate-400 hover:text-maroon-600 transition-colors" onclick="togglePassword('password_confirmation', this)">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-maroon-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-maroon-600/30 hover:bg-maroon-700 hover:shadow-maroon-600/50 transition-all cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script Toggle Password -->
<script>
    function togglePassword(inputId, iconContainer) {
        const input = document.getElementById(inputId);
        const icon = iconContainer.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection