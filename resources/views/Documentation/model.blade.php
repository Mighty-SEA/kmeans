@extends('Documentation.layout')

@section('title', 'Model - Dokumentasi')
@section('header', 'Model')
@section('breadcrumb')
    <nav class="mb-4 text-sm text-blue-700 font-medium flex items-center space-x-2">
        <a href="{{ route('documentation.index') }}" class="hover:underline">Dokumentasi</a>
        <span>/</span>
        <span class="text-blue-900">Model</span>
    </nav>
@endsection
@section('content')
    <div class="prose max-w-none">
        <h2 class="text-2xl font-bold mb-4">Penjelasan Model dalam Aplikasi K-Means Clustering</h2>
        
        <p class="mb-4">
            Model dalam Laravel merupakan representasi dari tabel pada basis data dan berfungsi sebagai penghubung antara logika aplikasi dengan data yang tersimpan. Pada aplikasi K-Means Clustering ini, model digunakan untuk mengelola data penerima bantuan, hasil clustering, pengaturan algoritma, serta proses pengambilan keputusan. Berikut adalah penjelasan setiap model yang digunakan beserta fungsinya dalam aplikasi.
        </p>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">1. User Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = ['name', 'email', 'password', 'avatar'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array { ... }
    public function getAvatarUrlAttribute(): string { ... }
}</code></pre>
            </div>
            <p>
                Model <b>User</b> berfungsi untuk merepresentasikan data pengguna aplikasi, baik sebagai admin maupun user biasa. Model ini mewarisi <i>Authenticatable</i> yang memungkinkan proses autentikasi pengguna secara otomatis oleh Laravel. Atribut <code>$fillable</code> mendefinisikan data yang dapat diisi secara massal, seperti nama, email, password, dan avatar. Sementara itu, <code>$hidden</code> digunakan untuk menyembunyikan data sensitif seperti password saat model dikonversi ke array atau JSON. Fungsi <code>getAvatarUrlAttribute</code> merupakan accessor yang digunakan untuk mengambil URL avatar pengguna, baik dari penyimpanan lokal maupun layanan avatar eksternal. Dengan demikian, model ini menjadi inti dari sistem autentikasi dan manajemen identitas pengguna dalam aplikasi.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">2. Beneficiary Model (Penerima Bantuan)</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class Beneficiary extends Model
{
    use HasFactory;
    protected $table = 'beneficiaries';
    protected $guarded = ['id'];
    protected $fillable = [...];
    public function clusteringResult() { ... }
    public function normalizationResult() { ... }
}</code></pre>
            </div>
            <p>
                Model <b>Beneficiary</b> digunakan untuk merepresentasikan data penerima bantuan dalam aplikasi. Model ini berfungsi sebagai pusat data utama yang akan diproses lebih lanjut, baik untuk proses normalisasi maupun clustering. Atribut-atribut seperti nama, alamat, usia, jumlah anak, kelayakan rumah, pendapatan per bulan, dan NIK didefinisikan dalam <code>$fillable</code> agar dapat diisi secara massal. Model ini juga memiliki relasi <i>one-to-one</i> dengan <b>ClusteringResult</b> dan <b>NormalizationResult</b>, yang berarti setiap penerima bantuan akan memiliki satu hasil clustering dan satu hasil normalisasi. Dengan demikian, model ini sangat penting dalam mendukung proses seleksi dan analisis data penerima bantuan secara otomatis dan objektif.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">3. ClusteringResult Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class ClusteringResult extends Model
{
    use HasFactory;
    protected $fillable = [...];
    public function beneficiary() { ... }
}</code></pre>
            </div>
            <p>
                Model <b>ClusteringResult</b> berfungsi untuk menyimpan hasil dari proses clustering K-Means yang dilakukan terhadap data penerima bantuan. Setiap entri pada model ini berisi informasi seperti ID penerima bantuan, nomor cluster yang didapat, nilai silhouette (untuk mengukur kualitas clustering), jumlah cluster, jumlah iterasi, waktu eksekusi, data cluster, dan posisi centroid. Model ini memiliki relasi <i>belongsTo</i> ke <b>Beneficiary</b>, menandakan bahwa setiap hasil clustering terkait dengan satu penerima bantuan. Dengan adanya model ini, aplikasi dapat merekam dan menampilkan hasil pengelompokan secara historis dan terstruktur.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">4. ClusteringSetting Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class ClusteringSetting extends Model
{
    use HasFactory;
    protected $fillable = [...];
    protected $casts = [...];
    public static function getCurrentSettings() { ... }
}</code></pre>
            </div>
            <p>
                Model <b>ClusteringSetting</b> digunakan untuk menyimpan dan mengelola pengaturan algoritma K-Means yang digunakan dalam aplikasi. Pengaturan ini meliputi nama konfigurasi, jumlah cluster, jumlah maksimum iterasi, status default, atribut yang digunakan, serta metode normalisasi. Dengan adanya properti <code>$casts</code>, beberapa atribut seperti <code>is_default</code> dan <code>attributes</code> akan otomatis dikonversi ke tipe data yang sesuai (boolean dan array). Fungsi <code>getCurrentSettings</code> memungkinkan aplikasi untuk selalu mendapatkan pengaturan clustering yang aktif atau membuat pengaturan default jika belum ada. Model ini sangat penting untuk memastikan proses clustering berjalan sesuai parameter yang diinginkan dan dapat diubah sesuai kebutuhan penelitian.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">5. DecisionResult Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class DecisionResult extends Model
{
    use HasFactory;
    protected $fillable = [...];
    public function items() { ... }
    public function beneficiaries() { ... }
    public function user() { ... }
}</code></pre>
            </div>
            <p>
                Model <b>DecisionResult</b> berfungsi untuk menyimpan hasil keputusan yang diambil berdasarkan hasil clustering dan kriteria tertentu. Model ini menyimpan informasi seperti judul keputusan, deskripsi, cluster yang dipilih, jumlah data, catatan, user yang membuat keputusan, serta data hasil keputusan. Model ini memiliki relasi <i>hasMany</i> ke <b>DecisionResultItem</b> dan <i>hasManyThrough</i> ke <b>Beneficiary</b> melalui DecisionResultItem, sehingga dapat menghubungkan hasil keputusan dengan data penerima bantuan yang terpilih. Selain itu, model ini juga memiliki relasi <i>belongsTo</i> ke <b>User</b> yang menandakan siapa pengambil keputusan. Dengan demikian, model ini sangat penting dalam mendokumentasikan dan melacak proses pengambilan keputusan dalam sistem.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">6. DecisionResultItem Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class DecisionResultItem extends Model
{
    protected $fillable = [...];
    public function decisionResult() { ... }
    public function beneficiary() { ... }
}</code></pre>
            </div>
            <p>
                Model <b>DecisionResultItem</b> merupakan model perantara yang menghubungkan antara hasil keputusan (<b>DecisionResult</b>) dengan data penerima bantuan (<b>Beneficiary</b>). Setiap item pada model ini berisi ID hasil keputusan dan ID penerima bantuan yang terpilih. Dengan adanya model ini, aplikasi dapat merepresentasikan satu keputusan yang terdiri dari beberapa penerima bantuan secara terstruktur dan mudah untuk ditelusuri. Model ini juga memudahkan proses pelacakan dan analisis hasil keputusan yang telah diambil.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">7. NormalizationResult Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class NormalizationResult extends Model
{
    use HasFactory;
    protected $fillable = [...];
    public function beneficiary() { ... }
}</code></pre>
            </div>
            <p>
                Model <b>NormalizationResult</b> digunakan untuk menyimpan hasil normalisasi data penerima bantuan sebelum dilakukan proses clustering. Data yang dinormalisasi meliputi usia, jumlah anak, kelayakan rumah, pendapatan per bulan, serta data minimum dan maksimum yang digunakan dalam proses normalisasi. Model ini memiliki relasi <i>belongsTo</i> ke <b>Beneficiary</b>, sehingga setiap hasil normalisasi terkait dengan satu penerima bantuan. Dengan adanya model ini, aplikasi dapat memastikan bahwa data yang digunakan untuk clustering sudah berada dalam skala yang seragam dan siap untuk dianalisis lebih lanjut.
            </p>
        </div>

        <h2 class="text-2xl font-bold mb-4">Konsep-konsep Penting dalam Model Laravel</h2>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">1. Eloquent ORM</h3>
            <p>
                Laravel menggunakan Eloquent sebagai Object-Relational Mapper (ORM) yang menyediakan implementasi ActiveRecord yang elegan untuk bekerja dengan basis data. Setiap model Eloquent merepresentasikan satu tabel pada basis data dan digunakan untuk berinteraksi dengan data pada tabel tersebut, baik untuk proses pengambilan, penyimpanan, maupun manipulasi data.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">2. Relasi Antar Model</h3>
            <p>
                Laravel menyediakan berbagai jenis relasi antar model, seperti <i>hasOne</i>, <i>hasMany</i>, <i>belongsTo</i>, dan <i>hasManyThrough</i>. Relasi ini memudahkan pengembang untuk menavigasi dan mengelola data yang saling terkait, misalnya satu penerima bantuan memiliki satu hasil clustering, atau satu hasil keputusan dapat memiliki banyak penerima bantuan melalui model perantara.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">3. Mutator dan Accessor</h3>
            <p>
                Mutator dan accessor pada Laravel memungkinkan pengembang untuk memodifikasi data sebelum disimpan ke basis data (mutator) atau setelah diambil dari basis data (accessor). Contohnya, pada model User terdapat accessor <code>getAvatarUrlAttribute</code> yang digunakan untuk mengatur format URL avatar pengguna secara otomatis.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">4. Mass Assignment Protection</h3>
            <p>
                Perlindungan terhadap <i>mass assignment</i> dilakukan dengan mendefinisikan atribut <code>$fillable</code> dan <code>$guarded</code> pada model. Hal ini bertujuan untuk mencegah pengisian data secara massal pada atribut yang tidak diinginkan, sehingga meningkatkan keamanan aplikasi.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">5. Type Casting</h3>
            <p>
                Laravel memungkinkan pengembang untuk menentukan tipe data atribut tertentu melalui properti <code>$casts</code>. Dengan demikian, data yang diambil dari basis data akan otomatis dikonversi ke tipe data yang sesuai, seperti boolean atau array, sehingga memudahkan proses manipulasi data di dalam aplikasi.
            </p>
        </div>

        <h2 class="text-2xl font-bold mb-4">Kesimpulan</h2>
        <p>
            Model dalam aplikasi K-Means Clustering ini dirancang untuk mendukung seluruh proses dalam sistem, mulai dari pengelolaan data penerima bantuan, proses normalisasi, clustering, hingga pengambilan keputusan. Setiap model memiliki peran dan fungsi yang saling melengkapi, serta didukung oleh fitur-fitur Eloquent ORM yang memudahkan pengelolaan data secara efisien dan terstruktur. Dengan dokumentasi ini, diharapkan pembaca skripsi dapat memahami peran penting model dalam arsitektur aplikasi dan bagaimana model-model tersebut berkontribusi terhadap otomatisasi dan objektivitas proses seleksi penerima bantuan.
        </p>
    </div>
@endsection 