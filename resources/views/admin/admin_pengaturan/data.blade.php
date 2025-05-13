@extends('layouts.admin')

@section('title', 'Backup & Restore Data')

@section('content')
<div class="container py-5">
    <h3 class="mb-4">Backup & Restore Database</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4 p-4">
        <h5>📦 Backup Database</h5>
        <form action="{{ route('admin.backup') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success mt-3">Download Backup (.sql)</button>
        </form>
    </div>

    <div class="card p-4">
        <h5>🗂 Restore Database</h5>
        <form action="{{ route('admin.restore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="sql_file" accept=".sql" class="form-control mb-3" required>
            <button type="submit" class="btn btn-primary">Restore dari File</button>
        </form>
    </div>

    <div class="card mb-4 p-4">
        <h5>📦 Backup Database (SQL Saja)</h5>
        <form action="{{ route('admin.backup') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success mt-3">Download SQL (.sql)</button>
        </form>
    </div>

    <div class="card mb-4 p-4">
        <h5>🧾 Backup Database + File (ZIP)</h5>
        <form action="{{ route('admin.backup.zip') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-warning mt-3">Download ZIP (.zip)</button>
        </form>
    </div>
</div>
@endsection
