@php
    $header = 'Kelola Pengumuman';
@endphp

<div>
    <div class="row">
        <div class="col-12">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <i class="icon fas fa-check"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Form Create/Edit -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bullhorn"></i> {{ $editingId ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}
                    </h3>
                </div>
                <form wire:submit.prevent="{{ $editingId ? 'updateAnnouncement' : 'createAnnouncement' }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Judul Pengumuman <span class="text-danger">*</span></label>
                            <input type="text" wire:model="title" class="form-control @error('title') is-invalid @enderror" placeholder="Masukkan judul pengumuman">
                            @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Konten <span class="text-danger">*</span></label>
                            <textarea wire:model="content" rows="5" class="form-control @error('content') is-invalid @enderror" placeholder="Masukkan isi pengumuman"></textarea>
                            @error('content') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label>Gambar</label>
                            <div class="custom-file">
                                <input type="file" wire:model="image" class="custom-file-input @error('image') is-invalid @enderror" id="imageInput" accept="image/*">
                                <label class="custom-file-label" for="imageInput">Pilih gambar...</label>
                                @error('image') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            @if ($image)
                                <div class="mt-2">
                                    <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Status Publikasi <span class="text-danger">*</span></label>
                            <select wire:model="publish_status" class="form-control @error('publish_status') is-invalid @enderror">
                                <option value="draf">Draft (Belum Dipublikasikan)</option>
                                <option value="diterbitkan">Diterbitkan</option>
                            </select>
                            @error('publish_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ $editingId ? 'Update' : 'Simpan' }}
                        </button>
                        @if($editingId)
                            <button type="button" wire:click="cancelEdit" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- List Announcements -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Daftar Pengumuman
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Konten</th>
                                <th>Status</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $index => $announcement)
                            <tr>
                                <td>{{ $announcements->firstItem() + $index }}</td>
                                <td><strong>{{ $announcement->title }}</strong></td>
                                <td>{{ \Illuminate\Support\Str::limit($announcement->content, 50) }}</td>
                                <td>
                                    @if($announcement->publish_status === 'diterbitkan')
                                        <span class="badge badge-success">Diterbitkan</span>
                                    @else
                                        <span class="badge badge-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $announcement->user->name ?? '-' }}</td>
                                <td>{{ $announcement->created_at->format('d M Y') }}</td>
                                <td>
                                    <button wire:click="editAnnouncement({{ $announcement->id }})" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="deleteAnnouncement({{ $announcement->id }})" onclick="return confirm('Yakin hapus pengumuman ini?')" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle"></i> Belum ada pengumuman
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Update custom file input label
    document.getElementById('imageInput').addEventListener('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Pilih gambar...';
        var label = e.target.nextElementSibling;
        label.innerText = fileName;
    });
    
    // Listen for scroll to form event
    window.addEventListener('scrollToForm', event => {
        document.querySelector('.card-primary').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    });
    
    // Reset file input label when Livewire updates
    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('morph.updated', ({ el, component }) => {
            const fileInput = document.getElementById('imageInput');
            const label = fileInput?.nextElementSibling;
            if (label && !fileInput?.files.length) {
                label.innerText = 'Pilih gambar...';
            }
        });
    });
</script>
@endpush