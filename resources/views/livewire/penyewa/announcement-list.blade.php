@php
    $header = 'Pengumuman';
@endphp

<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bullhorn"></i> Daftar Pengumuman
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="80">Gambar</th>
                                <th>Judul</th>
                                <th width="200">Tanggal</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $announcement)
                            <tr>
                                <td>
                                    @if($announcement->image_url)
                                        <img src="{{ asset('storage/' . $announcement->image_url) }}" 
                                             alt="Thumbnail" 
                                             class="img-thumbnail"
                                             style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white text-center" style="width: 60px; height: 60px; line-height: 60px; border-radius: 4px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $announcement->title }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($announcement->content, 80) }}</small>
                                </td>
                                <td>
                                    <i class="far fa-clock"></i> {{ $announcement->created_at->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal{{ $announcement->id }}" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle"></i> Tidak ada pengumuman.
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

    <!-- Detail Modals -->
    @foreach($announcements as $announcement)
    <div class="modal fade" id="detailModal{{ $announcement->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-bullhorn"></i> {{ $announcement->title }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="far fa-calendar"></i> {{ $announcement->created_at->format('d M Y, H:i') }}
                            <span class="ml-2">({{ $announcement->created_at->diffForHumans() }})</span>
                        </small>
                    </div>

                    @if($announcement->image_url)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . $announcement->image_url) }}" 
                             alt="{{ $announcement->title }}" 
                             class="img-fluid rounded border"
                             style="max-height: 400px;">
                    </div>
                    @endif

                    <div class="content">
                        <h6 class="text-bold">Isi Pengumuman:</h6>
                        <p style="white-space: pre-wrap;">{{ $announcement->content }}</p>
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
