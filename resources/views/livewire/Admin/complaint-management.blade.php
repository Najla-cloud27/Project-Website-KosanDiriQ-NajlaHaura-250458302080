@php
    $header = 'Manajemen Keluhan';
@endphp

<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Keluhan</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" wire:model.live="search" class="form-control" placeholder="Cari judul atau nama penyewa...">
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

                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Penyewa</th>
                                    <th>Kamar</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($complaints as $complaint)
                                <tr>
                                    <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $complaint->user->name ?? '-' }}</td>
                                    <td>{{ $complaint->room->name ?? '-' }}</td>
                                    <td>{{ $complaint->title }}</td>
                                    <td>
                                        <span class="badge badge-{{ $complaint->status === 'selesai' ? 'success' : ($complaint->status === 'diproses' ? 'warning' : ($complaint->status === 'ditolak' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="selectComplaint({{ $complaint->id }})" class="btn btn-sm btn-primary" type="button">
                                            <i class="fas fa-edit"></i> Update
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada keluhan.</td>
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

    <!-- Update Modal -->
    @if($selectedComplaint)
    <div class="modal fade" id="updateModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white">
                        <i class="fas fa-edit"></i> Update Status Keluhan
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="updateStatus">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Penyewa</th>
                                        <td>: {{ $selectedComplaint->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kamar</th>
                                        <td>: {{ $selectedComplaint->room->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>: {{ $selectedComplaint->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if($selectedComplaint->image_url)
                                <div class="text-center">
                                    <img src="{{ asset('storage/' . $selectedComplaint->image_url) }}" 
                                         alt="Foto Keluhan" 
                                         class="img-fluid rounded border"
                                         style="max-height: 150px;">
                                </div>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label><strong>Judul Keluhan</strong></label>
                            <p class="form-control-plaintext border-bottom">{{ $selectedComplaint->title }}</p>
                        </div>

                        <div class="form-group">
                            <label><strong>Deskripsi</strong></label>
                            <p class="form-control-plaintext border-bottom">{{ $selectedComplaint->description }}</p>
                        </div>
                        
                        <div class="form-group">
                            <label><strong>Status <span class="text-danger">*</span></strong></label>
                            <select wire:model="newStatus" class="form-control @error('newStatus') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="diproses">Sedang Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                            @error('newStatus')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    window.addEventListener('close-modal', event => {
        $('#updateModal').modal('hide');
        // Hapus backdrop dan class modal-open dari body
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    });

    window.addEventListener('show-modal', event => {
        $('#updateModal').modal('show');
    });

    // Pastikan cleanup saat modal ditutup manual
    $('#updateModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    });

    // Livewire hook untuk refresh setelah update
    Livewire.on('refreshComponent', () => {
        $('#updateModal').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    });
</script>
@endpush
