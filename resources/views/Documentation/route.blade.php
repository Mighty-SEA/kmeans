@extends('Documentation.layout')

@section('title', 'Route - Dokumentasi')
@section('header', 'Route')
@section('breadcrumb')
    <nav class="mb-4 text-sm text-blue-700 font-medium flex items-center space-x-2">
        <a href="{{ route('documentation.index') }}" class="hover:underline">Dokumentasi</a>
        <span>/</span>
        <span class="text-blue-900">Route</span>
    </nav>
@endsection
@section('content')
    <div class="prose max-w-none">
        <h2 class="text-2xl font-bold mb-4">Penjelasan Routing pada Aplikasi K-Means Clustering</h2>
        <p class="mb-4">
            Routing merupakan salah satu komponen fundamental dalam aplikasi Laravel yang menentukan bagaimana aplikasi merespons permintaan pada URL tertentu. Dalam aplikasi K-Means Clustering ini, route dikonfigurasi untuk mengatur alur navigasi, autentikasi, dan pengaksesan berbagai fitur dalam sistem. Berikut adalah penjelasan detail mengenai setiap bagian routing dalam aplikasi.
        </p>
        
        <h3 class="text-xl font-bold mb-2">1. Impor Controller</h3>
        <div class="bg-gray-50 p-4 rounded-lg mb-3">
            <pre><code class="language-php">use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Beneficiary\BeneficiaryController;
