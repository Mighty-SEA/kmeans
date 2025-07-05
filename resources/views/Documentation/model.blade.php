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

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
</code></pre>
            </div>
            <p>
                Model <b>User</b> merepresentasikan data pengguna yang dapat mengakses aplikasi, baik sebagai admin maupun user biasa. Model ini mewarisi <i>Authenticatable</i> dari Laravel sehingga mendukung fitur autentikasi secara otomatis. Properti <code>$fillable</code> mendefinisikan atribut yang dapat diisi secara massal, seperti nama, email, password, dan avatar. Sementara <code>$hidden</code> digunakan untuk menyembunyikan data sensitif seperti password dan token saat model dikonversi ke array atau JSON. Method <code>getAvatarUrlAttribute</code> adalah accessor yang menghasilkan URL avatar pengguna, baik dari file yang diunggah maupun dari layanan avatar eksternal jika belum ada avatar yang diunggah. Model ini menjadi pusat pengelolaan identitas dan autentikasi pengguna dalam aplikasi.
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
    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
        'usia',
        'jumlah_anak',
        'kelayakan_rumah',
        'pendapatan_perbulan',
        'nik',
    ];

    public function clusteringResult()
    {
        return $this->hasOne(ClusteringResult::class);
    }

    public function normalizationResult()
    {
        return $this->hasOne(NormalizationResult::class);
    }
}
</code></pre>
            </div>
            <p>
                Model <b>Beneficiary</b> merepresentasikan data penerima bantuan yang menjadi objek utama dalam proses seleksi dan penyaluran bantuan. Model ini menyimpan informasi penting seperti nama, alamat, nomor HP, usia, jumlah anak, kelayakan rumah, pendapatan per bulan, dan NIK. Properti <code>$fillable</code> memastikan hanya atribut yang diizinkan yang dapat diisi secara massal, sedangkan <code>$guarded</code> melindungi atribut <code>id</code> dari pengisian massal. Model ini memiliki relasi <code>hasOne</code> ke <b>ClusteringResult</b> dan <b>NormalizationResult</b>, yang berarti setiap penerima bantuan dapat memiliki satu hasil clustering dan satu hasil normalisasi. Dengan struktur ini, model Beneficiary menjadi inti dari seluruh proses analisis dan pengambilan keputusan dalam aplikasi.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">3. ClusteringResult Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class ClusteringResult extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'beneficiary_id',
        'cluster',
        'silhouette',
        'num_clusters',
        'max_iterations',
        'execution_time',
        'cluster_data',
        'centroids',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
