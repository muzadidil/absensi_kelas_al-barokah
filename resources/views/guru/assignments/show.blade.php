@extends('layouts.guru')

@section('title', 'Detail Tugas')

@section('content')
<div class="container-fluid px-2">

    <!-- Success Message -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    timer: 3000,
                    timerProgressBar: true,
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    <!-- ================= BAGIAN A: INFO TUGAS ================= -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                    <h4 class="fw-bold mb-1">{{ $assignment->title }}</h4>
                    <p class="text-muted mb-2">{{ $assignment->description ?: 'Tidak ada deskripsi.' }}</p>
                    <div class="d-flex flex-wrap gap-3 small">
                        <span>
                            <i class="bi bi-people-fill me-1"></i>
                            Target: <strong>{{ $assignment->grade_level ? 'Kelas ' . $assignment->grade_level : 'Individual' }}</strong>
                        </span>
                        <span>
                            <i class="bi bi-calendar-event me-1"></i>
                            Deadline: <strong>{{ $assignment->deadline ? $assignment->deadline->format('d/m/Y H:i') : 'Tidak ada' }}</strong>
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editAssignmentModal">
                        <i class="bi bi-pencil-square me-1"></i> Edit Tugas
                    </button>
                    <a href="{{ route('guru.assignments.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Assignment Modal -->
    <div class="modal fade" id="editAssignmentModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content border border-1 border-primary rounded-4 shadow">
          <form action="{{ route('guru.assignments.update', $assignment->id) }}" method="POST">
              @csrf
              @method('PUT')
              <div class="modal-header py-2 px-3">
              <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                  <i class="bi bi-pencil-square"></i> Edit Tugas
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body pt-1">
                  <div class="row g-3 mb-3 align-items-start">
                  <div class="col-md-12">
                      <label class="form-label">Judul Tugas</label>
                      <input type="text" name="title" class="form-control" value="{{ $assignment->title }}" required>
                  </div>
                  </div>

                  <div class="row g-3 mb-3 align-items-start">
                  <div class="col-md-12">
                      <label class="form-label">Deskripsi <span class="text-muted small">(opsional)</span></label>
                      <textarea name="description" class="form-control" rows="3">{{ $assignment->description }}</textarea>
                  </div>
                  </div>

                  <div class="row g-3 mb-3 align-items-start">
                    <div class="col-md-6">
                        <label class="form-label d-block">Target</label>
                        <div class="d-flex gap-3 pt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="showEditTargetKelas" value="kelas" onchange="document.getElementById('showEditGradeLevelField').classList.remove('d-none')" {{ $assignment->grade_level ? 'checked' : '' }}>
                                <label class="form-check-label" for="showEditTargetKelas">Untuk Kelas</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="target_type" id="showEditTargetIndividu" value="individu" onchange="document.getElementById('showEditGradeLevelField').classList.add('d-none')" {{ !$assignment->grade_level ? 'checked' : '' }}>
                                <label class="form-check-label" for="showEditTargetIndividu">Murid Tertentu</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 {{ $assignment->grade_level ? '' : 'd-none' }}" id="showEditGradeLevelField">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="grade_level" class="form-select">
                        <option value="" disabled>Pilih Tingkat</option>
                        @foreach($gradeLevels as $gradeLevel)
                            <option value="{{ $gradeLevel->name }}" @selected($assignment->grade_level === $gradeLevel->name)>{{ $gradeLevel->name }}</option>
                        @endforeach
                        </select>
                    </div>
                  </div>

                  <div class="row g-3 mb-3 align-items-start">
                    <div class="col-md-6">
                        <label class="form-label">Deadline <span class="text-muted small">(opsional)</span></label>
                        <input type="datetime-local" name="deadline" class="form-control" value="{{ $assignment->deadline ? $assignment->deadline->format('Y-m-d\TH:i') : '' }}">
                    </div>
                  </div>
              </div>
              <div class="modal-footer d-flex justify-content-end">
                  <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
              </div>
          </form>
          </div>
      </div>
    </div>

    <!-- ================= BAGIAN B: DAFTAR SOAL ================= -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Daftar Soal</h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Soal
                </button>
            </div>

            @forelse($assignment->questions as $question)
                <div class="border rounded-3 p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold">Soal {{ $loop->iteration }}</span>
                                <span class="badge {{ match($question->type) { 'pilgan' => 'bg-primary', 'essay' => 'bg-warning text-dark', 'praktek' => 'bg-info text-dark' } }}">
                                    {{ match($question->type) { 'pilgan' => 'Pilgan', 'essay' => 'Essay', 'praktek' => 'Praktek' } }}
                                </span>
                                <span class="badge bg-light text-dark border">{{ $question->points }} poin</span>
                            </div>
                            <p class="mb-2">{{ $question->question_text }}</p>

                            @if($question->type === 'pilgan' && $question->options)
                                <ul class="list-unstyled mb-0 small">
                                    @foreach($question->options as $i => $option)
                                        <li class="{{ $option === $question->correct_answer ? 'text-success fw-semibold' : '' }}">
                                            <i class="bi {{ $option === $question->correct_answer ? 'bi-check-circle-fill' : 'bi-circle' }} me-1"></i>
                                            {{ chr(97 + $i) }}. {{ $option }}
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif(in_array($question->type, ['essay', 'praktek']) && $question->answer_key)
                                <div class="small text-muted">
                                    <span class="fw-semibold">{{ $question->type === 'praktek' ? 'Kriteria penilaian:' : 'Kunci jawaban acuan:' }}</span> {{ $question->answer_key }}
                                </div>
                            @endif
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $question->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form id="deleteQuestionForm{{ $question->id }}" action="{{ route('guru.assignments.questions.destroy', [$assignment->id, $question->id]) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-danger rounded-pill" onclick="confirmDeleteQuestion({{ $question->id }})">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Edit Question Modal -->
                <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                      <div class="modal-content border border-1 border-primary rounded-4 shadow">
                      <form action="{{ route('guru.assignments.questions.update', [$assignment->id, $question->id]) }}" method="POST" onsubmit="return prepareQuestionSubmit(this)">
                          @csrf
                          @method('PUT')
                          <div class="modal-header py-2 px-3">
                              <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                                  <i class="bi bi-pencil-square"></i> Edit Soal
                              </h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body pt-1">
                              <div class="mb-3">
                                  <label class="form-label d-block">Tipe Soal</label>
                                  <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="type" value="pilgan" onchange="toggleQuestionType(this.form)" {{ $question->type === 'pilgan' ? 'checked' : '' }}>
                                      <label class="form-check-label">Pilihan Ganda</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="type" value="essay" onchange="toggleQuestionType(this.form)" {{ $question->type === 'essay' ? 'checked' : '' }}>
                                      <label class="form-check-label">Essay</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="type" value="praktek" onchange="toggleQuestionType(this.form)" {{ $question->type === 'praktek' ? 'checked' : '' }}>
                                      <label class="form-check-label">Praktek</label>
                                  </div>
                              </div>

                              <div class="mb-3">
                                  <label class="form-label">Pertanyaan</label>
                                  <textarea name="question_text" class="form-control" rows="3" required>{{ $question->question_text }}</textarea>
                              </div>

                              <div class="options-section {{ $question->type === 'pilgan' ? '' : 'd-none' }}">
                                  <label class="form-label d-block">Opsi Jawaban <span class="text-muted small">(pilih salah satu sebagai jawaban benar)</span></label>
                                  <div class="options-list">
                                      @php
                                          $existingOptions = $question->options ?: [];
                                          $existingOptions = array_pad($existingOptions, 5, null);
                                      @endphp
                                      @foreach($existingOptions as $i => $option)
                                          <div class="input-group mb-2">
                                              <span class="input-group-text">
                                                  {{ chr(97 + $i) }}.
                                                  <input class="form-check-input mt-0 ms-2" type="radio" name="correct_option" value="{{ $i }}" @checked($option !== null && $option === $question->correct_answer)>
                                              </span>
                                              <input type="text" name="options[]" class="form-control option-input" value="{{ $option }}" placeholder="Opsi {{ chr(97 + $i) }}" {{ ($question->type === 'pilgan' && $i < 2) ? 'required' : '' }}>
                                          </div>
                                      @endforeach
                                  </div>
                                  <button type="button" class="btn btn-sm btn-outline-secondary d-none" onclick="addOptionField(this.closest('.options-section').querySelector('.options-list'))">
                                      <i class="bi bi-plus"></i> Tambah Opsi
                                  </button>
                                  <input type="hidden" name="correct_answer" value="{{ $question->correct_answer }}">
                              </div>

                              <div class="essay-section {{ in_array($question->type, ['essay', 'praktek']) ? '' : 'd-none' }}">
                                  <label class="form-label">Kunci Jawaban / Kriteria Penilaian <span class="text-muted small">(opsional, panduan untuk guru saat menilai)</span></label>
                                  <textarea name="answer_key" class="form-control" rows="2">{{ $question->answer_key }}</textarea>
                              </div>

                              <div class="mb-3 mt-3">
                                  <label class="form-label">Bobot Nilai</label>
                                  <input type="number" name="points" class="form-control" style="max-width: 150px;" value="{{ $question->points }}" min="1" required>
                              </div>
                          </div>
                          <div class="modal-footer d-flex justify-content-end">
                              <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                          </div>
                      </form>
                      </div>
                  </div>
                </div>
            @empty
                <p class="text-muted text-center mb-0">Belum ada soal untuk tugas ini.</p>
            @endforelse
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content border border-1 border-primary rounded-4 shadow">
          <form action="{{ route('guru.assignments.questions.store', $assignment->id) }}" method="POST" onsubmit="return prepareQuestionSubmit(this)">
              @csrf
              <div class="modal-header py-2 px-3">
                  <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                      <i class="bi bi-plus-lg"></i> Tambah Soal
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body pt-1">
                  <div class="mb-3">
                      <label class="form-label d-block">Tipe Soal</label>
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="type" value="pilgan" checked onchange="toggleQuestionType(this.form)">
                          <label class="form-check-label">Pilihan Ganda</label>
                      </div>
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="type" value="essay" onchange="toggleQuestionType(this.form)">
                          <label class="form-check-label">Essay</label>
                      </div>
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="type" value="praktek" onchange="toggleQuestionType(this.form)">
                          <label class="form-check-label">Praktek</label>
                      </div>
                  </div>

                  <div class="mb-3">
                      <label class="form-label">Pertanyaan</label>
                      <textarea name="question_text" class="form-control" rows="3" required></textarea>
                  </div>

                  <div class="options-section">
                      <label class="form-label d-block">Opsi Jawaban <span class="text-muted small">(pilih salah satu sebagai jawaban benar; kosongkan opsi yang tidak dipakai)</span></label>
                      <div class="options-list">
                          @foreach(['a', 'b', 'c', 'd', 'e'] as $i => $letter)
                              <div class="input-group mb-2">
                                  <span class="input-group-text">
                                      {{ $letter }}.
                                      <input class="form-check-input mt-0 ms-2" type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}>
                                  </span>
                                  <input type="text" name="options[]" class="form-control option-input" placeholder="Opsi {{ $letter }}" {{ $i < 2 ? 'required' : '' }}>
                              </div>
                          @endforeach
                      </div>
                      <input type="hidden" name="correct_answer">
                  </div>

                  <div class="essay-section d-none">
                      <label class="form-label">Kunci Jawaban / Kriteria Penilaian <span class="text-muted small">(opsional, panduan untuk guru saat menilai)</span></label>
                      <textarea name="answer_key" class="form-control" rows="2"></textarea>
                  </div>

                  <div class="mb-3 mt-3">
                      <label class="form-label">Bobot Nilai</label>
                      <input type="number" name="points" class="form-control" style="max-width: 150px;" value="10" min="1" required>
                  </div>
              </div>
              <div class="modal-footer d-flex justify-content-end">
                  <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
              </div>
          </form>
          </div>
      </div>
    </div>

    <!-- ================= BAGIAN C: MURID YANG DITUGASKAN ================= -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Murid yang Ditugaskan</h5>

            <!-- Assign Form -->
            <form action="{{ route('guru.assignments.assign', $assignment->id) }}" method="POST" class="border rounded-3 p-3 mb-3 bg-light">
                @csrf
                <div class="mb-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="assign_type" id="assignKelas" value="kelas" checked onchange="toggleAssignFields()">
                        <label class="form-check-label" for="assignKelas">Assign per Kelas</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="assign_type" id="assignIndividu" value="individu" onchange="toggleAssignFields()">
                        <label class="form-check-label" for="assignIndividu">Assign Individual</label>
                    </div>
                </div>

                <div id="assignKelasField" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label">Pilih Kelas</label>
                        <select name="grade_level" class="form-select" style="min-width: 200px;">
                            <option value="" selected disabled>Pilih Tingkat</option>
                            @foreach($gradeLevels as $gradeLevel)
                                <option value="{{ $gradeLevel->name }}">{{ $gradeLevel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-people-fill me-1"></i> Assign Semua Murid di Kelas Ini
                        </button>
                    </div>
                </div>

                <div id="assignIndividuField" class="row g-2 align-items-end d-none">
                    <div class="col-auto">
                        <label class="form-label">Pilih Kelas</label>
                        <select id="assignFilterGrade" class="form-select" style="min-width: 200px;" onchange="loadLearnersForAssign(this.value)">
                            <option value="" selected disabled>-- Pilih Kelas --</option>
                            @foreach($gradeLevels as $gradeLevel)
                                <option value="{{ $gradeLevel->name }}">{{ $gradeLevel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Pilih Murid</label>
                        <select name="learner_ids[]" id="assignLearnerSelect" class="form-select" multiple disabled size="4" style="min-width: 250px;">
                            <option value="">-- Pilih Kelas Dahulu --</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus-fill me-1"></i> Assign
                        </button>
                    </div>
                </div>
            </form>

            <!-- Assigned Learners Table -->
            <div class="overflow-auto rounded-lg border border-gray-300 shadow-sm">
                <table class="table table-sm table-compact table-bordered table-hover bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 1%;">No.</th>
                            <th class="px-3 py-2 text-left">Nama Murid</th>
                            <th class="px-3 py-2 text-left">Kelas</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Nilai</th>
                            <th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignment->assignmentLearners as $al)
                            <tr>
                                <td class="px-3 py-1">{{ $loop->iteration }}</td>
                                <td class="px-3 py-1">{{ $al->learner->nama_lengkap }}</td>
                                <td class="px-3 py-1">{{ $al->learner->grade_level }}</td>
                                <td class="px-3 py-1">
                                    <span class="badge {{ $al->status === 'selesai' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $al->status === 'selesai' ? 'Selesai' : 'Belum' }}
                                    </span>
                                </td>
                                <td class="px-3 py-1">
                                    @if($al->status === 'selesai')
                                        {{ $al->total_score ?? 0 }}
                                        <span class="text-muted small d-block">(dinilai admin)</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-1 text-center">
                                    <form action="{{ route('guru.assignments.unassign', [$assignment->id, $al->learner_id]) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus penugasan murid ini? Jawaban yang sudah diisi juga akan dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger rounded-pill">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada murid yang ditugaskan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDeleteQuestion(id) {
        Swal.fire({
            title: 'Hapus Soal?',
            text: 'Soal ini akan dihapus permanen beserta jawaban murid untuk soal ini.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteQuestionForm' + id).submit();
            }
        });
    }

    function addOptionField(list) {
        const count = list.querySelectorAll('.option-input').length;
        if (count >= 5) return;

        const letter = String.fromCharCode(97 + count);
        const row = document.createElement('div');
        row.className = 'input-group mb-2';
        row.innerHTML = `
            <span class="input-group-text">
                ${letter}.
                <input class="form-check-input mt-0 ms-2" type="radio" name="correct_option" value="${count}">
            </span>
            <input type="text" name="options[]" class="form-control option-input" placeholder="Opsi ${letter}">
        `;
        list.appendChild(row);
    }

    function toggleQuestionType(form) {
        const isPilgan = form.querySelector('input[name="type"]:checked').value === 'pilgan';
        const optionsSection = form.querySelector('.options-section');
        const essaySection = form.querySelector('.essay-section');
        optionsSection.classList.toggle('d-none', !isPilgan);
        essaySection.classList.toggle('d-none', isPilgan);
        form.querySelectorAll('.option-input').forEach(el => el.required = isPilgan);
    }

    function prepareQuestionSubmit(form) {
        const isPilgan = form.querySelector('input[name="type"]:checked').value === 'pilgan';
        const section = form.querySelector('.options-section');

        if (isPilgan) {
            const optionInputs = form.querySelectorAll('.option-input');
            const checkedRadio = form.querySelector('input[name="correct_option"]:checked');
            const hidden = form.querySelector('input[name="correct_answer"]');
            if (checkedRadio && optionInputs[parseInt(checkedRadio.value, 10)]) {
                hidden.value = optionInputs[parseInt(checkedRadio.value, 10)].value;
            }
            // Opsi yang dikosongkan (mis. cuma isi a-c dari 5 slot) tidak ikut terkirim.
            optionInputs.forEach(el => {
                if (!el.value.trim()) el.disabled = true;
            });
        } else {
            // Soal essay: nonaktifkan field opsi supaya tidak ikut terkirim
            // (kalau tetap terkirim sebagai string kosong, validasi options.* akan gagal).
            section.querySelectorAll('input').forEach(el => el.disabled = true);
        }
        return true;
    }

    function toggleAssignFields() {
        const isKelas = document.getElementById('assignKelas').checked;
        document.getElementById('assignKelasField').classList.toggle('d-none', !isKelas);
        document.getElementById('assignIndividuField').classList.toggle('d-none', isKelas);
    }

    function loadLearnersForAssign(gradeLevel) {
        const select = document.getElementById('assignLearnerSelect');
        select.innerHTML = '<option value="">Memuat data murid...</option>';
        select.disabled = true;

        if (!gradeLevel) {
            select.innerHTML = '<option value="">-- Pilih Kelas Dahulu --</option>';
            return;
        }

        fetch(`/api/learners-by-grade/${encodeURIComponent(gradeLevel)}`)
            .then(response => response.json())
            .then(learners => {
                if (!learners.length) {
                    select.innerHTML = '<option value="">Tidak ada murid di kelas ini</option>';
                    return;
                }
                select.innerHTML = '';
                learners.forEach(learner => {
                    const option = document.createElement('option');
                    option.value = learner.id;
                    option.textContent = learner.name;
                    select.appendChild(option);
                });
                select.disabled = false;
            })
            .catch(() => {
                select.innerHTML = '<option value="">Gagal memuat data murid</option>';
            });
    }
</script>
@endsection
