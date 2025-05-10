{{-- dosen/kelas/form.blade.php --}}
<form method="POST" action="{{ isset($kelas) ? route('dosen.kelas.update', $kelas->id) : route('dosen.kelas.store') }}">
    @csrf
    @if(isset($kelas))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="nama_kelas" class="form-label">Nama Kelas</label>
        <input type="text" name="nama_kelas" id="nama_kelas" class="form-control"
               value="{{ old('nama_kelas', $kelas->nama_kelas ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="nama_matakuliah" class="form-label">Nama Mata Kuliah</label>
        <input type="text" name="nama_matakuliah" id="nama_matakuliah" class="form-control"
               value="{{ old('nama_matakuliah', $kelas->nama_matakuliah ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="kode_unik" class="form-label">Kode Unik</label>
        <input type="text" name="kode_unik" id="kode_unik" class="form-control"
               value="{{ old('kode_unik', $kelas->kode_unik ?? '') }}" readonly>
    </div>

    <div class="mb-3">
        <label for="whatsapp_link" class="form-label">Link WhatsApp (opsional)</label>
        <input type="url" name="whatsapp_link" id="whatsapp_link" class="form-control"
               value="{{ old('whatsapp_link', $kelas->whatsapp_link ?? '') }}">
    </div>

    <button type="submit" class="btn btn-success">
        {{ isset($kelas) ? 'Update Kelas' : 'Tambah Kelas' }}
    </button>
</form>