</code></pre>
            </div>
            <p>
                Model <b>ClusteringResult</b> digunakan untuk menyimpan hasil pengelompokan (clustering) yang dilakukan dengan algoritma K-Means terhadap data penerima bantuan. Setiap record pada model ini berisi informasi seperti ID penerima bantuan, nomor cluster hasil pengelompokan, nilai silhouette (untuk evaluasi kualitas cluster), jumlah cluster, jumlah maksimum iterasi, waktu eksekusi, data cluster, dan posisi centroid. Model ini memiliki relasi <code>belongsTo</code> ke <b>Beneficiary</b>, sehingga setiap hasil clustering selalu terkait dengan satu penerima bantuan. Dengan adanya model ini, aplikasi dapat menyimpan dan menampilkan riwayat hasil clustering secara detail dan terstruktur.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">4. ClusteringSetting Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class ClusteringSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'num_clusters',
        'max_iterations',
        'is_default',
        'attributes',
        'normalization',
    ];
    
    protected $casts = [
        'is_default' => 'boolean',
        'attributes' => 'array',
    ];
    
    public static function getCurrentSettings()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Default Setting',
                'num_clusters' => 3,
                'max_iterations' => 100,
                'is_default' => true,
                'normalization' => 'robust',
                'attributes' => [
                    'income' => true,
                    'dependents' => true,
                    'house_status' => true,
                    'house_condition' => true,
                ]
            ]
        );
    }
}
</code></pre>
            </div>
            <p>
                Model <b>ClusteringSetting</b> berfungsi untuk menyimpan konfigurasi atau pengaturan yang digunakan dalam proses clustering K-Means. Pengaturan yang disimpan meliputi nama setting, jumlah cluster, jumlah maksimum iterasi, status default, atribut yang digunakan untuk clustering, dan metode normalisasi. Properti <code>$casts</code> digunakan untuk mengonversi atribut <code>is_default</code> menjadi boolean dan <code>attributes</code> menjadi array. Method <code>getCurrentSettings</code> digunakan untuk mendapatkan atau membuat pengaturan clustering default. Model ini sangat penting agar proses clustering dapat berjalan sesuai parameter yang diinginkan dan dapat diatur ulang sesuai kebutuhan analisis.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">5. DecisionResult Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class DecisionResult extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'cluster',
        'count',
        'notes',
        'user_id',
        'sort_by',
        'sort_direction',
        'limit',
        'result_data',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(DecisionResultItem::class);
    }

    public function beneficiaries()
    {
        return $this->hasManyThrough(
            Beneficiary::class,
            DecisionResultItem::class,
            'decision_result_id',
            'id',
            'id',
            'beneficiary_id'
        );
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
</code></pre>
            </div>
            <p>
                Model <b>DecisionResult</b> digunakan untuk menyimpan hasil keputusan yang diambil berdasarkan hasil clustering dan kriteria tertentu. Model ini menyimpan informasi seperti judul keputusan, deskripsi, cluster yang dipilih, jumlah penerima yang dipilih, catatan, user yang membuat keputusan, serta data hasil keputusan. Model ini memiliki relasi <code>hasMany</code> ke <b>DecisionResultItem</b> untuk menyimpan daftar penerima yang terpilih, <code>hasManyThrough</code> ke <b>Beneficiary</b> untuk mengakses data penerima melalui item keputusan, dan <code>belongsTo</code> ke <b>User</b> untuk mengetahui siapa yang membuat keputusan. Dengan struktur ini, model DecisionResult sangat penting untuk mendokumentasikan dan melacak proses pengambilan keputusan dalam aplikasi.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">6. DecisionResultItem Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class DecisionResultItem extends Model
{
    protected $fillable = [
        'decision_result_id',
        'beneficiary_id',
    ];

    public function decisionResult(): BelongsTo
    {
        return $this->belongsTo(DecisionResult::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
</code></pre>
            </div>
            <p>
                Model <b>DecisionResultItem</b> adalah model perantara yang menghubungkan antara hasil keputusan (<b>DecisionResult</b>) dengan data penerima bantuan (<b>Beneficiary</b>). Setiap record pada model ini berisi ID hasil keputusan dan ID penerima bantuan yang terpilih. Model ini memiliki relasi <code>belongsTo</code> ke <b>DecisionResult</b> dan <b>Beneficiary</b>, sehingga memudahkan pelacakan penerima bantuan yang terpilih dalam setiap keputusan. Dengan adanya model ini, aplikasi dapat merepresentasikan satu keputusan yang terdiri dari beberapa penerima bantuan secara terstruktur dan mudah untuk dianalisis.
            </p>
        </div>

        <div class="mb-8">
            <h3 class="text-xl font-bold mb-2">7. NormalizationResult Model</h3>
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <pre><code class="language-php">class NormalizationResult extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'beneficiary_id',
        'usia_normalized',
        'jumlah_anak_normalized',
        'kelayakan_rumah_normalized',
        'pendapatan_perbulan_normalized',
        'normalized_data',
        'min_values',
        'max_values',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
</code></pre>
            </div>
            <p>
                Model <b>NormalizationResult</b> digunakan untuk menyimpan hasil normalisasi data penerima bantuan sebelum dilakukan proses clustering. Data yang disimpan meliputi nilai-nilai yang telah dinormalisasi untuk atribut usia, jumlah anak, kelayakan rumah, dan pendapatan per bulan, serta data minimum dan maksimum yang digunakan dalam proses normalisasi. Model ini memiliki relasi <code>belongsTo</code> ke <b>Beneficiary</b>, sehingga setiap hasil normalisasi selalu terkait dengan satu penerima bantuan. Dengan adanya model ini, aplikasi dapat memastikan bahwa data yang digunakan untuk clustering sudah berada dalam skala yang seragam dan siap untuk dianalisis lebih lanjut.
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