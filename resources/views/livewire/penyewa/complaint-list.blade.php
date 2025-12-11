@php
    $header = 'Keluhan Saya';
@endphp

<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Keluhan</h3>
                    <div class="card-tools">
                        <a href="{{ route('penyewa.complaints.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Buat Keluhan Baru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" wire:model.live="search" class="form-control" placeholder="Cari keluhan...">
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="statusFilter" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="diproses">Sedang Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Judul</th>
                                    <th>Kamar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complaints as $complaint)
                                <tr>
                                    <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $complaint->title }}</td>
                                    <td>{{ $complaint->room->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $complaint->status === 'selesai' ? 'success' : ($complaint->status === 'diproses' ? 'warning' : ($complaint->status === 'ditolak' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $complaint->id }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada keluhan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $complaints->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modals -->
    @foreach($complaints as $complaint)
    <div class="modal fade" id="detailModal{{ $complaint->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle"></i> Detail Keluhan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Tanggal Dibuat</th>
                                    <td>{{ $complaint->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Kamar</th>
                                    <td>{{ $complaint->room->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge badge-{{ $complaint->status === 'selesai' ? 'success' : ($complaint->status === 'diproses' ? 'warning' : ($complaint->status === 'ditolak' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($complaint->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($complaint->image_url)
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $complaint->image_url) }}" 
                                     alt="Foto Keluhan" 
                                     class="img-fluid rounded border"
                                     style="max-height: 200px;">
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="form-group">
                        <label><strong>Judul Keluhan</strong></label>
                        <p class="form-control-static">{{ $complaint->title }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label><strong>Deskripsi</strong></label>
                        <p class="form-control-static">{{ $complaint->description }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
