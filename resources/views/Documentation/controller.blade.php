@extends('Documentation.layout')

@section('title', 'Controller - Dokumentasi')
@section('header', 'Controller')
@section('breadcrumb')
    <nav class="mb-4 text-sm text-blue-700 font-medium flex items-center space-x-2">
        <a href="{{ route('documentation.index') }}" class="hover:underline">Dokumentasi</a>
        <span>/</span>
        <span class="text-blue-900">Controller</span>
    </nav>
@endsection
@section('content')
    <div class="prose max-w-none">
        <h2 class="text-2xl font-bold mb-4">Penjelasan Controller dalam Aplikasi K-Means Clustering</h2>
        
        <p class="mb-4">
            Controller dalam Laravel berfungsi sebagai perantara antara Model dan View, menangani permintaan HTTP, memproses data, dan mengembalikan respons yang sesuai. Pada aplikasi K-Means Clustering ini, controller berperan penting dalam mengelola alur kerja aplikasi, mulai dari autentikasi pengguna, pengelolaan data penerima bantuan, proses clustering, hingga pengambilan keputusan. Berikut adalah penjelasan setiap controller yang digunakan beserta fungsinya dalam aplikasi.
        </p>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">1. AuthController</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class AuthController extends Controller
{
    public function showLoginForm() { ... }
    public function login(Request $request) { ... }
    public function showRegisterForm() { ... }
    public function register(Request $request) { ... }
    public function logout(Request $request) { ... }
}</code></pre>
            </div>
            <p>
                Controller <b>AuthController</b> menangani proses autentikasi pengguna dalam aplikasi, termasuk login, registrasi, dan logout. Fungsi <code>showLoginForm()</code> dan <code>showRegisterForm()</code> bertanggung jawab untuk menampilkan halaman login dan registrasi. Fungsi <code>login()</code> memproses permintaan login dengan memvalidasi kredensial yang dimasukkan pengguna dan membuat sesi autentikasi jika kredensial valid. Fungsi <code>register()</code> memproses pendaftaran pengguna baru dengan memvalidasi data yang dimasukkan, membuat pengguna baru dalam database, dan langsung mengautentikasi pengguna tersebut. Terakhir, fungsi <code>logout()</code> mengakhiri sesi pengguna dan mengarahkannya kembali ke halaman login. Controller ini sangat penting untuk keamanan aplikasi dan manajemen akses pengguna.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">2. BeneficiaryController</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class BeneficiaryController extends Controller
{
    public function dashboard() { ... }
    public function index(Request $request) { ... }
    public function create() { ... }
    public function store(Request $request) { ... }
    public function edit($id) { ... }
    public function update(Request $request, $id) { ... }
    public function destroy($id) { ... }
    public function exportExcel(Request $request) { ... }
    public function importExcel(Request $request) { ... }
    public function bulkDelete(Request $request) { ... }
}</code></pre>
            </div>
            <p>
                Controller <b>BeneficiaryController</b> mengelola seluruh operasi terkait data penerima bantuan, yang merupakan data utama dalam aplikasi ini. Fungsi <code>dashboard()</code> menyiapkan data untuk tampilan dashboard utama, termasuk statistik jumlah penerima bantuan dan distribusi cluster. Fungsi <code>index()</code> menampilkan daftar penerima bantuan dengan fitur pencarian dan paginasi. Fungsi <code>create()</code> dan <code>store()</code> menangani proses penambahan data penerima baru, termasuk validasi input. Fungsi <code>edit()</code> dan <code>update()</code> memungkinkan pengeditan data penerima yang sudah ada. Fungsi <code>destroy()</code> menghapus data penerima beserta data terkait seperti hasil normalisasi dan clustering. 
            </p>
            <p>
                Selain itu, controller ini juga menyediakan fungsi untuk manajemen data secara massal, seperti <code>exportExcel()</code> untuk mengekspor data ke format Excel, <code>importExcel()</code> untuk mengimpor data dari file Excel, dan <code>bulkDelete()</code> untuk menghapus banyak data sekaligus. Controller ini sangat penting dalam aplikasi karena mengelola data dasar yang akan diproses dalam algoritma clustering.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">3. StatisticController</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class StatisticController extends Controller
{
    public function index() { ... }
    public function recalculate(Request $request) { ... }
    public function showCluster($cluster, Request $request) { ... }
    private function normalizeData($data, array $usiaValues, ...) { ... }
    public function doClustering(Request $request, $successMessage = null) { ... }
    private function calculateSilhouetteScores(array $samples, array $labels) { ... }
    private function calculateAverageDistance(array $point, array $points) { ... }
    private function euclideanDistance(array $point1, array $point2) { ... }
}</code></pre>
            </div>
            <p>
                Controller <b>StatisticController</b> merupakan inti dari aplikasi K-Means Clustering ini, menangani seluruh proses terkait analisis statistik dan algoritma clustering. Fungsi <code>index()</code> menampilkan halaman statistik utama dengan visualisasi hasil clustering jika data sudah diproses, atau formulir untuk memulai clustering jika belum. Fungsi <code>doClustering()</code> adalah fungsi utama yang mengimplementasikan algoritma K-Means menggunakan library Rubix ML, termasuk proses normalisasi data, pengelompokan, dan perhitungan silhouette score untuk evaluasi kualitas clustering.
            </p>
            <p>
                Fungsi <code>recalculate()</code> memungkinkan pengguna untuk menjalankan ulang proses clustering dengan parameter berbeda, seperti jumlah cluster atau metode normalisasi. Fungsi <code>showCluster()</code> menampilkan detail lengkap untuk cluster tertentu, termasuk anggota cluster dan statistik deskriptif. Controller ini juga memiliki beberapa fungsi pembantu privat seperti <code>normalizeData()</code> untuk menormalisasi data sebelum clustering, <code>calculateSilhouetteScores()</code> untuk menghitung skor silhouette yang mengukur kualitas clustering, dan <code>euclideanDistance()</code> untuk menghitung jarak antar titik data. Controller ini sangat penting dalam implementasi algoritma K-Means dan analisis hasil clustering.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">4. DecisionController</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class DecisionController extends Controller
{
    public function index() { ... }
    public function create() { ... }
    public function store(Request $request) { ... }
    public function show($id) { ... }
    public function destroy($id) { ... }
}</code></pre>
            </div>
            <p>
                Controller <b>DecisionController</b> bertanggung jawab untuk mengelola proses pengambilan keputusan berdasarkan hasil clustering. Fungsi <code>index()</code> menampilkan halaman utama panel keputusan dengan informasi tentang distribusi cluster dan daftar keputusan yang telah dibuat sebelumnya. Fungsi <code>create()</code> menampilkan formulir untuk membuat keputusan baru, termasuk perhitungan prioritas cluster berdasarkan rata-rata fitur. 
            </p>
            <p>
                Fungsi <code>store()</code> memproses pembuatan keputusan baru dengan memilih penerima bantuan berdasarkan cluster yang dipilih dan jumlah yang diinginkan. Fungsi ini mengimplementasikan logika untuk memilih penerima bantuan secara acak dari cluster tertentu atau dari semua cluster dengan mempertimbangkan prioritas. Fungsi <code>show()</code> menampilkan detail lengkap dari keputusan yang telah dibuat, termasuk daftar penerima bantuan yang terpilih. Fungsi <code>destroy()</code> menghapus keputusan yang tidak diperlukan lagi. Controller ini sangat penting dalam mengimplementasikan tujuan akhir aplikasi, yaitu menggunakan hasil clustering untuk mendukung proses pengambilan keputusan dalam distribusi bantuan.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">5. ProfileController</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class ProfileController extends Controller
{
    public function edit() { ... }
    public function update(Request $request) { ... }
    public function updatePassword(Request $request) { ... }
    public function updateAvatar(Request $request) { ... }
}</code></pre>
            </div>
            <p>
                Controller <b>ProfileController</b> menangani operasi terkait profil pengguna dalam aplikasi. Fungsi <code>edit()</code> menampilkan halaman pengaturan profil pengguna yang sedang login. Fungsi <code>update()</code> memproses pembaruan informasi dasar pengguna seperti nama dan email, dengan validasi untuk memastikan email tetap unik di database. Fungsi <code>updatePassword()</code> menangani pembaruan kata sandi dengan validasi kata sandi saat ini dan persyaratan untuk kata sandi baru. Fungsi <code>updateAvatar()</code> memproses pengunggahan dan pembaruan foto profil pengguna, termasuk penghapusan foto lama jika ada. Controller ini penting untuk memberikan pengalaman pengguna yang baik dan memungkinkan pengguna mengelola informasi pribadi mereka dalam aplikasi.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">6. DocumentationController</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class DocumentationController extends Controller
{
    public function index() { ... }
    public function model() { ... }
    public function view() { ... }
    public function controller() { ... }
    public function route() { ... }
    public function middleware() { ... }
    public function migration() { ... }
}</code></pre>
            </div>
            <p>
                Controller <b>DocumentationController</b> mengelola halaman dokumentasi aplikasi, yang menyediakan informasi tentang struktur dan komponen aplikasi. Fungsi <code>index()</code> menampilkan halaman utama dokumentasi dengan daftar topik yang tersedia. Fungsi-fungsi lainnya seperti <code>model()</code>, <code>view()</code>, <code>controller()</code>, <code>route()</code>, <code>middleware()</code>, dan <code>migration()</code> menampilkan halaman dokumentasi untuk masing-masing komponen aplikasi. Controller ini sangat berguna untuk pengembangan dan pemeliharaan aplikasi, serta sebagai referensi bagi pengembang baru yang ingin memahami struktur aplikasi.
            </p>
        </div>

        <h2 class="text-2xl font-bold mb-4">Konsep-konsep Penting dalam Controller Laravel</h2>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">1. Resource Controller</h3>
            <p>
                Laravel menyediakan konsep Resource Controller yang mengimplementasikan pola CRUD (Create, Read, Update, Delete) secara standar. Dalam aplikasi ini, BeneficiaryController mengikuti pola resource controller dengan metode index(), create(), store(), edit(), update(), dan destroy(). Pendekatan ini memberikan struktur yang konsisten dan mudah dipahami untuk operasi dasar pada sumber daya aplikasi.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">2. Request Validation</h3>
            <p>
                Validasi request adalah aspek penting dalam controller Laravel untuk memastikan data yang diterima memenuhi kriteria yang ditentukan sebelum diproses lebih lanjut. Dalam aplikasi ini, metode seperti store() dan update() pada BeneficiaryController menggunakan fungsi validate() untuk memvalidasi input pengguna. Validasi ini mencegah data yang tidak valid masuk ke database dan memberikan umpan balik yang jelas kepada pengguna.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">3. Dependency Injection</h3>
            <p>
                Laravel menggunakan dependency injection untuk menyediakan instance kelas yang diperlukan oleh controller. Contohnya, parameter Request $request yang digunakan dalam banyak metode controller secara otomatis diisi dengan instance Request yang sesuai. Pendekatan ini memudahkan pengujian dan membuat kode lebih modular.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">4. Route Model Binding</h3>
            <p>
                Route model binding memungkinkan controller menerima instance model langsung dari parameter rute. Misalnya, pada metode edit($id) di BeneficiaryController, parameter $id digunakan untuk mengambil instance Beneficiary yang sesuai. Fitur ini menyederhanakan kode dan menghindari pengulangan logika pencarian model.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">5. Middleware</h3>
            <p>
                Controller dalam Laravel sering menggunakan middleware untuk memfilter HTTP request sebelum mencapai metode controller. Dalam aplikasi ini, middleware autentikasi digunakan untuk memastikan hanya pengguna yang sudah login yang dapat mengakses fitur-fitur tertentu. Middleware memberikan lapisan keamanan dan kontrol akses yang penting dalam aplikasi web.
            </p>
        </div>

        <h2 class="text-2xl font-bold mb-4">Kesimpulan</h2>
        <p>
            Controller dalam aplikasi K-Means Clustering ini dirancang untuk mendukung seluruh proses dalam sistem, mulai dari autentikasi pengguna, pengelolaan data penerima bantuan, implementasi algoritma clustering, hingga pengambilan keputusan berdasarkan hasil clustering. Setiap controller memiliki tanggung jawab yang jelas dan terfokus, mengikuti prinsip Single Responsibility dalam desain perangkat lunak. Dengan struktur yang terorganisir dan pemanfaatan fitur-fitur Laravel seperti validasi request, dependency injection, dan middleware, controller dalam aplikasi ini memberikan fondasi yang solid untuk implementasi logika aplikasi dan interaksi dengan pengguna.
        </p>
    </div>
@endsection 