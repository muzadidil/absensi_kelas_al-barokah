@extends('layouts.admin')

@section('title', 'Log Audit Email')

@section('content')
<div class="container">
  <h5>Log Audit Email</h5>

  <table class="table table-striped table-compact table-bordered table-hover table-sm">
    <thead>
      <tr>
        <th style="width: 1%;">No.</th>
        <th>Pengguna</th>
        <th>Email</th>
        <th>Subjek</th>
        <th>Waktu Kirim</th>
      </tr>
    </thead>
    <tbody>
      @forelse($logs as $log)
        <tr>
          <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
          <td>{{ $log->user->name }}</td>
          <td>{{ $log->email }}</td>
          <td>{{ $log->subject }}</td>
          <td>{{ $log->sent_at->format('d M Y H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center">Belum ada log email.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Record Count Summary and Pagination -->
  <div class="d-flex justify-content-between align-items-center">
      <div class="small text-muted mb-0">
          Menampilkan {{ $logs->firstItem() }} sampai {{ $logs->lastItem() }} dari {{ $logs->total() }} data
      </div>
      <div class="mb-0">
          <div class="pagination-wrapper small mb-0">
              {{ $logs->links() }}
          </div>
      </div>
  </div>
</div>
@endsection