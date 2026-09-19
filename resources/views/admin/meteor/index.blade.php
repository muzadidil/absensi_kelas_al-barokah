@extends('layouts.admin')

@section('title', 'Game 10 Jari')

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Ada isian yang belum benar:</strong>
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="mb-3">
        <h4 class="fw-bold mb-1"><i class="bi bi-rocket-takeoff me-1"></i> Pengaturan Game 10 Jari</h4>
        <p class="text-muted small mb-0">
            Semua tingkat kesulitan diatur di sini — huruf yang keluar, kecepatan jatuh, jumlah peluru boss,
            nuansa, dan efek. Ubah sewaktu-waktu kalau ternyata terlalu berat untuk murid.
            Perubahan langsung berlaku tanpa perlu menyentuh kode.
        </p>
    </div>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-jilid" type="button">JILID <span class="badge bg-secondary ms-1">{{ $levels->count() }}</span></button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-nuansa" type="button">Nuansa <span class="badge bg-secondary ms-1">{{ $themes->count() }}</span></button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-efek" type="button">Efek <span class="badge bg-secondary ms-1">{{ $effects->count() }}</span></button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-peluru" type="button">Peluru <span class="badge bg-secondary ms-1">{{ $bullets->count() }}</span></button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-boss" type="button">Boss <span class="badge bg-secondary ms-1">{{ $bosses->count() }}</span></button></li>
    </ul>

    <div class="tab-content">

        {{-- ============ JILID ============ --}}
        <div class="tab-pane fade show active" id="tab-jilid">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="text-muted small mb-0">Murid membuka JILID berurutan. Lulus = boss kalah sebelum nyawa habis.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#levelModal"
                        data-mode="tambah" data-action="{{ route('admin.meteor.levels.store') }}">
                    <i class="bi bi-plus-lg me-1"></i> Tambah JILID
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th><th>Nama</th><th>Huruf</th><th>Meteor</th>
                            <th>Jatuh</th><th>Jeda</th><th>Boss</th><th>Peluru</th><th>Nuansa</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($levels as $l)
                        <tr>
                            <td>{{ $l->level_number }}</td>
                            <td>
                                {{ $l->display_label }}
                                @if($l->is_checkpoint)<span class="badge bg-success-subtle text-success-emphasis ms-1">Checkpoint</span>@endif
                                @if($l->bullet_returns)<span class="badge bg-info-subtle text-info-emphasis ms-1">Pantul</span>@endif
                            </td>
                            <td class="small">
                                <div>
                                    <span class="text-muted">Meteor:</span>
                                    @if($l->waveWords())
                                        <span class="badge bg-info-subtle text-info-emphasis">{{ count($l->waveWords()) }} kata</span>
                                    @else
                                        <code>{{ strtoupper($l->allowed_keys) }}</code>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-muted">Boss:</span>
                                    @if($l->bossWords())
                                        <span class="badge bg-info-subtle text-info-emphasis">{{ count($l->bossWords()) }} kata</span>
                                    @else
                                        <code>{{ strtoupper($l->effectiveBossKeys()) }}</code>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $l->wave_target }}</td>
                            <td>{{ rtrim(rtrim(number_format($l->fall_seconds, 1), '0'), '.') }}s</td>
                            <td>{{ $l->spawn_interval_ms }}ms</td>
                            <td>{{ $l->boss?->name ?? '—' }} <span class="text-muted small">({{ $l->boss_hp }} HP)</span></td>
                            <td>{{ $l->bullet?->name ?? '—' }} <span class="text-muted small">×{{ $l->boss_bullets_per_shot }}</span></td>
                            <td>{{ $l->theme?->name ?? '—' }}</td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#levelModal"
                                        data-mode="edit" data-action="{{ route('admin.meteor.levels.update', $l->id) }}"
                                        data-json='@json($l)'><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.meteor.levels.destroy', $l->id) }}" method="POST" class="d-inline js-hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">Belum ada JILID.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============ NUANSA ============ --}}
        <div class="tab-pane fade" id="tab-nuansa">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="text-muted small mb-0">Warna langit, tanah, dan markas. Satu nuansa bisa dipakai banyak JILID.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#themeModal"
                        data-mode="tambah" data-action="{{ route('admin.meteor.themes.store') }}">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Nuansa
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light"><tr><th>Nama</th><th>Langit</th><th>Tanah</th><th>Markas</th><th>Objek</th><th>Latar</th><th></th></tr></thead>
                    <tbody>
                    @forelse($themes as $t)
                        <tr>
                            <td>{{ $t->name }}</td>
                            <td><span class="swatch" style="background:linear-gradient(to bottom,{{ $t->sky_top }},{{ $t->sky_mid }},{{ $t->sky_bottom }})"></span></td>
                            <td><span class="swatch" style="background:linear-gradient(to bottom,{{ $t->ground_top }},{{ $t->ground_bottom }})"></span></td>
                            <td>
                                <span class="swatch" style="background:{{ $t->wall_color }}"></span>
                                <span class="swatch" style="background:{{ $t->dome_color }}"></span>
                            </td>
                            <td class="small">{{ $skyObjects[$t->sky_object] ?? $t->sky_object }}</td>
                            <td class="small">{{ $t->is_dark ? 'Gelap' : 'Terang' }}</td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#themeModal"
                                        data-mode="edit" data-action="{{ route('admin.meteor.themes.update', $t->id) }}"
                                        data-json='@json($t)'><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.meteor.themes.destroy', $t->id) }}" method="POST" class="d-inline js-hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada nuansa.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============ EFEK ============ --}}
        <div class="tab-pane fade" id="tab-efek">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="text-muted small mb-0">Ledakan, sinar tembakan, dan getaran layar saat sesuatu dihancurkan.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#effectModal"
                        data-mode="tambah" data-action="{{ route('admin.meteor.effects.store') }}">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Efek
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light"><tr><th>Nama</th><th>Partikel</th><th>Sebaran</th><th>Warna</th><th>Getaran</th><th>Sinar</th><th></th></tr></thead>
                    <tbody>
                    @forelse($effects as $e)
                        <tr>
                            <td>{{ $e->name }}</td>
                            <td>{{ $e->particle_count }}</td>
                            <td>{{ $e->particle_spread }}</td>
                            <td><span class="swatch" style="background:hsl({{ $e->particle_hue }},100%,55%)"></span> <span class="text-muted small">{{ $e->particle_hue }}°</span></td>
                            <td>{{ $e->shake_strength }}</td>
                            <td><span class="swatch" style="background:{{ $e->beam_color }}"></span></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#effectModal"
                                        data-mode="edit" data-action="{{ route('admin.meteor.effects.update', $e->id) }}"
                                        data-json='@json($e)'><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.meteor.effects.destroy', $e->id) }}" method="POST" class="d-inline js-hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada efek.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============ PELURU ============ --}}
        <div class="tab-pane fade" id="tab-peluru">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="text-muted small mb-0">Nama peluru sekaligus jadi nama senjata boss yang memakainya.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulletModal"
                        data-mode="tambah" data-action="{{ route('admin.meteor.bullets.store') }}">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Peluru
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light"><tr><th>Nama</th><th>Tampilan</th><th>Efek saat kena</th><th></th></tr></thead>
                    <tbody>
                    @forelse($bullets as $b)
                        <tr>
                            <td>{{ $b->name }}</td>
                            <td><span class="swatch swatch-bola" style="background:radial-gradient(circle at 35% 30%,{{ $b->color_core }},{{ $b->color_mid }} 55%,{{ $b->color_edge }})"></span></td>
                            <td class="small">{{ $b->effect?->name ?? '—' }}</td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulletModal"
                                        data-mode="edit" data-action="{{ route('admin.meteor.bullets.update', $b->id) }}"
                                        data-json='@json($b)'><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.meteor.bullets.destroy', $b->id) }}" method="POST" class="d-inline js-hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada peluru.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============ BOSS ============ --}}
        <div class="tab-pane fade" id="tab-boss">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <p class="text-muted small mb-0">Nama boleh apa saja; gambarnya dipilih dari yang sudah tersedia.</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bossModal"
                        data-mode="tambah" data-action="{{ route('admin.meteor.bosses.store') }}">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Boss
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light"><tr><th>Nama</th><th>Gambar</th><th>Warna aura</th><th></th></tr></thead>
                    <tbody>
                    @forelse($bosses as $b)
                        <tr>
                            <td>{{ $b->name }}</td>
                            <td class="small">{{ $sprites[$b->sprite] ?? $b->sprite }}</td>
                            <td><span class="swatch" style="background:hsl({{ $b->hue }},100%,55%)"></span> <span class="text-muted small">{{ $b->hue }}°</span></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bossModal"
                                        data-mode="edit" data-action="{{ route('admin.meteor.bosses.update', $b->id) }}"
                                        data-json='@json($b)'><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('admin.meteor.bosses.destroy', $b->id) }}" method="POST" class="d-inline js-hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada boss.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= MODAL ================= --}}

    <div class="modal fade" id="levelModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form method="POST" class="modal-content js-form">
          @csrf <input type="hidden" name="_method" value="PUT" class="js-method">
          <div class="modal-header"><h5 class="modal-title">JILID</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-4"><label class="form-label small">Urutan</label><input type="number" name="level_number" class="form-control" min="1" required></div>
              <div class="col-8"><label class="form-label small">Nama tampil</label><input name="display_label" class="form-control" placeholder="JILID 1" required></div>

              <div class="col-12"><hr class="my-1"><span class="small fw-semibold text-muted">Isi gelombang meteor</span></div>

              <div class="col-12">
                <label class="form-label small">Huruf meteor</label>
                <input name="allowed_keys" class="form-control font-monospace" placeholder="asdfghjkl;" required>
                <div class="form-text">Ketik berdempetan tanpa spasi. Huruf dobel otomatis dibuang. Makin banyak huruf, makin sulit.</div>
              </div>

              <div class="col-12">
                <label class="form-label small">Kata meteor <span class="text-muted fw-normal">(opsional)</span></label>
                <textarea name="wave_words" rows="2" class="form-control font-monospace" placeholder="ada, kaki, jalan, sekolah"></textarea>
                <div class="form-text">
                  Kalau diisi, meteornya membawa <strong>kata</strong> dan kolom huruf di atas tidak dipakai.
                  Murid mengetik kata itu sampai selesai untuk menghancurkannya.
                  Kosongkan kalau mau kembali ke huruf tunggal. Pisahkan dengan koma atau baris baru.
                </div>
              </div>

              <div class="col-6 col-md-3"><label class="form-label small">Nyawa</label><input type="number" name="lives" class="form-control" min="1" max="20" required></div>
              <div class="col-6 col-md-3"><label class="form-label small">Jumlah meteor</label><input type="number" name="wave_target" class="form-control" min="1" required></div>
              <div class="col-6 col-md-3"><label class="form-label small">Jeda meteor (ms)</label><input type="number" name="spawn_interval_ms" class="form-control" min="200" max="10000" required><div class="form-text">Makin kecil makin rapat</div></div>
              <div class="col-6 col-md-3"><label class="form-label small">Lama jatuh (detik)</label><input type="number" step="0.1" name="fall_seconds" class="form-control" min="1" max="60" required><div class="form-text">Makin besar makin pelan</div></div>

              <div class="col-12"><hr class="my-1"><span class="small fw-semibold text-muted">Lawan boss</span></div>

              <div class="col-6 col-md-4"><label class="form-label small">Boss</label><select name="meteor_boss_id" class="form-select"><option value="">— tidak ada —</option>@foreach($bosses as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
              <div class="col-6 col-md-4"><label class="form-label small">Peluru boss</label><select name="meteor_bullet_id" class="form-select"><option value="">— bawaan —</option>@foreach($bullets as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
              <div class="col-6 col-md-4"><label class="form-label small">Darah boss</label><input type="number" name="boss_hp" class="form-control" min="1" required></div>

              <div class="col-12">
                <label class="form-label small">Huruf peluru boss</label>
                <input name="boss_keys" class="form-control font-monospace" placeholder="kosongkan = ikut huruf meteor">
                <div class="form-text">Isi kalau mau huruf saat lawan boss berbeda dari huruf gelombang meteornya.</div>
              </div>

              <div class="col-12">
                <label class="form-label small">Kata peluru boss <span class="text-muted fw-normal">(opsional)</span></label>
                <textarea name="boss_words" rows="2" class="form-control font-monospace" placeholder="api, badai, meteor"></textarea>
                <div class="form-text">Aturannya sama: diisi &rarr; peluru boss membawa kata, dikosongkan &rarr; pakai huruf.</div>
              </div>

              <div class="col-6 col-md-4"><label class="form-label small">Peluru per serangan</label><input type="number" name="boss_bullets_per_shot" class="form-control" min="1" max="20" required></div>
              <div class="col-6 col-md-4"><label class="form-label small">Jeda serangan (detik)</label><input type="number" step="0.1" name="boss_shot_gap" class="form-control" min="0.5" max="30" required></div>
              <div class="col-6 col-md-4"><label class="form-label small">Lama peluru jatuh (detik)</label><input type="number" step="0.1" name="bullet_seconds" class="form-control" min="1" max="60" required></div>

              <div class="col-12"><hr class="my-1"><span class="small fw-semibold text-muted">Tampilan</span></div>

              <div class="col-6"><label class="form-label small">Nuansa</label><select name="meteor_theme_id" class="form-select"><option value="">— bawaan —</option>@foreach($themes as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
              <div class="col-6"><label class="form-label small">Efek meteor</label><select name="meteor_effect_id" class="form-select"><option value="">— bawaan —</option>@foreach($effects as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach</select></div>

              <div class="col-12 d-flex flex-wrap gap-4">
                <div class="form-check"><input type="checkbox" name="bullet_returns" value="1" class="form-check-input" id="lvReturn"><label class="form-check-label small" for="lvReturn">Peluru memantul balik ke boss</label></div>
                <div class="form-check"><input type="checkbox" name="is_checkpoint" value="1" class="form-check-input" id="lvCp"><label class="form-check-label small" for="lvCp">Jadikan checkpoint</label></div>
              </div>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary btn-sm">Simpan</button></div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="themeModal" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content js-form">
          @csrf <input type="hidden" name="_method" value="PUT" class="js-method">
          <div class="modal-header"><h5 class="modal-title">Nuansa</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12"><label class="form-label small">Nama</label><input name="name" class="form-control" placeholder="Malam / Pagi / Senja" required></div>
              <div class="col-4"><label class="form-label small">Langit atas</label><input type="color" name="sky_top" class="form-control form-control-color w-100"></div>
              <div class="col-4"><label class="form-label small">Langit tengah</label><input type="color" name="sky_mid" class="form-control form-control-color w-100"></div>
              <div class="col-4"><label class="form-label small">Langit bawah</label><input type="color" name="sky_bottom" class="form-control form-control-color w-100"></div>
              <div class="col-6"><label class="form-label small">Tanah atas</label><input type="color" name="ground_top" class="form-control form-control-color w-100"></div>
              <div class="col-6"><label class="form-label small">Tanah bawah</label><input type="color" name="ground_bottom" class="form-control form-control-color w-100"></div>
              <div class="col-4"><label class="form-label small">Dinding markas</label><input type="color" name="wall_color" class="form-control form-control-color w-100"></div>
              <div class="col-4"><label class="form-label small">Kubah</label><input type="color" name="dome_color" class="form-control form-control-color w-100"></div>
              <div class="col-4"><label class="form-label small">Cahaya/perisai</label><input type="color" name="accent" class="form-control form-control-color w-100"></div>
              <div class="col-7"><label class="form-label small">Objek langit</label><select name="sky_object" class="form-select">@foreach($skyObjects as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
              <div class="col-5 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="is_dark" value="1" class="form-check-input" id="thDark"><label class="form-check-label small" for="thDark">Latar gelap</label></div></div>
              <div class="col-12"><div class="form-text">Centang "Latar gelap" kalau langitnya gelap, supaya tulisan di arena dibuat terang.</div></div>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary btn-sm">Simpan</button></div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="effectModal" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content js-form">
          @csrf <input type="hidden" name="_method" value="PUT" class="js-method">
          <div class="modal-header"><h5 class="modal-title">Efek</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12"><label class="form-label small">Nama</label><input name="name" class="form-control" placeholder="Ledakan Api" required></div>
              <div class="col-6"><label class="form-label small">Jumlah partikel</label><input type="number" name="particle_count" class="form-control" min="0" max="120" required></div>
              <div class="col-6"><label class="form-label small">Sebaran</label><input type="number" name="particle_spread" class="form-control" min="10" max="600" required></div>
              <div class="col-6"><label class="form-label small">Warna partikel (0-360)</label><input type="number" name="particle_hue" class="form-control" min="0" max="360" required></div>
              <div class="col-6"><label class="form-label small">Getaran layar</label><input type="number" name="shake_strength" class="form-control" min="0" max="30" required></div>
              <div class="col-6"><label class="form-label small">Warna sinar tembakan</label><input type="color" name="beam_color" class="form-control form-control-color w-100"></div>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary btn-sm">Simpan</button></div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="bulletModal" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content js-form">
          @csrf <input type="hidden" name="_method" value="PUT" class="js-method">
          <div class="modal-header"><h5 class="modal-title">Peluru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12"><label class="form-label small">Nama</label><input name="name" class="form-control" placeholder="Bola Api" required></div>
              <div class="col-4"><label class="form-label small">Inti</label><input type="color" name="color_core" class="form-control form-control-color w-100"></div>
              <div class="col-4"><label class="form-label small">Tengah</label><input type="color" name="color_mid" class="form-control form-control-color w-100"></div>
              <div class="col-4"><label class="form-label small">Tepi</label><input type="color" name="color_edge" class="form-control form-control-color w-100"></div>
              <div class="col-12"><label class="form-label small">Efek saat kena</label><select name="meteor_effect_id" class="form-select"><option value="">— bawaan —</option>@foreach($effects as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach</select></div>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary btn-sm">Simpan</button></div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="bossModal" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content js-form">
          @csrf <input type="hidden" name="_method" value="PUT" class="js-method">
          <div class="modal-header"><h5 class="modal-title">Boss</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12"><label class="form-label small">Nama</label><input name="name" class="form-control" placeholder="Pocong" required></div>
              <div class="col-12"><label class="form-label small">Gambar</label><select name="sprite" class="form-select">@foreach($sprites as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
              <div class="col-6"><label class="form-label small">Warna aura (0-360)</label><input type="number" name="hue" class="form-control" min="0" max="360" required></div>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary btn-sm">Simpan</button></div>
        </form>
      </div>
    </div>

</div>

<style>
    .swatch { display:inline-block; width:34px; height:18px; border-radius:4px; border:1px solid rgba(0,0,0,.15); vertical-align:middle; }
    .swatch-bola { width:22px; height:22px; border-radius:50%; }
</style>

<script>
(function () {
    // Satu modal dipakai bersama semua baris: isinya diisi dari data-json tombol yang diklik.
    var DEFAULTS = {
        levelModal:  { level_number: '', display_label: '', allowed_keys: 'asdfghjkl;',
                       boss_keys: '', wave_words: '', boss_words: '', lives: 5,
                       wave_target: 12, spawn_interval_ms: 1500, fall_seconds: 7, bullet_seconds: 5.5,
                       boss_shot_gap: 2.8, boss_bullets_per_shot: 2, boss_hp: 12,
                       meteor_theme_id: '', meteor_boss_id: '', meteor_bullet_id: '', meteor_effect_id: '',
                       bullet_returns: false, is_checkpoint: false },
        themeModal:  { name: '', sky_top: '#04061a', sky_mid: '#101a45', sky_bottom: '#1d2764',
                       ground_top: '#16371f', ground_bottom: '#040c07', accent: '#6ec8ff',
                       wall_color: '#04150c', dome_color: '#04150c', sky_object: 'bintang', is_dark: true },
        effectModal: { name: '', particle_count: 20, particle_spread: 150, particle_hue: 18,
                       shake_strength: 3, beam_color: '#8ce6ff' },
        bulletModal: { name: '', color_core: '#ffdca6', color_mid: '#ff8b3d', color_edge: '#8b2b07',
                       meteor_effect_id: '' },
        bossModal:   { name: '', sprite: 'pocong', hue: 18 }
    };

    function fill(form, data) {
        Object.keys(data).forEach(function (key) {
            var field = form.querySelector('[name="' + key + '"]');
            if (!field) return;
            if (field.type === 'checkbox') { field.checked = !!data[key] && data[key] !== '0'; }
            else { field.value = data[key] === null ? '' : data[key]; }
        });
    }

    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('show.bs.modal', function (ev) {
            var trigger = ev.relatedTarget;
            if (!trigger) return;
            var form = modal.querySelector('.js-form');
            var edit = trigger.dataset.mode === 'edit';

            form.action = trigger.dataset.action;
            form.querySelector('.js-method').disabled = !edit;   // tambah = POST, edit = PUT
            modal.querySelector('.modal-title').textContent =
                (edit ? 'Ubah ' : 'Tambah ') + modal.querySelector('.modal-title').textContent.replace(/^(Ubah|Tambah) /, '');

            fill(form, DEFAULTS[modal.id] || {});
            if (edit && trigger.dataset.json) {
                try { fill(form, JSON.parse(trigger.dataset.json)); } catch (e) {}
            }
        });
    });

    document.querySelectorAll('.js-hapus').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            if (!confirm('Hapus data ini? Tindakan ini tidak bisa dibatalkan.')) ev.preventDefault();
        });
    });
})();
</script>
@endsection
