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
        <h2 class="text-2xl font-bold mb-4">Penjelasan Setiap Syntax pada routes/web.php</h2>
        <pre class="bg-gray-50 p-4 rounded-lg mb-3"><code class="language-php">use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Beneficiary\BeneficiaryController;
use App\Http\Controllers\Statistic\StatisticController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Decision\DecisionController;
use App\Http\Controllers\Documentation\DocumentationController;
</code></pre>
        <ul class="mb-6">
            <li>Baris-baris di atas mengimpor class controller yang akan digunakan pada route. Dengan ini, Anda bisa memanggil method controller secara langsung pada definisi route.</li>
        </ul>
        <pre class="bg-gray-50 p-4 rounded-lg mb-3"><code class="language-php">// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
</code></pre>
        <ul class="mb-6">
            <li><code>Route::middleware(['guest'])->group(function () {...})</code>: Membungkus sekelompok route yang hanya bisa diakses oleh user yang belum login (guest).</li>
            <li><code>Route::get('/login', ...)</code>: Menampilkan halaman login, method GET, menggunakan method <code>showLoginForm</code> pada <code>AuthController</code>, dan diberi nama route <code>login</code>.</li>
            <li><code>Route::post('/login', ...)</code>: Memproses form login, method POST, menggunakan method <code>login</code> pada <code>AuthController</code>.</li>
            <li><code>Route::get('/register', ...)</code>: Menampilkan halaman registrasi, method GET, menggunakan method <code>showRegisterForm</code> pada <code>AuthController</code>, dan diberi nama route <code>register</code>.</li>
            <li><code>Route::post('/register', ...)</code>: Memproses form registrasi, method POST, menggunakan method <code>register</code> pada <code>AuthController</code>.</li>
            <li><code>Route::post('/logout', ...)</code>: Memproses logout user, method POST, menggunakan method <code>logout</code> pada <code>AuthController</code>, dan diberi nama route <code>logout</code>.</li>
        </ul>
        <pre class="bg-gray-50 p-4 rounded-lg mb-3"><code class="language-php">// Protected Routes
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
});
</code></pre>
        <ul class="mb-6">
            <li><code>Route::middleware(['auth'])->group(function () {...})</code>: Membungkus sekelompok route yang hanya bisa diakses oleh user yang sudah login (authenticated).</li>
            <li><code>Route::get('/', ...)</code>: Menampilkan dashboard utama, method GET, menggunakan method <code>dashboard</code> pada <code>BeneficiaryController</code>.</li>
            <li><code>Route::resource('beneficiary', ...)</code>: Membuat semua route CRUD (index, create, store, show, edit, update, destroy) untuk resource <code>beneficiary</code> dengan controller <code>BeneficiaryController</code>.</li>
            <li><code>Route::get('/statistic', ...)</code>: Menampilkan halaman statistik, method GET, method <code>index</code> pada <code>StatisticController</code>, nama route <code>statistic.index</code>.</li>
            <li><code>Route::get('/statistic/cluster/{cluster}', ...)</code>: Menampilkan detail cluster tertentu, method GET, method <code>showCluster</code> pada <code>StatisticController</code>, parameter <code>{cluster}</code> dinamis, nama route <code>statistic.cluster</code>.</li>
            <li><code>Route::post('/statistic/recalculate', ...)</code>: Memproses perhitungan ulang clustering, method POST, method <code>recalculate</code> pada <code>StatisticController</code>, nama route <code>statistic.recalculate</code>.</li>
            <li><code>Route::post('/statistic/clustering', ...)</code>: Memproses clustering, method POST, method <code>doClustering</code> pada <code>StatisticController</code>, nama route <code>statistic.clustering</code>.</li>
            <li><code>Route::post('beneficiary-export', ...)</code>: Mengekspor data beneficiary ke Excel, method POST, method <code>exportExcel</code> pada <code>BeneficiaryController</code>, nama route <code>beneficiary.export</code>.</li>
            <li><code>Route::post('beneficiary-import', ...)</code>: Mengimpor data beneficiary dari Excel, method POST, method <code>importExcel</code> pada <code>BeneficiaryController</code>, nama route <code>beneficiary.import</code>.</li>
            <li><code>Route::delete('beneficiary-bulk-delete', ...)</code>: Menghapus banyak data beneficiary sekaligus, method DELETE, method <code>bulkDelete</code> pada <code>BeneficiaryController</code>, nama route <code>beneficiary.bulkDelete</code>.</li>
            <li><code>Route::get('/decision', ...)</code>: Menampilkan daftar keputusan distribusi bantuan, method GET, method <code>index</code> pada <code>DecisionController</code>, nama route <code>decision.index</code>.</li>
            <li><code>Route::get('/decision/create', ...)</code>: Menampilkan form pembuatan keputusan, method GET, method <code>create</code> pada <code>DecisionController</code>, nama route <code>decision.create</code>.</li>
            <li><code>Route::post('/decision', ...)</code>: Menyimpan keputusan baru, method POST, method <code>store</code> pada <code>DecisionController</code>, nama route <code>decision.store</code>.</li>
            <li><code>Route::get('/decision/{id}', ...)</code>: Menampilkan detail keputusan, method GET, method <code>show</code> pada <code>DecisionController</code>, parameter <code>{id}</code> dinamis, nama route <code>decision.show</code>.</li>
            <li><code>Route::delete('/decision/{id}', ...)</code>: Menghapus keputusan, method DELETE, method <code>destroy</code> pada <code>DecisionController</code>, parameter <code>{id}</code> dinamis, nama route <code>decision.destroy</code>.</li>
            <li><code>Route::get('/profile', ...)</code>: Menampilkan halaman edit profil, method GET, method <code>edit</code> pada <code>ProfileController</code>, nama route <code>profile.edit</code>.</li>
            <li><code>Route::put('/profile', ...)</code>: Memperbarui data profil, method PUT, method <code>update</code> pada <code>ProfileController</code>, nama route <code>profile.update</code>.</li>
            <li><code>Route::put('/profile/password', ...)</code>: Memperbarui password, method PUT, method <code>updatePassword</code> pada <code>ProfileController</code>, nama route <code>profile.password.update</code>.</li>
            <li><code>Route::post('/profile/avatar', ...)</code>: Mengunggah avatar baru, method POST, method <code>updateAvatar</code> pada <code>ProfileController</code>, nama route <code>profile.avatar.update</code>.</li>
        </ul>
        <pre class="bg-gray-50 p-4 rounded-lg mb-3"><code class="language-php">// Route untuk halaman dokumentasi (terisolasi)
Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/documentation/model', [DocumentationController::class, 'model'])->name('documentation.model');
Route::get('/documentation/view', [DocumentationController::class, 'view'])->name('documentation.view');
Route::get('/documentation/controller', [DocumentationController::class, 'controller'])->name('documentation.controller');
Route::get('/documentation/route', [DocumentationController::class, 'route'])->name('documentation.route');
Route::get('/documentation/middleware', [DocumentationController::class, 'middleware'])->name('documentation.middleware');
Route::get('/documentation/migration', [DocumentationController::class, 'migration'])->name('documentation.migration');
</code></pre>
        <ul class="mb-6">
            <li>Setiap baris di atas adalah route GET yang menampilkan halaman dokumentasi tertentu, menggunakan method pada <code>DocumentationController</code> yang sesuai, dan diberi nama route yang spesifik untuk setiap dokumentasi (index, model, view, controller, route, middleware, migration).</li>
        </ul>
    </div>
@endsection 