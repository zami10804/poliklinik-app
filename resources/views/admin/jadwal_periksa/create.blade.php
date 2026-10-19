<x-layouts.app title="Tambah Jadwal">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('jadwal_periksa.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Tambah Jadwal Periksa</h2>
    </div>

    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">
            <form action="{{ route('jadwal_periksa.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Dokter <span class="text-red-500">*</span></label>
                    <select name="id_dokter" class="w-full px-4 py-2 rounded-lg border-2 border-slate-300 focus:border-primary focus:outline-none @error('id_dokter') border-red-500 @enderror" required>
                        <option value="">-- Pilih Dokter --</option>
                        @foreach($dokters as $dokter)
                            <option value="{{ $dokter->id }}" {{ old('id_dokter') == $dokter->id ? 'selected' : '' }}>{{ $dokter->nama }}</option>
                        @endforeach
                    </select>
                    @error('id_dokter') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Hari <span class="text-red-500">*</span></label>
                    <select name="hari" class="w-full px-4 py-2 rounded-lg border-2 border-slate-300 focus:border-primary focus:outline-none @error('hari') border-red-500 @enderror" required>
                        <option value="">-- Pilih Hari --</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $h)
                            <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                    @error('hari') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-4 mb-8">
                    <div class="w-1/2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" class="w-full px-4 py-2 rounded-lg border-2 border-slate-300 focus:border-primary focus:outline-none @error('jam_mulai') border-red-500 @enderror" required>
                        @error('jam_mulai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" class="w-full px-4 py-2 rounded-lg border-2 border-slate-300 focus:border-primary focus:outline-none @error('jam_selesai') border-red-500 @enderror" required>
                        @error('jam_selesai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-white font-semibold text-sm transition"><i class="fas fa-save mr-1"></i> Simpan</button>
                    <a href="{{ route('jadwal_periksa.index') }}" class="px-6 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
