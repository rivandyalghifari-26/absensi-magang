<x-filament-panels::page>
    <div class="max-w-lg mx-auto space-y-4">

    <div class="bg-white rounded-xl shadow p-4">
        <h3 class="font-semibold text-gray-700 mb-1">
            Magang: {{ auth()->user()->name }}
        </h3>
        <p class="text-sm text-gray-500">
            Kode: {{ auth()->user()->intern?->intern_code }} |
            Status Wajah : 

            @if (auth()->user()->intern?->face_descriptor)
            <span class="text-gray-600 font-medium">
                Sudah disetup
            </span>
            @else 
            <span class="text-yellow-600 font-medium">Belum disetup</span>
            @endif
        </p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-sm text-gray-500 mb-3">
            Posisikan wajah di tengah frame, lalu klik <strong>Simpan Data Wajah</strong>
        </p>
        <video id="video"
    width="400"
    height="300"
    class="pointer-events-none rounded-lg mx-auto border border-white w-full"
    autoplay muted>
</video>
    </div>
    <div id="status" class="text-center text-sm text-gray-400">
        Sedang memuat pengenalan wajah...
    </div>
    <x-filament::button id="btnSave" class="w-full" disabled>
        Simpan Data Wajah
    </x-filament::button>
  

    </div>

    <script src="{{ asset('face-api/face-api.min.js')}}"> </script>
    <script>
        const video = document.getElementById('video');
         const status = document.getElementById('status');
          const btnSave = document.getElementById('btnSave');
           const btnReset = document.getElementById('btnReset');
            const CSRF = '{{csrf_token()}}';
            console.log('btnSave:', btnSave);

            async function init() {
                await faceapi.nets.tinyFaceDetector.loadFromUri('/face-api/models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('/face-api/models');
                await faceapi.nets.faceRecognitionNet.loadFromUri('/face-api/models');

                status.textContent = 'Siap digunakan dan arahkan kamera ke wajah';

                const stream = await navigator.mediaDevices.getUserMedia({
                 video: true

                });
                video.srcObject = stream;

                setInterval(async () => {
                    const detection = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                      if (detection) {
        status.textContent = 'Wajah berhasil terdeteksi dan siap disimpan';
        status.className = 'text-center text-sm text-green-600 font-medium';
        btnSave.removeAttribute('disabled');
        btnSave.removeAttribute('aria-disabled');
        btnSave.classList.remove('fi-disabled');

        btnSave.disabled = false;
        btnSave._descriptor = Array.from(detection.descriptor);

        
        btnSave.classList.remove('opacity-50');
        btnSave.classList.add('bg-blue-600', 'hover:bg-blue-700', 'text-white');

    } else {
        status.textContent = 'Wajah tidak terdeteksi...';
        status.className = 'text-center text-sm text-yellow-500';

        btnSave.disabled = true;

        
        btnSave.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'text-white');
        btnSave.classList.add('opacity-50');
    }
}, 500);
            }
            btnSave.addEventListener('click', async () => {
                if(!btnSave._descriptor) return;
                btnSave.disabled = true;
                btnSave.textContent = "Sedang Menyimpan...";

               const res = await fetch('/app/setup-face/store', {
    method: 'POST',
    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        descriptor: btnSave._descriptor
                    })
                });

                if(res.ok){
                    btnSave.textContent = 'Data wajah berhasil tersimpan';
                    btnSave.className= btnSave.className.replace('bg-blue-600', 'bg-green-600');
                    setTimeout(() => {
                        window.location.reload();

                    }, 1500);
                } else {
                    btnSave.textContent = 'Gagal menyimpan';
                    btnSave.disabled = false;
                }


            })
           

            init()

    </script>
</x-filament-panels::page>
