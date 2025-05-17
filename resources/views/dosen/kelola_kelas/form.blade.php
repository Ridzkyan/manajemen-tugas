<form method="POST" id="form-kelas" action="{{ isset($kelas) ? route('dosen.kelola_kelas.update', $kelas->id) : route('dosen.kelola_kelas.store') }}">
    @csrf
    @if(isset($kelas))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="nama_kelas" class="form-label label-form">
            <i class="bi bi-person-lines-fill me-1 text-warning"></i> Nama Kelas
        </label>
        <input type="text" name="nama_kelas" id="nama_kelas" class="form-control input-form"
               value="{{ old('nama_kelas', $kelas->nama_kelas ?? '') }}" placeholder="Contoh: Kelas A" required>
    </div>

    <div class="mb-3">
        <label for="nama_matakuliah" class="form-label label-form">
            <i class="bi bi-book me-1 text-warning"></i> Nama Mata Kuliah
        </label>
        <input type="text" name="nama_matakuliah" id="nama_matakuliah" class="form-control input-form"
               value="{{ old('nama_matakuliah', $kelas->nama_matakuliah ?? '') }}" placeholder="Contoh: Pemrograman Web" required>
    </div>

    <div class="mb-3">
        <label for="kode_unik" class="form-label label-form">
            <i class="bi bi-shield-lock me-1 text-warning"></i> Kode Unik
        </label>
        <input type="text" name="kode_unik" id="kode_unik" class="form-control input-form bg-light"
               value="{{ old('kode_unik', $kelas->kode_unik ?? '') }}" readonly>
    </div>

    <div class="mb-3">
        <label for="whatsapp_link" class="form-label label-form">
            <i class="bi bi-whatsapp me-1 text-warning"></i> Link WhatsApp (opsional)
        </label>
        <input type="url" name="whatsapp_link" id="whatsapp_link" class="form-control input-form"
               value="{{ old('whatsapp_link', $kelas->whatsapp_link ?? '') }}" placeholder="https://chat.whatsapp.com/...">
    </div>

    @unless(isset($kelas))
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-custom">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
        </button>
    </div>
    @endunless
</form>