<x-filament-panels::page>
    @php
        $intern = auth()->user()->intern;
        $hasFace = !empty($intern?->face_descriptor);
    @endphp

    <div class="max-w-lg mx-auto space-y-4">
        {{-- Info Peserta --}}
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="font-semibold text-gray-700 mb-1">
                Magang: {{ auth()->user()->name }}
            </h3>
            <p class="text-sm text-gray-500">
                Kode: {{ $intern?->intern_code }} | Status Wajah:
                @if ($hasFace)
                    <span class="text-green-600 font-medium">Sudah disetup</span>
                @else
                    <span class="text-yellow-600 font-medium">Belum disetup</span>
                @endif
            </p>
        </div>

        @if (!$hasFace)
            {{-- Form Kamera: Hanya tampil jika BELUM terdaftar --}}
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

                        status.textContent = 'Arahkan wajah ke kamera...';

                        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        video.srcObject = stream;

                        setInterval(async () => {
                            try {
                                const detection = await faceapi
                                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                                    .withFaceLandmarks()
                                    .withFaceDescriptor();

                                if (detection) {
                                    status.textContent = 'Wajah terdeteksi dan siap disimpan';
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
                        status.textContent = 'Kamera tidak dapat diakses: ' + err.message;
                        status.className = 'text-center text-sm text-red-500';
                    }
                }

                if (btnSave) {
                    btnSave.addEventListener('click', async () => {
                        if (!btnSave._descriptor) return;
                        btnSave.disabled = true;
                        btnSave.textContent = "Sedang Menyimpan...";

                        try {
                            const res = await fetch('{{ route('intern.face.store', $intern) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': CSRF
                                },
                                body: JSON.stringify({ descriptor: btnSave._descriptor })
                            });

                            if (res.ok) {
                                new FilamentNotification()
                                    .title('Data wajah berhasil didaftarkan')
                                    .success()
                                    .send();

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
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
            {{-- Jika SUDAH didaftarkan --}}
            <div class="bg-white rounded-xl shadow p-6 text-center space-y-2">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-2 text-xl font-bold">
                    ✓
                </div>
                <p class="text-green-600 font-semibold text-base">
                    Data wajah Anda sudah terdaftar di sistem.
                </p>
                <p class="text-xs text-gray-500">
                    Anda sudah dapat melakukan presensi kehadiran. Jika ingin memperbarui data wajah, silakan hubungi Administrator.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>