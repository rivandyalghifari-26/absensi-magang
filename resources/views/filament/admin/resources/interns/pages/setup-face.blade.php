<x-filament-panels::page>
    @php
        $internRecord = $this->record ?? $record ?? null;
        $hasFace = !empty($internRecord?->face_descriptor);
    @endphp

    <div class="max-w-lg mx-auto space-y-4">
        {{-- Card Info Peserta --}}
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="font-semibold text-gray-700 mb-1">
                Magang: {{ $internRecord?->user?->name ?? 'Peserta' }}
            </h3>
            <p class="text-sm text-gray-500">
                Kode: {{ $internRecord?->intern_code }} | Status Wajah:
                @if ($hasFace)
                    <span class="text-green-600 font-medium">Sudah disetup</span>
                @else
                    <span class="text-yellow-600 font-medium">Belum disetup</span>
                @endif
            </p>
        </div>

        @if (!$hasFace)
            {{-- Bagian Kamera: Hanya tampil jika BELUM setup --}}
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-sm text-gray-500 mb-3">
                    Posisikan wajah di tengah frame, lalu klik <strong>Simpan Data Wajah</strong>
                </p>
                <div class="flex justify-center">
                    <video id="video" class="w-72 h-52 object-cover rounded-lg border border-gray-300" autoplay muted playsinline></video>
                </div>
            </div>

            <div id="status" class="text-center text-sm text-gray-400">
                Sedang memuat pengenalan wajah...
            </div>

            <x-filament::button id="btnSave" class="w-full" disabled>
                Simpan Data Wajah
            </x-filament::button>

            <script src="{{ asset('face-api/face-api.min.js') }}"></script>
            <script>
                const video = document.getElementById('video');
                const status = document.getElementById('status');
                const btnSave = document.getElementById('btnSave');
                const CSRF = '{{ csrf_token() }}';

                async function startCamera() {
                    try {
                        await faceapi.nets.tinyFaceDetector.loadFromUri('/face-api/models');
                        await faceapi.nets.faceLandmark68Net.loadFromUri('/face-api/models');
                        await faceapi.nets.faceRecognitionNet.loadFromUri('/face-api/models');

                        status.textContent = 'Arahkan kamera ke wajah...';

                        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        video.srcObject = stream;

                        setInterval(async () => {
                            try {
                                const detection = await faceapi
                                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                                    .withFaceLandmarks()
                                    .withFaceDescriptor();

                                if (detection) {
                                    status.textContent = 'Wajah berhasil terdeteksi dan siap disimpan';
                                    status.className = 'text-center text-sm text-green-600 font-medium';
                                    if (btnSave) {
                                        btnSave.removeAttribute('disabled');
                                        btnSave.removeAttribute('aria-disabled');
                                        btnSave.classList.remove('fi-disabled');
                                        btnSave.disabled = false;
                                        btnSave._descriptor = Array.from(detection.descriptor);
                                    }
                                } else {
                                    status.textContent = 'Wajah tidak terdeteksi...';
                                    status.className = 'text-center text-sm text-yellow-500';
                                    if (btnSave) {
                                        btnSave.disabled = true;
                                    }
                                }
                            } catch (e) {
                                console.error(e);
                            }
                        }, 500);
                    } catch (err) {
                        status.textContent = 'Gagal memuat kamera: ' + err.message;
                        status.className = 'text-center text-sm text-red-500';
                    }
                }

                if (btnSave) {
        btnSave.addEventListener('click', async () => {
            if (!btnSave._descriptor) return;
            btnSave.disabled = true;
            btnSave.textContent = "Sedang Menyimpan...";

            try {
                const res = await fetch('{{ route('intern.face.store', $internRecord) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({ descriptor: btnSave._descriptor })
                });

                if (res.ok) {
                    new FilamentNotification()
                        .title('Data wajah berhasil tersimpan')
                        .success()
                        .send();

                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    new FilamentNotification()
                        .title('Gagal menyimpan data wajah')
                        .danger()
                        .send();

                    btnSave.disabled = false;
                    btnSave.textContent = "Simpan Data Wajah";
                }
            } catch (error) {
                new FilamentNotification()
                    .title('Terjadi kesalahan jaringan')
                    .danger()
                    .send();

                btnSave.disabled = false;
                btnSave.textContent = "Simpan Data Wajah";
            }
        });
    }

                startCamera();
            </script>
        @else
            {{-- Bagian Hapus: Tampil jika SUDAH setup --}}
            <div class="bg-white rounded-xl shadow p-6 text-center space-y-2">
               <p class="text-green-600 font-semibold text-base">
                    ✓ Data wajah sudah terdaftar untuk magang ini.
                </p>
                <p class="text-xs text-gray-400">
                    Kamera dinonaktifkan. Silakan hapus data jika ingin melakukan re-scan.
                </p>
            </div>

            <x-filament::modal id="confirm-delete-face" width="md" alignment="center">
                <x-slot name="trigger">
                    <x-filament::button color="danger" outlined class="w-full">
                        Hapus Data Wajah
                    </x-filament::button>
                </x-slot>

                <x-slot name="heading">
                    Hapus Data Wajah
                </x-slot>

                <x-slot name="description">
                    Apakah Anda yakin ingin menghapus data wajah ini? Tindakan ini tidak dapat dibatalkan.
                </x-slot>

                <div class="flex justify-end gap-x-3 mt-4">
                    <x-filament::button color="gray" x-on:click="close">
                        Batal
                    </x-filament::button>

                    <x-filament::button color="danger" id="btnConfirmDelete">
                        Ya, Hapus
                    </x-filament::button>
                </div>
            </x-filament::modal>
        @endif
            </div>

            <script>
             const btnConfirmDelete = document.getElementById('btnConfirmDelete');
            const CSRF = '{{ csrf_token() }}';

            if (btnConfirmDelete) {
                btnConfirmDelete.addEventListener('click', async () => {
                    btnConfirmDelete.disabled = true;

                    try {
                        const res = await fetch('{{ route('intern.face.destroy', $internRecord) }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': CSRF
                            }
                        });

                        if (res.ok) {
                            new FilamentNotification()
                                .title('Data wajah berhasil terhapus')
                                .success()
                                .send();

                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            new FilamentNotification()
                                .title('Gagal menghapus data wajah')
                                .danger()
                                .send();

                            btnConfirmDelete.disabled = false;
                        }
                    } catch (error) {
                        new FilamentNotification()
                            .title('Terjadi kesalahan jaringan')
                            .danger()
                            .send();

                        btnConfirmDelete.disabled = false;
                    }
                });
            }
        </script>
</x-filament-panels::page>