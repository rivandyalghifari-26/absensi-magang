<x-filament-panels::page>
    <div class="max-w-3xl mx-auto space-y-4">
        {{-- Info Status Hari Ini --}}

         @if ($hasCheckedIn && $hasCheckedOut)
         <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <p class="text-green-700 font-semibold">Anda telah melakukan presensi masuk dan keluar hari ini</p>
               <p class="text-sm text-green-600 mt-1">
                Masuk: {{ $attendance->check_in}} | Keluar: {{$attendance->check_out}}
               </p>
         </div>
         @elseif($hasCheckedIn)
         <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
            <p class="text-blue-700 font-semibold">Anda telah melakukan presensi masuk hari ini</p>
               <p class="text-sm text-blue-600 mt-1">
                Masuk: {{ $attendance->check_in}} 
               </p>
                <p class="text-blue-700 font-semibold">Jangan lupa untuk melakukan presensi keluar nanti</p>
         </div>
        @else 
                 <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
            <p class="text-yellow-700 font-semibold">Belum melakukan presensi hari ini</p>
         
         </div>
         @endif

      {{-- Kamera --}}
       <div class="bg-white rounded-xl shadow p-4">
        <p class="text-sm text-gray-500 text-center mb-3">
            Pastikan wajah anda terlihat jelas di kamera
        </p>
        <video  id="video" class="w-full rounded-lg border border-gray-300" autoplay muted playsinline></video>
       </div>

      {{-- Info GPS --}}
      <div class="text-xs text-center text-gray-500" id="gps-info">
        Mendapatkan lokasi GPS
      </div>

      {{-- Info Wajah --}}
     <div class="text-xs text-center text-yellow-500" id="face-status">
        Memuat model pengenalan wajah
      </div>


      {{-- Tombol absensi --}}
     @if (!$hasCheckedIn)
         <button disabled id="btnCheckin" 
         class="w-full bg-green-500 hover:bg-green-700 disabled:bg-gray-300 text-white font-bold py-3 rounded-xl transition">
        Check-In Sekarang
        </button>
    @elseif(!$hasCheckedOut)
       <button disabled id="btnCheckout" 
         class="w-full bg-blue-500 hover:bg-blue-700 disabled:bg-gray-300 text-white font-bold py-3 rounded-xl transition">
        Check-Out Sekarang
        </button>
        @endif

        {{-- Hasil --}}
        <div id="result" class="hidden p-4 rounded-xl text-sm font-medium text-center"></div>

    </div>
<script src="{{ asset('face-api/face-api.min.js')}}"> </script>
<script> 
    const MODEL_URL = '/face-api/models';
    const API_TOKEN = @json($token);
    const CSRF_TOKEN = @json(csrf_token());
    const HAS_CHECKED_IN = @json($hasCheckedIn);
    const HAS_CHECKED_OUT = @json($hasCheckedOut);
    const referenceDescriptor = @json($faceDescriptor);


    const video = document.getElementById('video');
    const gpsInfo = document.getElementById('gps-info');
    const faceStatus = document.getElementById('face-status');
    const btnCheckin = document.getElementById('btnCheckin');
    const btnCheckout = document.getElementById('btnCheckout');
    const resultDiv = document.getElementById('result');

    let faceDetection = false;
    let currentDetection = null;
    let currentLat = null;
    let currentLng = null;
    let detectionInterval = null;
    let modelIsLoaded = false;
    let faceVerified = false;

 //1.gps
 navigator.geolocation.getCurrentPosition(
    pos => {
        currentLat = pos.coords.latitude;
        currentLng = pos.coords.longitude;
        gpsInfo.textContent =`Lokasi GPS: ${currentLat.toFixed(5)}, ${currentLng.toFixed(5)}`;
        gpsInfo.className = 'text-xs text-center text-green-600';
        tryEnableButtons();
    },
    () => {
        gpsInfo.textContent = 'Gagal mendapatkan lokasi GPS. Pastikan izin diberikan';
        gpsInfo.className = 'text-xs text-center text-red-600';
    }, {
        enableHighAccuracy: true,
        timeout: 10000
    }

 ) 
 // kamera 
 async function initFaceApi() {
    try {
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        modelIsLoaded = true;
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'user'
            }
        });
        video.srcObject = stream;

       //deteksi wajah setiap 600ms