use App\Http\Controllers\Statistic\StatisticController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Decision\DecisionController;
use App\Http\Controllers\Documentation\DocumentationController;</code></pre>
        </div>
        <p class="mb-6">
            Bagian ini mengimpor class controller yang akan digunakan dalam definisi route aplikasi. Penggunaan namespace lengkap seperti ini mengikuti standar PSR-4 autoloading yang diadopsi oleh Laravel. Dengan mengimpor controller di awal file, kode routing menjadi lebih bersih dan mudah dibaca karena tidak perlu menulis namespace lengkap pada setiap definisi route. Praktik ini juga memudahkan manajemen kode jika terjadi perubahan struktur namespace di masa depan, karena perubahan hanya perlu dilakukan pada bagian impor ini.
        </p>
        
        <h3 class="text-xl font-bold mb-2">2. Route Autentikasi</h3>
        <div class="bg-gray-50 p-4 rounded-lg mb-3">
            <pre><code class="language-php">// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');</code></pre>
        </div>
        <p class="mb-4">
            Route autentikasi mengatur akses ke fungsi-fungsi yang berhubungan dengan proses masuk dan keluar dari sistem. Berikut penjelasan detail setiap route:
        </p>
        
        <div class="mb-6">
            <h4 class="text-lg font-semibold mb-2">Route::middleware(['guest'])->group()</h4>
            <p class="mb-4">
                Bagian ini menggunakan middleware 'guest' untuk mengelompokkan route-route yang hanya boleh diakses oleh pengunjung yang belum terautentikasi. Middleware 'guest' berperan sebagai penjaga yang akan mengarahkan pengguna yang sudah login ke halaman dashboard jika mencoba mengakses halaman login atau register. Penggunaan group routing seperti ini meningkatkan efisiensi kode dan memudahkan pengelolaan hak akses secara terpusat.
            </p>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')</h5>
                <p class="mb-3">
                    Route ini menangani permintaan GET ke URL '/login'. Ketika diakses, sistem akan memanggil method 'showLoginForm' pada AuthController yang bertugas menampilkan form login kepada pengguna. Pemberian nama 'login' pada route ini memungkinkan penggunaan fungsi helper route('login') di berbagai bagian aplikasi, sehingga jika URL login diubah di masa depan, referensi ke route tersebut tetap valid.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::post('/login', [AuthController::class, 'login'])</h5>
                <p class="mb-3">
                    Route ini menangani permintaan POST ke URL '/login' yang terjadi ketika pengguna mengirimkan form login. Data yang dikirim akan diproses oleh method 'login' pada AuthController, yang bertugas memvalidasi kredensial dan menginisialisasi sesi pengguna jika kredensial valid. Penggunaan metode POST sesuai dengan prinsip HTTP yang mengharuskan operasi yang mengubah state server (dalam hal ini, membuat sesi) tidak dilakukan melalui GET.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')</h5>
                <p class="mb-3">
                    Route ini menangani permintaan GET ke URL '/register' dan memanggil method 'showRegisterForm' pada AuthController untuk menampilkan formulir pendaftaran akun baru. Sama seperti route login, route ini juga diberi nama 'register' untuk memudahkan referensi melalui fungsi helper route().
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::post('/register', [AuthController::class, 'register'])</h5>
                <p class="mb-3">
                    Route ini menangani permintaan POST ke URL '/register' yang terjadi ketika pengguna mengirimkan form pendaftaran. Method 'register' pada AuthController akan memvalidasi data yang dikirim, membuat akun user baru di database, dan biasanya langsung melakukan proses login untuk akun tersebut.
                </p>
            </div>
        </div>
        
        <div class="mb-6">
            <h4 class="text-lg font-semibold mb-2">Route::post('/logout', [AuthController::class, 'logout'])->name('logout')</h4>
            <p class="mb-4">
                Route ini berada di luar grup middleware 'guest' karena fungsinya yang berkebalikan - hanya bisa diakses oleh pengguna yang sudah login. Ketika pengguna mengirimkan permintaan logout, method 'logout' pada AuthController akan mengakhiri sesi, menghapus data autentikasi, dan mengarahkan pengguna kembali ke halaman login. Penggunaan metode POST untuk logout, bukan GET, adalah praktik keamanan yang baik untuk mencegah logout tidak disengaja atau serangan CSRF.
            </p>
        </div>
        
        <h3 class="text-xl font-bold mb-2">3. Route yang Dilindungi (Protected Routes)</h3>
        <div class="bg-gray-50 p-4 rounded-lg mb-3">
            <pre><code class="language-php">// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [BeneficiaryController::class, 'dashboard']);
    Route::resource('beneficiary', BeneficiaryController::class);
    Route::get('/statistic', [StatisticController::class, 'index'])->name('statistic.index');
    Route::get('/statistic/cluster/{cluster}', [StatisticController::class, 'showCluster'])->name('statistic.cluster');
    Route::post('/statistic/recalculate', [StatisticController::class, 'recalculate'])->name('statistic.recalculate');
    Route::post('/statistic/clustering', [StatisticController::class, 'doClustering'])->name('statistic.clustering');
    Route::post('beneficiary-export', [BeneficiaryController::class, 'exportExcel'])->name('beneficiary.export');
    Route::post('beneficiary-import', [BeneficiaryController::class, 'importExcel'])->name('beneficiary.import');
    Route::delete('beneficiary-bulk-delete', [BeneficiaryController::class, 'bulkDelete'])->name('beneficiary.bulkDelete');
    
    // Decision Panel Routes
    Route::get('/decision', [DecisionController::class, 'index'])->name('decision.index');
    Route::get('/decision/create', [DecisionController::class, 'create'])->name('decision.create');
    Route::post('/decision', [DecisionController::class, 'store'])->name('decision.store');
    Route::get('/decision/{id}', [DecisionController::class, 'show'])->name('decision.show');
    Route::delete('/decision/{id}', [DecisionController::class, 'destroy'])->name('decision.destroy');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
});</code></pre>
        </div>
        <p class="mb-4">
            Bagian ini mendefinisikan route-route yang hanya dapat diakses oleh pengguna yang sudah login (terautentikasi). Penggunaan middleware 'auth' memastikan bahwa semua route di dalamnya terlindungi dan akan mengarahkan pengunjung yang belum login ke halaman login. Berikut penjelasan detail untuk setiap kelompok route:
        </p>
        
        <div class="mb-6">
            <h4 class="text-lg font-semibold mb-2">Route Utama dan Beneficiary</h4>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/', [BeneficiaryController::class, 'dashboard'])</h5>
                <p class="mb-3">
                    Route ini mendefinisikan halaman utama (root URL) aplikasi yang akan menampilkan dashboard dengan memanggil method 'dashboard' pada BeneficiaryController. Dashboard ini berisi ringkasan data penerima bantuan, statistik clustering, dan informasi penting lainnya yang diperlukan untuk monitoring dan pengambilan keputusan.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::resource('beneficiary', BeneficiaryController::class)</h5>
                <p class="mb-3">
                    Route resource ini secara otomatis membuat 7 route standar CRUD (Create, Read, Update, Delete) untuk pengelolaan data penerima bantuan:
                </p>
                <ul class="list-disc ml-6">
                    <li><b>GET /beneficiary</b> - Menampilkan daftar penerima (method 'index')</li>
                    <li><b>GET /beneficiary/create</b> - Menampilkan form pembuatan data penerima baru (method 'create')</li>
                    <li><b>POST /beneficiary</b> - Menyimpan data penerima baru (method 'store')</li>
                    <li><b>GET /beneficiary/{id}</b> - Menampilkan detail satu penerima (method 'show')</li>
                    <li><b>GET /beneficiary/{id}/edit</b> - Menampilkan form edit penerima (method 'edit')</li>
                    <li><b>PUT/PATCH /beneficiary/{id}</b> - Memperbarui data penerima (method 'update')</li>
                    <li><b>DELETE /beneficiary/{id}</b> - Menghapus data penerima (method 'destroy')</li>
                </ul>
                <p class="mt-2">
                    Penggunaan resource route merupakan implementasi dari pola RESTful API, menyediakan antarmuka yang konsisten dan standar untuk pengelolaan resource, dalam hal ini data penerima bantuan. Pendekatan ini juga meningkatkan keterbacaan kode dengan mengurangi jumlah definisi route yang perlu ditulis secara manual.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::post('beneficiary-export', [BeneficiaryController::class, 'exportExcel'])->name('beneficiary.export')</h5>
                <p class="mb-3">
                    Route ini menangani permintaan untuk mengekspor data penerima bantuan ke dalam format Excel. Method 'exportExcel' pada BeneficiaryController bertanggung jawab untuk mengambil data dari database, memformatnya, dan menghasilkan file Excel yang dapat diunduh. Penggunaan metode POST memungkinkan pengiriman parameter tambahan seperti filter atau kolom yang ingin diekspor.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::post('beneficiary-import', [BeneficiaryController::class, 'importExcel'])->name('beneficiary.import')</h5>
                <p class="mb-3">
                    Route ini menangani permintaan untuk mengimpor data penerima bantuan dari file Excel. Method 'importExcel' pada BeneficiaryController akan memproses file yang diunggah, mengekstrak data, memvalidasinya, dan menyimpannya ke database. Penggunaan metode POST diperlukan karena route ini melibatkan unggahan file.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::delete('beneficiary-bulk-delete', [BeneficiaryController::class, 'bulkDelete'])->name('beneficiary.bulkDelete')</h5>
                <p class="mb-3">
                    Route ini memungkinkan penghapusan massal data penerima bantuan. Method 'bulkDelete' pada BeneficiaryController akan memproses daftar ID yang diterima dan menghapus semua data yang sesuai dalam satu operasi. Penggunaan metode DELETE sesuai dengan standar RESTful untuk operasi penghapusan dan memberikan kejelasan intent dibandingkan menggunakan POST untuk operasi destruktif.
                </p>
            </div>
        </div>
        
        <div class="mb-6">
            <h4 class="text-lg font-semibold mb-2">Route Statistik</h4>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/statistic', [StatisticController::class, 'index'])->name('statistic.index')</h5>
                <p class="mb-3">
                    Route ini menampilkan halaman utama statistik yang berisi visualisasi dan ringkasan hasil clustering data penerima bantuan. Method 'index' pada StatisticController bertugas menyiapkan data-data statistik seperti jumlah anggota per cluster, centroid, dan perhitungan evaluasi clustering seperti silhouette score.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/statistic/cluster/{cluster}', [StatisticController::class, 'showCluster'])->name('statistic.cluster')</h5>
                <p class="mb-3">
                    Route ini menampilkan detail informasi untuk cluster tertentu, yang diidentifikasi oleh parameter {cluster}. Method 'showCluster' pada StatisticController akan mengambil dan menampilkan data spesifik untuk cluster tersebut, seperti daftar anggota, karakteristik cluster, dan statistik lain yang relevan. Parameter {cluster} merupakan contoh route parameter yang memungkinkan penanganan data dinamis dalam route.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::post('/statistic/recalculate', [StatisticController::class, 'recalculate'])->name('statistic.recalculate')</h5>
                <p class="mb-3">
                    Route ini menangani permintaan untuk melakukan perhitungan ulang statistik clustering. Method 'recalculate' pada StatisticController akan menjalankan kembali algoritma perhitungan yang diperlukan tanpa mengubah hasil clustering itu sendiri. Operasi ini berguna ketika ada perubahan pada parameter visualisasi atau ketika ingin menyegarkan data statistik.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::post('/statistic/clustering', [StatisticController::class, 'doClustering'])->name('statistic.clustering')</h5>
                <p class="mb-3">
                    Route ini menghandle permintaan untuk menjalankan proses clustering data. Method 'doClustering' pada StatisticController bertanggung jawab untuk menjalankan algoritma K-Means, mengolah data input, membentuk cluster, dan menyimpan hasilnya. Route ini menggunakan metode POST karena proses clustering mengubah state data di server dan mungkin memerlukan parameter input seperti jumlah cluster atau metode normalisasi.
                </p>
            </div>
        </div>
        
        <div class="mb-6">
            <h4 class="text-lg font-semibold mb-2">Route Keputusan (Decision)</h4>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/decision', [DecisionController::class, 'index'])->name('decision.index')</h5>
                <p class="mb-3">
                    Route ini menampilkan halaman daftar keputusan distribusi bantuan yang telah dibuat. Method 'index' pada DecisionController mengambil data keputusan dari database dan menyajikannya dalam bentuk tabel yang informatif, memudahkan pengguna untuk melihat riwayat dan status keputusan distribusi bantuan.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/decision/create', [DecisionController::class, 'create'])->name('decision.create')</h5>
                <p class="mb-3">
                    Route ini menampilkan formulir untuk membuat keputusan distribusi bantuan baru. Method 'create' pada DecisionController menyiapkan data yang diperlukan untuk form, seperti daftar cluster yang tersedia dan opsi parameter keputusan, serta menampilkan antarmuka yang user-friendly untuk proses pengambilan keputusan.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::post('/decision', [DecisionController::class, 'store'])->name('decision.store')</h5>
                <p class="mb-3">
                    Route ini memproses penyimpanan keputusan distribusi bantuan baru. Method 'store' pada DecisionController memvalidasi input, menjalankan algoritma penentuan penerima bantuan berdasarkan hasil clustering dan parameter yang diberikan, kemudian menyimpan hasil keputusan ke database untuk referensi dan penggunaan di masa depan.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/decision/{id}', [DecisionController::class, 'show'])->name('decision.show')</h5>
                <p class="mb-3">
                    Route ini menampilkan detail dari satu keputusan distribusi bantuan tertentu. Method 'show' pada DecisionController mengambil data keputusan berdasarkan ID, termasuk daftar penerima bantuan yang terpilih, parameter yang digunakan, dan informasi lain yang relevan, kemudian menyajikannya dalam tampilan yang komprehensif.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::delete('/decision/{id}', [DecisionController::class, 'destroy'])->name('decision.destroy')</h5>
                <p class="mb-3">
                    Route ini menangani penghapusan keputusan distribusi bantuan. Method 'destroy' pada DecisionController menghapus data keputusan dan data terkait dari database, memastikan tidak ada data yang tertinggal atau menjadi orphaned. Penggunaan metode DELETE sesuai dengan konvensi RESTful untuk operasi penghapusan resource.
                </p>
            </div>
        </div>
        
        <div class="mb-6">
            <h4 class="text-lg font-semibold mb-2">Route Profil</h4>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')</h5>
                <p class="mb-3">
                    Route ini menampilkan halaman edit profil pengguna. Method 'edit' pada ProfileController mengambil data profil pengguna yang sedang login dan menampilkannya dalam form yang dapat diedit. Halaman ini biasanya mencakup informasi seperti nama, email, dan pengaturan preferensi pengguna.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update')</h5>
                <p class="mb-3">
                    Route ini memproses pembaruan data profil pengguna. Method 'update' pada ProfileController memvalidasi data input dan menyimpan perubahan ke database. Penggunaan metode PUT sesuai dengan standar RESTful untuk operasi pembaruan resource.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update')</h5>
                <p class="mb-3">
                    Route ini secara khusus menangani pembaruan password pengguna. Method 'updatePassword' pada ProfileController memvalidasi password lama dan baru, memastikan keamanan proses penggantian password, seperti kekuatan password dan konfirmasi password, kemudian menyimpan password baru dalam bentuk terenkripsi ke database.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update')</h5>
                <p class="mb-3">
                    Route ini menangani unggahan dan pembaruan avatar (gambar profil) pengguna. Method 'updateAvatar' pada ProfileController memproses file gambar yang diunggah, melakukan validasi (tipe file, ukuran), memformat ulang jika diperlukan, dan menyimpannya ke penyimpanan yang ditentukan. Penggunaan metode POST diperlukan karena route ini melibatkan unggahan file.
                </p>
            </div>
        </div>
        
        <h3 class="text-xl font-bold mb-2">4. Route Dokumentasi</h3>
        <div class="bg-gray-50 p-4 rounded-lg mb-3">
            <pre><code class="language-php">// Route untuk halaman dokumentasi (terisolasi)
Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/documentation/model', [DocumentationController::class, 'model'])->name('documentation.model');
Route::get('/documentation/view', [DocumentationController::class, 'view'])->name('documentation.view');
Route::get('/documentation/controller', [DocumentationController::class, 'controller'])->name('documentation.controller');
Route::get('/documentation/route', [DocumentationController::class, 'route'])->name('documentation.route');
Route::get('/documentation/middleware', [DocumentationController::class, 'middleware'])->name('documentation.middleware');
Route::get('/documentation/migration', [DocumentationController::class, 'migration'])->name('documentation.migration');</code></pre>
        </div>
        <p class="mb-4">
            Bagian ini mendefinisikan route untuk sistem dokumentasi internal aplikasi. Route-route ini sengaja tidak diletakkan dalam grup middleware 'auth' agar bisa diakses secara langsung tanpa login, memudahkan proses onboarding developer baru atau sebagai referensi cepat. Berikut penjelasan setiap route dokumentasi:
        </p>
        
        <div class="mb-6">
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index')</h5>
                <p class="mb-3">
                    Route ini menampilkan halaman utama dokumentasi yang berisi gambaran umum sistem, struktur aplikasi, dan navigasi ke bagian dokumentasi lainnya. Method 'index' pada DocumentationController menyajikan halaman beranda dokumentasi yang informatif dan mudah dinavigasi.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/documentation/model', [DocumentationController::class, 'model'])->name('documentation.model')</h5>
                <p class="mb-3">
                    Route ini menampilkan dokumentasi tentang model-model dalam aplikasi. Method 'model' pada DocumentationController menyajikan informasi tentang struktur database, relasi antar model, dan atribut-atribut penting yang digunakan dalam logika bisnis aplikasi.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/documentation/view', [DocumentationController::class, 'view'])->name('documentation.view')</h5>
                <p class="mb-3">
                    Route ini menampilkan dokumentasi tentang sistem view/template dalam aplikasi. Method 'view' pada DocumentationController menjelaskan tentang struktur folder view, layout utama, komponen yang dapat digunakan kembali, dan konvensi penamaan yang diterapkan.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/documentation/controller', [DocumentationController::class, 'controller'])->name('documentation.controller')</h5>
                <p class="mb-3">
                    Route ini menampilkan dokumentasi tentang controller-controller dalam aplikasi. Method 'controller' pada DocumentationController menyajikan informasi tentang peran dan fungsi setiap controller, method yang tersedia, dan alur kerja proses bisnis yang ditangani.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/documentation/route', [DocumentationController::class, 'route'])->name('documentation.route')</h5>
                <p class="mb-3">
                    Route ini menampilkan dokumentasi tentang sistem routing aplikasi (halaman ini). Method 'route' pada DocumentationController menyajikan penjelasan terstruktur tentang setiap route yang didefinisikan, termasuk middleware yang diterapkan, parameter yang diterima, dan controller/method yang dituju.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/documentation/middleware', [DocumentationController::class, 'middleware'])->name('documentation.middleware')</h5>
                <p class="mb-3">
                    Route ini menampilkan dokumentasi tentang middleware yang digunakan dalam aplikasi. Method 'middleware' pada DocumentationController menjelaskan tentang berbagai middleware yang mengatur akses, validasi, dan keamanan aplikasi.
                </p>
            </div>
            
            <div class="ml-6 mb-4">
                <h5 class="text-base font-semibold mb-2">Route::get('/documentation/migration', [DocumentationController::class, 'migration'])->name('documentation.migration')</h5>
                <p class="mb-3">
                    Route ini menampilkan dokumentasi tentang migrasi database dalam aplikasi. Method 'migration' pada DocumentationController menyajikan informasi tentang skema database, perubahan struktur yang telah dilakukan, dan panduan untuk menjalankan atau membuat migrasi baru.
                </p>
            </div>
        </div>
    </div>
@endsection 