detectionInterval = setInterval(async () => {

    const detection = await faceapi
        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks()
        .withFaceDescriptor();

    currentDetection = detection;

    faceDetection = !!detection;

    if(faceDetection) {

        faceStatus.textContent =
            'Wajah terdeteksi. Memeriksa kecocokan...';

        faceStatus.className =
            'text-xs text-center text-yellow-600';

        const faceResult = await getFaceResult();

        if(faceResult.face_verified) {

            faceVerified = true;

            faceStatus.textContent =
                'Wajah terdeteksi. Wajah sudah cocok.';

            faceStatus.className =
                'text-xs text-center text-green-600';

        } else {

            faceVerified = false;

            faceStatus.textContent =
                'Wajah terdeteksi, tetapi wajah tidak cocok.';

            faceStatus.className =
                'text-xs text-center text-red-600';
        }

    } else {

        faceVerified = false;

        faceStatus.textContent =
            'Wajah tidak terdeteksi. Pastikan wajah terlihat jelas';

        faceStatus.className =
            'text-xs text-center text-yellow-600';
    }

    tryEnableButtons();

}, 1500);
    } catch (err) {
         faceStatus.textContent = 'Gagal memuat model pengenalan wajah';
         faceStatus.className = 'text-xs text-center text-red-600';
    }
 }
 //hidupkan button jika semua syarat terpenuhi
 function tryEnableButtons() {
    const ready = currentLat !== null && modelIsLoaded && faceDetection;
    if(btnCheckin) btnCheckin.disabled = !ready;
    if (btnCheckout) btnCheckout.disabled = !ready;

    if(ready && detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }
 }
 //hitung jarak wajah vs referensi 
 async function getFaceResult(){
    if(referenceDescriptor.length !== 128) {
        return {
            face_distance: 0,
            face_verified: true
        };
    }
    const detection = await faceapi 
    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
    .withFaceLandmarks()
    .withFaceDescriptor();

    if(!detection) {
        return null;
    }


    const refFloat = new Float32Array(referenceDescriptor);
    const distance = faceapi.euclideanDistance(detection.descriptor, refFloat);
    return {
        face_distance: distance,
        face_verified: distance < 0.5
    };
 }
 //kirim ke API 
 async function doAttendance(endpoint) {
    if(!currentLat || !currentLng) return showResult('GPS tidak tersedia', 'red');
    if(!faceDetection) return showResult('Wajah tidak terdeteksi', 'red');

    const faceResult = await getFaceResult();
    
    showResult('Memproses absensi ...', 'yellow');

    if (btnCheckin) btnCheckin.disabled = true;
    if (btnCheckout) btnCheckout.disabled = true;

    try {
        const res = await fetch(`/api/attendance/${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${API_TOKEN}`,
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({
                latitude: currentLat,
                longitude: currentLng,
                face_distance: faceResult.face_distance,
                face_verified: faceResult.face_verified
            }),
        });
        const data = await res.json();

        if (res.ok) {
            showResult(data.message, 'green');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showResult(data.message || 'Gagal melakukan absensi', 'red');
            if (btnCheckin) btnCheckin.disabled = false;
            if (btnCheckout) btnCheckout.disabled = false;

        }

    } catch (error) {
       showResult('Terjadi kesalahan saat melakukan absensi', 'red');
       if  (btnCheckin) btnCheckin.disabled = false;
       if (btnCheckout) btnCheckout.disabled = false;
    }
 }

function showResult(message, color) {
    const classes = {
        green: 'bg-green-100 text-green-700 border border-green-200',
        red: 'bg-red-100 text-red-700 border border-red-200',
        yellow: 'bg-yellow-100 text-yellow-700 border border-yellow-200'
    }
    resultDiv.className = `p-4 rounded-xl text-sm font-medium text-center ${classes[color]}`;
    resultDiv.textContent = message;
    resultDiv.style.display = 'block';
    resultDiv.classList.remove('hidden');
}
if(btnCheckin) btnCheckin.addEventListener('click', () =>doAttendance('check-in'));
if(btnCheckout) btnCheckout.addEventListener('click', () =>doAttendance('check-out'));
initFaceApi();
</script>
</x-filament-panels::page>
