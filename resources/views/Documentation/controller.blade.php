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
            Controller dalam Laravel berfungsi sebagai perantara antara Model dan View, menangani permintaan HTTP, memproses data, dan mengembalikan respons yang sesuai. Pada aplikasi K-Means Clustering ini, controller berperan penting dalam mengelola alur kerja aplikasi, mulai dari autentikasi pengguna, pengelolaan data penerima bantuan (beneficiaries), proses clustering, hingga pengambilan keputusan. Berikut adalah penjelasan detail setiap controller beserta fungsi-fungsinya.
        </p>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">1. AuthController</h3>
            <p class="mb-4">
                <b>AuthController</b> merupakan komponen fundamental dalam sistem aplikasi K-Means Clustering yang bertanggung jawab atas seluruh proses autentikasi pengguna. Keberadaan controller ini sangat penting untuk menjaga keamanan data dan memastikan hanya pengguna yang berhak dapat mengakses fitur-fitur aplikasi. Dalam konteks pengembangan perangkat lunak berbasis web, autentikasi menjadi lapisan pertama dalam sistem keamanan, sehingga implementasi yang baik pada AuthController akan berdampak langsung pada integritas dan keandalan aplikasi secara keseluruhan. AuthController tidak hanya mengelola proses login dan logout, tetapi juga pendaftaran pengguna baru, serta pengelolaan sesi pengguna. Dengan demikian, controller ini menjadi gerbang utama interaksi antara pengguna dan sistem.
            </p>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">showLoginForm()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function showLoginForm()
{
    return view('auth.login');
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>showLoginForm()</b> adalah fungsi yang bertugas untuk menampilkan halaman login kepada pengguna. Secara konseptual, fungsi ini menjadi titik awal proses autentikasi, di mana pengguna diberikan antarmuka untuk memasukkan kredensial berupa email dan password. Dalam konteks keamanan aplikasi, penyajian form login yang terpisah dan terstruktur sangat penting untuk meminimalisir risiko serangan seperti phishing atau pencurian data. Dengan memanfaatkan view 'auth.login', fungsi ini memastikan bahwa pengguna diarahkan ke halaman yang tepat sebelum dapat mengakses fitur-fitur lain dalam aplikasi. Penempatan fungsi ini pada controller juga memudahkan pengelolaan logika tampilan dan pemisahan antara proses bisnis dan presentasi.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">login(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'Email atau password yang dimasukkan tidak sesuai.',
    ])->onlyInput('email');</code></pre>
                </div>
                <p class="mb-4">
                    <b>login(Request $request)</b> merupakan inti dari proses autentikasi pengguna. Fungsi ini tidak hanya memvalidasi input yang diberikan oleh pengguna, tetapi juga melakukan proses verifikasi terhadap data yang tersimpan di database. Validasi input sangat penting untuk memastikan bahwa data yang masuk telah sesuai dengan format yang diharapkan, sehingga dapat mencegah terjadinya error atau celah keamanan. Setelah validasi, fungsi ini menggunakan <i>Auth::attempt()</i> untuk mencocokkan kredensial pengguna dengan data yang ada. Jika autentikasi berhasil, sesi pengguna akan diregenerasi untuk mencegah serangan session fixation, dan pengguna diarahkan ke halaman utama atau halaman yang sebelumnya ingin diakses. Jika gagal, pengguna akan menerima pesan error yang informatif. Proses ini sangat krusial dalam menjaga keamanan dan kenyamanan pengguna dalam menggunakan aplikasi.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">showRegisterForm()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function showRegisterForm()
{
    return view('auth.register');
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>showRegisterForm()</b> berfungsi untuk menampilkan halaman pendaftaran pengguna baru. Dalam pengembangan aplikasi berbasis web, proses registrasi merupakan pintu masuk bagi pengguna baru untuk dapat memanfaatkan seluruh fitur aplikasi. Fungsi ini secara khusus memisahkan logika tampilan form registrasi dari proses bisnis, sehingga memudahkan pengelolaan dan pemeliharaan kode. Dengan menampilkan view 'auth.register', aplikasi memberikan pengalaman pengguna yang terstruktur dan mudah dipahami, serta memastikan bahwa proses pendaftaran berjalan secara terpisah dari proses lain seperti login atau pengelolaan data.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">register(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    Auth::login($user);

    return redirect('/');
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>register(Request $request)</b> adalah fungsi yang menangani proses pendaftaran pengguna baru secara menyeluruh. Fungsi ini melakukan validasi data yang dimasukkan oleh pengguna, seperti nama, email, dan password, untuk memastikan bahwa data yang diterima telah sesuai dengan standar keamanan dan integritas data. Validasi email unik sangat penting untuk mencegah duplikasi akun, sedangkan validasi password memastikan keamanan akun pengguna. Setelah data valid, fungsi ini membuat akun baru di database dengan password yang sudah di-hash, sehingga password tidak disimpan dalam bentuk teks asli. Setelah akun berhasil dibuat, pengguna langsung diautentikasi dan diarahkan ke halaman utama. Proses ini mendukung pengalaman pengguna yang seamless dan aman, serta memperkuat sistem keamanan aplikasi.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">logout(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>logout(Request $request)</b> merupakan fungsi yang bertanggung jawab untuk mengakhiri sesi autentikasi pengguna. Proses logout sangat penting untuk menjaga keamanan akun, terutama pada aplikasi yang digunakan secara bersama-sama atau di perangkat publik. Fungsi ini pertama-tama memanggil <i>Auth::logout()</i> untuk menghapus status autentikasi pengguna. Selanjutnya, sesi pengguna diinvalidasi dan token CSRF diregenerasi untuk mencegah penyalahgunaan sesi lama. Setelah proses logout selesai, pengguna diarahkan kembali ke halaman login. Dengan demikian, fungsi ini memastikan bahwa tidak ada sesi yang tertinggal dan seluruh proses logout berjalan dengan aman dan efisien.
                </p>
            </div>
        </div>
        
        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">2. BeneficiaryController</h3>
            <p class="mb-4">
                <b>BeneficiaryController</b> merupakan controller yang berperan sentral dalam pengelolaan data penerima bantuan pada aplikasi K-Means Clustering. Controller ini bertanggung jawab untuk mengatur seluruh siklus hidup data penerima, mulai dari penambahan, pengeditan, penghapusan, hingga ekspor dan impor data. Dengan adanya controller ini, proses manajemen data penerima bantuan menjadi lebih terstruktur, efisien, dan terjamin integritasnya. Setiap fungsi di dalamnya dirancang untuk mendukung kebutuhan operasional aplikasi, sekaligus memastikan bahwa data yang dikelola selalu valid dan siap digunakan dalam proses analisis lebih lanjut, seperti clustering dan pengambilan keputusan. Berikut penjelasan detail setiap fungsi utama dalam BeneficiaryController:
            </p>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">dashboard()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function dashboard()
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>dashboard()</b> adalah fungsi yang bertugas untuk menyiapkan dan menyajikan data ringkasan terkait penerima bantuan pada halaman utama dashboard aplikasi. Fungsi ini tidak hanya menampilkan jumlah total penerima, tetapi juga menampilkan data penerima terbaru, distribusi anggota per cluster, serta rata-rata fitur penting seperti usia, jumlah anak, kelayakan rumah, dan pendapatan per cluster. Dengan demikian, fungsi ini memberikan gambaran menyeluruh kepada pengguna mengenai kondisi data penerima bantuan secara real-time. Penyajian data yang komprehensif ini sangat penting untuk mendukung proses monitoring, evaluasi, dan pengambilan keputusan berbasis data dalam aplikasi.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">index(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function index(Request $request)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>index(Request $request)</b> berfungsi untuk menampilkan daftar lengkap penerima bantuan dengan fitur pencarian dan paginasi. Fungsi ini sangat penting dalam konteks pengelolaan data skala besar, di mana pengguna dapat dengan mudah mencari data penerima berdasarkan nama, NIK, alamat, atau nomor HP. Dengan adanya fitur paginasi, tampilan data menjadi lebih terorganisir dan mudah diakses, meskipun jumlah data sangat banyak. Fungsi ini juga memastikan bahwa setiap permintaan pencarian atau perubahan jumlah data per halaman diproses secara efisien, sehingga pengalaman pengguna tetap optimal.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">create()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function create()
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>create()</b> adalah fungsi yang bertugas untuk menampilkan formulir penambahan data penerima bantuan baru. Fungsi ini memisahkan logika tampilan dari proses bisnis, sehingga memudahkan pengelolaan dan pemeliharaan kode. Dengan menyediakan form input yang terstruktur, aplikasi memastikan bahwa proses penambahan data baru dapat dilakukan dengan mudah dan terstandarisasi, serta meminimalisir kesalahan input dari pengguna.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">store(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function store(Request $request)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>store(Request $request)</b> merupakan fungsi yang menangani proses penyimpanan data penerima bantuan baru ke dalam database. Fungsi ini melakukan validasi ketat terhadap setiap input yang diberikan, seperti NIK, nama, alamat, nomor HP, usia, jumlah anak, kelayakan rumah, dan pendapatan. Validasi ini sangat penting untuk menjaga integritas dan kualitas data yang masuk ke sistem. Setelah data dinyatakan valid, fungsi ini akan menyimpan data ke database dan memberikan umpan balik kepada pengguna berupa pesan sukses. Proses ini memastikan bahwa hanya data yang benar-benar valid yang dapat masuk ke dalam sistem, sehingga mendukung analisis data yang akurat di tahap selanjutnya.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">edit($id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function edit($id)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>edit($id)</b> adalah fungsi yang digunakan untuk menampilkan formulir pengeditan data penerima bantuan yang sudah ada. Fungsi ini mengambil data penerima berdasarkan ID yang diberikan, kemudian menampilkannya dalam form yang dapat diedit oleh pengguna. Dengan adanya fitur ini, aplikasi memberikan fleksibilitas kepada pengguna untuk memperbaiki atau memperbarui data yang mungkin mengalami perubahan atau kesalahan input sebelumnya. Proses ini sangat penting untuk menjaga akurasi dan relevansi data dalam sistem.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">update(Request $request, $id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function update(Request $request, $id)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>update(Request $request, $id)</b> berfungsi untuk memproses pembaruan data penerima bantuan yang sudah ada di database. Fungsi ini melakukan validasi ulang terhadap data yang diinputkan, memastikan bahwa setiap perubahan yang dilakukan tetap memenuhi standar kualitas data. Setelah data valid, fungsi ini akan memperbarui data penerima di database dan memberikan umpan balik kepada pengguna. Proses update ini sangat penting untuk menjaga konsistensi dan keakuratan data, terutama dalam aplikasi yang datanya sering mengalami perubahan.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">destroy($id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function destroy($id)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>destroy($id)</b> adalah fungsi yang bertanggung jawab untuk menghapus data penerima bantuan beserta data terkait lainnya dari sistem. Fungsi ini tidak hanya menghapus data utama penerima, tetapi juga memastikan bahwa data normalisasi dan clustering yang terkait juga dihapus, sehingga tidak ada data yang tertinggal atau menjadi orphaned. Proses penghapusan yang menyeluruh ini sangat penting untuk menjaga kebersihan dan integritas database, serta mencegah terjadinya inkonsistensi data di masa mendatang.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">exportExcel(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function exportExcel(Request $request)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>exportExcel(Request $request)</b> merupakan fungsi yang memungkinkan pengguna untuk mengekspor data penerima bantuan ke dalam format Excel. Fitur ini sangat bermanfaat untuk keperluan analisis data lebih lanjut di luar aplikasi, seperti pembuatan laporan atau visualisasi data menggunakan perangkat lunak lain. Dengan menyediakan opsi ekspor, aplikasi mendukung kebutuhan pengguna dalam mengelola dan memanfaatkan data secara lebih fleksibel dan profesional.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">importExcel(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function importExcel(Request $request)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>importExcel(Request $request)</b> adalah fungsi yang memudahkan pengguna untuk menambahkan banyak data penerima bantuan sekaligus melalui file Excel. Fungsi ini melakukan validasi terhadap file yang diunggah, memastikan format dan isi file sesuai dengan standar yang ditetapkan. Dengan fitur impor ini, proses input data menjadi jauh lebih efisien, terutama ketika harus menangani data dalam jumlah besar. Hal ini sangat mendukung efisiensi operasional dan mempercepat proses digitalisasi data penerima bantuan.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">bulkDelete(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function bulkDelete(Request $request)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>bulkDelete(Request $request)</b> adalah fungsi yang dirancang untuk menghapus banyak data penerima bantuan sekaligus, baik seluruh data maupun data terpilih. Fitur ini sangat berguna dalam manajemen data massal, misalnya ketika perlu melakukan reset data atau membersihkan data yang tidak lagi relevan. Dengan adanya fungsi ini, proses penghapusan data menjadi lebih cepat, efisien, dan terkontrol, sehingga mendukung pengelolaan database yang sehat dan terstruktur.
                </p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">3. StatisticController</h3>
            <p class="mb-4">
                Controller StatisticController merupakan inti dari aplikasi K-Means Clustering ini, menangani seluruh proses terkait analisis statistik dan algoritma clustering.
            </p>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">index()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function index()
{
    $data = Beneficiary::all(['id', 'nama', 'nik', 'alamat', 'usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan_perbulan']);
    if ($data->count() < 3) {
        return view('statistics.statistics', [
            'clusters' => [],
            'message' => 'Data kurang dari 3, tidak bisa melakukan clustering.'
        ]);
    }
    
    // Ambil hasil cluster dari tabel clustering_results jika sudah ada
    $clustering = ClusteringResult::all();
    $clustered = $clustering->count() === $data->count();
    
    // Ambil pengaturan clustering terakhir
    $lastNumClusters = session('last_num_clusters', 3);
    $lastNormalization = session('last_normalization', 'robust');
    
    if ($clustered) {
        // Logika untuk menampilkan hasil clustering yang sudah ada
        // ...
    }
    
    // Tampilkan halaman statistik
    return view('statistics.statistics', [
        'data' => $data,
        'clustered' => $clustered,
        'clusters' => $result ?? [],
        'scatterData' => $scatterData ?? [],
        'clusterCounts' => $clusterCounts ?? [],
        'clusterMeans' => $clusterMeans ?? [],
        'clusterStats' => $clusterStats ?? [],
        'normalizedClusterMeans' => $normalizedClusterMeans ?? [],
        'lastNumClusters' => $lastNumClusters,
        'lastNormalization' => $lastNormalization
    ]);
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan halaman statistik utama dengan visualisasi hasil clustering jika data sudah diproses, atau formulir untuk memulai clustering jika belum. Pertama, fungsi memeriksa apakah jumlah data memadai untuk clustering (minimal 3). Kemudian, fungsi memeriksa apakah sudah ada hasil clustering sebelumnya dengan membandingkan jumlah hasil clustering dengan jumlah data. Jika sudah ada, fungsi akan memuat data tersebut dan menyiapkan visualisasi seperti scatter plot, pie chart untuk distribusi anggota per cluster, dan statistik deskriptif untuk setiap cluster. Fungsi juga memuat pengaturan clustering terakhir dari session untuk digunakan sebagai nilai default pada form.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">recalculate(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function recalculate(Request $request)
{
    $data = Beneficiary::all(['id']);
    if ($data->count() < 3) {
        return redirect()->route('statistic.index')->with('error', 'Data kurang dari 3, tidak bisa melakukan clustering.');
    }
    
    // Validasi input
    $validated = $request->validate([
        'num_clusters' => 'required|integer|min:2|max:10',
        'normalization' => 'required|in:none,minmax,standard,robust',
    ]);
    
    $numClusters = $validated['num_clusters'];
    $normalization = $validated['normalization'];
    
    // Simpan pengaturan terakhir ke session
    session(['last_num_clusters' => $numClusters]);
    session(['last_normalization' => $normalization]);
    
    // Hapus semua hasil clustering lama
    ClusteringResult::truncate();
    // Hapus semua hasil normalisasi lama
    NormalizationResult::truncate();
    
    // Ubah pesan sukses untuk memberikan informasi parameter yang digunakan
    $normalizationName = [
        'none' => 'Tanpa Normalisasi',
        'minmax' => 'Min-Max',
        'standard' => 'Standard (Z-Score)',
        'robust' => 'Robust'
    ][$normalization] ?? 'Robust';
    
    // Panggil doClustering dengan parameter yang sama
    return $this->doClustering($request, "Clustering berhasil dihitung ulang dengan $numClusters cluster dan normalisasi $normalizationName.");
}</code></pre>
                </div>
                <p>
                    Fungsi ini memungkinkan pengguna untuk menjalankan ulang proses clustering dengan parameter berbeda. Fungsi ini memvalidasi input yang dikirim, memastikan jumlah cluster valid (antara 2-10) dan metode normalisasi sesuai dengan pilihan yang tersedia. Kemudian, fungsi menyimpan pengaturan terakhir ke session, menghapus semua hasil clustering dan normalisasi sebelumnya, dan memanggil fungsi doClustering() untuk melakukan proses clustering dengan parameter yang baru. Fungsi juga menyiapkan pesan sukses yang informatif tentang parameter yang digunakan.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">showCluster($cluster, Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function showCluster($cluster, Request $request)
{
    $clusterIndex = (int) $cluster - 1;
    
    // Periksa apakah cluster valid
    $maxCluster = ClusteringResult::max('cluster');
    if ($maxCluster === null || $clusterIndex < 0 || $clusterIndex > $maxCluster) {
        return redirect()->route('statistic.index')->with('error', 'Cluster tidak valid.');
    }
    
    $search = $request->input('search');
    
    $query = Beneficiary::join('clustering_results', 'beneficiaries.id', '=', 'clustering_results.beneficiary_id')
        ->where('clustering_results.cluster', $clusterIndex);
        
    // Tambahkan pencarian jika ada
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('alamat', 'like', "%{$search}%");
        });
    }
    
    $data = $query->paginate(10)->withQueryString();
        
    if ($data->isEmpty() && !$search) {
        return redirect()->route('statistic.index')->with('error', 'Tidak ada data dalam cluster ini.');
    }
    
    // Ambil semua data untuk statistik dan hitung statistik deskriptif
    // ...
    
    return view('statistics.cluster_detail', [
        'clusterIndex' => $clusterIndex,
        'cluster' => $data,
        'normalizedData' => $normalizedData,
        'clusterStats' => $clusterStats,
        'silhouetteStats' => $silhouetteStats,
        'total' => $allClusterData->count(),
        'search' => $search
    ]);
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan detail lengkap untuk cluster tertentu, termasuk anggota cluster dan statistik deskriptif. Fungsi pertama memeriksa apakah cluster yang diminta valid, lalu mengambil data penerima bantuan yang termasuk dalam cluster tersebut dengan fitur pencarian dan pagination. Fungsi juga menghitung statistik deskriptif untuk cluster seperti nilai minimum, maksimum, rata-rata, median, dan standar deviasi untuk setiap fitur. Jika ada, fungsi juga menampilkan statistik skor silhouette untuk mengevaluasi kualitas clustering. Informasi ini ditampilkan dalam view 'statistics.cluster_detail'.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">doClustering(Request $request, $successMessage = null)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function doClustering(Request $request, $successMessage = null)
{
    // Validasi input
    $validated = $request->validate([
        'num_clusters' => 'required|integer|min:2|max:10',
        'normalization' => 'required|in:none,minmax,standard,robust',
    ]);
    
    $numClusters = $validated['num_clusters'];
    $normalization = $validated['normalization'];
    
    // Simpan pengaturan terakhir ke session
    session(['last_num_clusters' => $numClusters]);
    session(['last_normalization' => $normalization]);
    
    $data = Beneficiary::all(['id', 'nama', 'nik', 'alamat', 'usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan_perbulan']);
    if ($data->count() < $numClusters) {
        return redirect()->route('statistic.index')->with('error', 'Jumlah data lebih sedikit dari jumlah cluster yang diminta.');
    }
    
    // Hapus hasil cluster lama
    ClusteringResult::truncate();
    NormalizationResult::truncate();
    
    // Ekstrak dan normalisasi data
    // ...
    
    // Lakukan K-Means Clustering
    $clusterer = new KMeans($numClusters);
    $clusterer->train(new Unlabeled($samples));
    $labels = $clusterer->predict(new Unlabeled($samples));
    
    // Hitung silhouette score
    $silhouetteScores = $this->calculateSilhouetteScores($samples, $labels);
    
    // Simpan hasil clustering dan normalisasi ke database
    foreach ($data as $i => $row) {
        ClusteringResult::updateOrCreate([
            'beneficiary_id' => $row->id
        ], [
            'cluster' => $labels[$i],
            'silhouette' => $silhouetteScores[$i]
        ]);
        
        NormalizationResult::updateOrCreate([
            'beneficiary_id' => $row->id
        ], [
            'usia_normalized' => $normalizedData[$i]['usia_normalized'],
            'jumlah_anak_normalized' => $normalizedData[$i]['jumlah_anak_normalized'],
            'kelayakan_rumah_normalized' => $normalizedData[$i]['kelayakan_rumah_normalized'],
            'pendapatan_perbulan_normalized' => $normalizedData[$i]['pendapatan_perbulan_normalized']
        ]);
    }
    
    return redirect()->route('statistic.index')->with('success', $successMessage ?? "Clustering berhasil dilakukan dengan $numClusters cluster.");
}</code></pre>
                </div>
                <p>
                    Fungsi ini adalah fungsi utama yang mengimplementasikan algoritma K-Means. Pertama, fungsi memvalidasi input user, memastikan jumlah cluster dan metode normalisasi valid. Kemudian, fungsi mengekstrak data dari penerima bantuan, mempersiapkan data untuk normalisasi, dan menerapkan normalisasi sesuai metode yang dipilih (tanpa normalisasi, Min-Max, Z-Score, atau Robust). Setelah data dinormalisasi, fungsi menggunakan library Rubix ML untuk melakukan K-Means Clustering dan mendapatkan label cluster untuk setiap penerima bantuan. Fungsi juga menghitung skor silhouette untuk evaluasi kualitas clustering. Terakhir, hasil clustering dan normalisasi disimpan ke database dan pengguna diarahkan kembali ke halaman statistik dengan pesan sukses.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">calculateSilhouetteScores(array $samples, array $labels)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">private function calculateSilhouetteScores(array $samples, array $labels): array
{
    $silhouettes = [];
    $clusterPoints = [];
    
    // Kelompokkan points berdasarkan cluster
    foreach ($labels as $i => $cluster) {
        if (!isset($clusterPoints[$cluster])) {
            $clusterPoints[$cluster] = [];
        }
        $clusterPoints[$cluster][] = $samples[$i];
    }
    
    // Hitung silhouette score untuk setiap point
    foreach ($samples as $i => $sample) {
        $a = $this->calculateAverageDistance($sample, $clusterPoints[$labels[$i]]);
        
        $b = PHP_FLOAT_MAX;
        foreach ($clusterPoints as $cluster => $points) {
            if ($cluster != $labels[$i]) {
                $distance = $this->calculateAverageDistance($sample, $points);
                $b = min($b, $distance);
            }
        }
        
        if ($a == 0 && $b == 0) {
            $silhouettes[$i] = 0;
        } else {
            $silhouettes[$i] = ($b - $a) / max($a, $b);
        }
    }
    
    return $silhouettes;
}</code></pre>
                </div>
                <p>
                    Fungsi privat ini menghitung skor silhouette untuk setiap data point dalam clustering. Skor silhouette adalah ukuran seberapa baik suatu objek cocok dengan klusternya dibandingkan dengan kluster lainnya, dengan nilai -1 hingga 1. Nilai mendekati 1 menunjukkan objek sangat cocok dengan klusternya. Fungsi ini mengelompokkan data berdasarkan kluster, kemudian untuk setiap data, menghitung jarak rata-rata ke semua data lain dalam kluster yang sama (a) dan jarak rata-rata ke data dalam kluster terdekat lainnya (b). Skor silhouette kemudian dihitung dengan rumus (b-a)/max(a,b). Fungsi ini sangat penting untuk evaluasi kualitas hasil clustering.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">calculateAverageDistance(array $point, array $points)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">private function calculateAverageDistance(array $point, array $points): float
{
    if (count($points) <= 1) {
        return 0;
    }
    
    $sum = 0;
    $count = 0;
    
    foreach ($points as $otherPoint) {
        // Skip jika point yang sama
        if ($point === $otherPoint) {
            continue;
        }
        
        $sum += $this->euclideanDistance($point, $otherPoint);
        $count++;
    }
    
    return $count > 0 ? $sum / $count : 0;
}</code></pre>
                </div>
                <p>
                    Fungsi pembantu ini menghitung jarak rata-rata antara satu titik data dengan kumpulan titik data lainnya. Fungsi ini digunakan dalam perhitungan skor silhouette. Fungsi mengiterasi melalui semua titik data dalam kumpulan, menghitung jarak Euclidean antara titik yang diberikan dengan setiap titik lainnya (kecuali titik yang sama), dan mengembalikan rata-rata dari jarak tersebut. Jika hanya ada satu titik atau kurang dalam kumpulan, fungsi mengembalikan 0.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">euclideanDistance(array $point1, array $point2)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">private function euclideanDistance(array $point1, array $point2): float
{
    $sum = 0;
    
    foreach ($point1 as $i => $value) {
        $sum += pow($value - $point2[$i], 2);
    }
    
    return sqrt($sum);
}</code></pre>
                </div>
                <p>
                    Fungsi ini menghitung jarak Euclidean antara dua titik data dalam ruang n-dimensi. Jarak Euclidean adalah ukuran perbedaan antara dua titik data yang menjadi dasar dalam banyak algoritma clustering, termasuk K-Means. Fungsi ini mengkuadratkan selisih antara setiap dimensi dari kedua titik, menjumlahkannya, lalu mengambil akar kuadrat dari jumlah tersebut. Rumus ini adalah implementasi langsung dari teorema Pythagoras yang diperluas ke dimensi yang lebih tinggi.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">normalizeData($data, array $usiaValues, array $anakValues, array $rumahValues, array $pendapatanValues)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">private function normalizeData($data, array $usiaValues, array $anakValues, array $rumahValues, array $pendapatanValues)
{
    // Normalisasi dengan Robust Scaler dari Rubix ML
    $features = [];
    foreach ($data as $i => $row) {
        $features[] = [
            $usiaValues[$i],
            $anakValues[$i],
            $rumahValues[$i],
            $pendapatanValues[$i],
        ];
    }
    $dataset = new \Rubix\ML\Datasets\Unlabeled($features);
    $transformer = new \Rubix\ML\Transformers\RobustStandardizer(true); // center = true
    $transformer->fit($dataset);
    $features = $dataset->samples(); // ambil array 2D dari dataset
    $transformer->transform($features); // transform array, hasilnya by reference
    $normalized = $features;

    // Simpan hasil normalisasi ke array asosiatif
    $normalizedData = [];
    foreach ($data as $i => $row) {
        $normalizedData[] = [
            'beneficiary_id' => $row->id,
            'usia_normalized' => $normalized[$i][0],
            'jumlah_anak_normalized' => $normalized[$i][1],
            'kelayakan_rumah_normalized' => $normalized[$i][2],
            'pendapatan_perbulan_normalized' => $normalized[$i][3],
        ];
    }

    return $normalizedData;
}</code></pre>
                </div>
                <p>
                    Fungsi ini menormalisasi data sebelum clustering menggunakan metode Robust Standardization. Normalisasi adalah proses penskalaan data untuk menghilangkan bias yang mungkin terjadi karena perbedaan skala antar fitur. Fungsi ini menggunakan RobustStandardizer dari library Rubix ML yang lebih tahan terhadap outlier dibandingkan standardisasi biasa. Fungsi menyiapkan dataset dari fitur-fitur yang diberikan, melakukan fitting dan transformasi, kemudian menyimpan hasil normalisasi ke array asosiatif yang menghubungkan ID penerima bantuan dengan nilai-nilai fitur yang sudah dinormalisasi. Normalisasi ini sangat penting untuk memastikan semua fitur diperlakukan sama dalam proses clustering.
                </p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">4. DecisionController</h3>
            <p class="mb-4">
                Controller DecisionController bertanggung jawab untuk mengelola proses pengambilan keputusan berdasarkan hasil clustering untuk distribusi bantuan.
            </p>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">index()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function index()
{
    // Hitung jumlah anggota per cluster
    $clusterCounts = ClusteringResult::select('cluster', DB::raw('count(*) as count'))
        ->groupBy('cluster')
        ->pluck('count', 'cluster')
        ->toArray();
    
    if (empty($clusterCounts)) {
        $clusterCounts = [0 => 0, 1 => 0, 2 => 0];
    }

    // Ambil statistik cluster (mean, silhouette, prioritas)
    $clusterMeans = [];
    $avgSilhouettes = [];
    foreach ($clusterCounts as $cluster => $count) {
        $means = ClusteringResult::where('cluster', $cluster)
            ->join('beneficiaries', 'clustering_results.beneficiary_id', '=', 'beneficiaries.id')
            ->select(
                DB::raw('AVG(usia) as usia'),
                DB::raw('AVG(jumlah_anak) as jumlah_anak'),
                DB::raw('AVG(kelayakan_rumah) as kelayakan_rumah'),
                DB::raw('AVG(pendapatan_perbulan) as pendapatan'),
                DB::raw('AVG(silhouette) as silhouette')
            )
            ->first();
        $clusterMeans[$cluster] = [
            'usia' => (float) $means->usia,
            'jumlah_anak' => (float) $means->jumlah_anak,
            'kelayakan_rumah' => (float) $means->kelayakan_rumah,
            'pendapatan' => (float) $means->pendapatan,
        ];
        $avgSilhouettes[$cluster] = (float) $means->silhouette;
    }
    // Hitung prioritas
    $pendapatanArr = array_column($clusterMeans, 'pendapatan');
    $kelayakanArr = array_column($clusterMeans, 'kelayakan_rumah');
    $jumlahAnakArr = array_column($clusterMeans, 'jumlah_anak');
    $needScores = [];
    foreach ($clusterMeans as $idx => $mean) {
        $pendapatan = $mean['pendapatan'] ?? 0;
        $kelayakan = $mean['kelayakan_rumah'] ?? 0;
        $jumlah_anak = $mean['jumlah_anak'] ?? 0;
        $score = (max($pendapatanArr) - $pendapatan)
            + (max($kelayakanArr) - $kelayakan)
            + ($jumlah_anak - min($jumlahAnakArr));
        $needScores[$idx] = $score;
    }
    arsort($needScores);
    $rankMap = [];
    $rank = 1;
    foreach(array_keys($needScores) as $idx) {
        $rankMap[$idx] = $rank++;
    }
    // Ambil semua decision results untuk ditampilkan
    $decisionResults = DecisionResult::orderBy('created_at', 'desc')->get();
    
    return view('decision.index', [
        'clusterCounts' => $clusterCounts,
        'decisionResults' => $decisionResults,
        'clusterMeans' => $clusterMeans,
        'avgSilhouettes' => $avgSilhouettes,
        'rankMap' => $rankMap
    ]);
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan halaman utama panel keputusan dengan informasi tentang distribusi cluster dan daftar keputusan yang telah dibuat sebelumnya. Pertama, fungsi menghitung jumlah anggota per cluster, lalu mengambil statistik untuk setiap cluster seperti rata-rata usia, jumlah anak, kelayakan rumah, dan pendapatan. Fungsi juga menghitung prioritas cluster berdasarkan kebutuhan bantuan dengan formula yang mempertimbangkan pendapatan (lebih rendah lebih prioritas), kelayakan rumah (lebih rendah lebih prioritas), dan jumlah anak (lebih tinggi lebih prioritas). Terakhir, fungsi mengambil semua keputusan yang telah dibuat dan merender view 'decision.index' dengan semua data ini.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">create()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function create()
{
    // Hitung jumlah anggota per cluster
    $clusterCounts = ClusteringResult::select('cluster', DB::raw('count(*) as count'))
        ->groupBy('cluster')
        ->pluck('count', 'cluster')
        ->toArray();
        
    if (empty($clusterCounts)) {
        return redirect()->route('decision.index')->with('error', 'Belum ada data clustering. Silakan lakukan clustering terlebih dahulu.');
    }

    // Ambil rata-rata fitur per cluster untuk prioritas
    $clusterMeans = [];
    foreach ($clusterCounts as $cluster => $count) {
        $means = ClusteringResult::where('cluster', $cluster)
            ->join('beneficiaries', 'clustering_results.beneficiary_id', '=', 'beneficiaries.id')
            ->select(
                DB::raw('AVG(usia) as usia'),
                DB::raw('AVG(jumlah_anak) as jumlah_anak'),
                DB::raw('AVG(kelayakan_rumah) as kelayakan_rumah'),
                DB::raw('AVG(pendapatan_perbulan) as pendapatan')
            )
            ->first();
        $clusterMeans[$cluster] = [
            'usia' => (float) $means->usia,
            'jumlah_anak' => (float) $means->jumlah_anak,
            'kelayakan_rumah' => (float) $means->kelayakan_rumah,
            'pendapatan' => (float) $means->pendapatan,
        ];
    }

    // Hitung skor kebutuhan bantuan untuk setiap cluster
    $pendapatanArr = array_column($clusterMeans, 'pendapatan');
    $kelayakanArr = array_column($clusterMeans, 'kelayakan_rumah');
    $jumlahAnakArr = array_column($clusterMeans, 'jumlah_anak');
    $needScores = [];
    foreach ($clusterMeans as $idx => $mean) {
        $pendapatan = $mean['pendapatan'] ?? 0;
        $kelayakan = $mean['kelayakan_rumah'] ?? 0;
        $jumlah_anak = $mean['jumlah_anak'] ?? 0;
        $score = (max($pendapatanArr) - $pendapatan)
            + (max($kelayakanArr) - $kelayakan)
            + ($jumlah_anak - min($jumlahAnakArr));
        $needScores[$idx] = $score;
    }
    arsort($needScores);
    $rankMap = [];
    $rank = 1;
    foreach(array_keys($needScores) as $idx) {
        $rankMap[$idx] = $rank++;
    }

    return view('decision.create', [
        'clusterCounts' => $clusterCounts,
        'rankMap' => $rankMap
    ]);
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan formulir untuk membuat keputusan baru. Pertama, fungsi memeriksa apakah sudah ada data hasil clustering. Jika belum, pengguna akan diarahkan kembali ke halaman index dengan pesan error. Jika sudah, fungsi menghitung jumlah anggota per cluster dan rata-rata fitur per cluster untuk menentukan prioritas. Sama seperti pada fungsi index(), fungsi ini menghitung skor kebutuhan bantuan untuk setiap cluster dan membuat peringkat cluster berdasarkan skor tersebut. Data ini kemudian dikirim ke view 'decision.create' untuk membantu pengguna dalam memilih cluster yang tepat untuk distribusi bantuan.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">store(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'cluster' => 'required',
        'count' => 'required|integer|min:1',
        'notes' => 'nullable|string',
    ]);

    $cluster = $validated['cluster'];
    $totalNeeded = $validated['count'];
    $beneficiaryIds = [];

    if ($cluster === 'all') {
        // Hitung prioritas (rankMap) seperti di create()
        $clusterCounts = ClusteringResult::select('cluster', DB::raw('count(*) as count'))
            ->groupBy('cluster')
            ->pluck('count', 'cluster')
            ->toArray();
        $clusterMeans = [];
        foreach ($clusterCounts as $cl => $count) {
            $means = ClusteringResult::where('cluster', $cl)
                ->join('beneficiaries', 'clustering_results.beneficiary_id', '=', 'beneficiaries.id')
                ->select(
                    DB::raw('AVG(usia) as usia'),
                    DB::raw('AVG(jumlah_anak) as jumlah_anak'),
                    DB::raw('AVG(kelayakan_rumah) as kelayakan_rumah'),
                    DB::raw('AVG(pendapatan_perbulan) as pendapatan')
                )
                ->first();
            $clusterMeans[$cl] = [
                'usia' => (float) $means->usia,
                'jumlah_anak' => (float) $means->jumlah_anak,
                'kelayakan_rumah' => (float) $means->kelayakan_rumah,
                'pendapatan' => (float) $means->pendapatan,
            ];
        }
        $pendapatanArr = array_column($clusterMeans, 'pendapatan');
        $kelayakanArr = array_column($clusterMeans, 'kelayakan_rumah');
        $jumlahAnakArr = array_column($clusterMeans, 'jumlah_anak');
        $needScores = [];
        foreach ($clusterMeans as $idx => $mean) {
            $pendapatan = $mean['pendapatan'] ?? 0;
            $kelayakan = $mean['kelayakan_rumah'] ?? 0;
            $jumlah_anak = $mean['jumlah_anak'] ?? 0;
            $score = (max($pendapatanArr) - $pendapatan)
                + (max($kelayakanArr) - $kelayakan)
                + ($jumlah_anak - min($jumlahAnakArr));
            $needScores[$idx] = $score;
        }
        arsort($needScores);
        $prioritasClusters = array_keys($needScores);

        // Ambil penerima dari prioritas 1, 2, dst
        $remaining = $totalNeeded;
        foreach ($prioritasClusters as $cl) {
            if ($remaining <= 0) break;
            $ids = ClusteringResult::where('cluster', $cl)
                ->whereNotIn('beneficiary_id', $beneficiaryIds)
                ->inRandomOrder()
                ->limit($remaining)
                ->pluck('beneficiary_id')
                ->toArray();
            $beneficiaryIds = array_merge($beneficiaryIds, $ids);
            $remaining = $totalNeeded - count($beneficiaryIds);
        }
        if (count($beneficiaryIds) < $totalNeeded) {
            return back()->withErrors(['count' => "Jumlah yang dipilih melebihi total seluruh cluster (" . count($beneficiaryIds) . ")"])->withInput();
        }
    } else {
        // Validasi cluster harus integer dan ada di data
        if (!is_numeric($cluster) || !ClusteringResult::where('cluster', $cluster)->exists()) {
            return back()->withErrors(['cluster' => 'Cluster tidak valid'])->withInput();
        }
        $clusterCount = ClusteringResult::where('cluster', $cluster)->count();
        if ($totalNeeded > $clusterCount) {
            return back()->withErrors(['count' => "Jumlah yang dipilih melebihi jumlah anggota dalam cluster ({$clusterCount})"])->withInput();
        }
        $beneficiaryIds = ClusteringResult::where('cluster', $cluster)
            ->inRandomOrder()
            ->limit($totalNeeded)
            ->pluck('beneficiary_id')
            ->toArray();
    }

    DB::beginTransaction();
    try {
        // Simpan decision result
        $decisionResult = DecisionResult::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'cluster' => $cluster === 'all' ? -1 : $cluster,
            'count' => $totalNeeded,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Buat item untuk setiap penerima yang dipilih
        foreach ($beneficiaryIds as $beneficiaryId) {
            DecisionResultItem::create([
                'decision_result_id' => $decisionResult->id,
                'beneficiary_id' => $beneficiaryId
            ]);
        }

        DB::commit();
        return redirect()->route('decision.show', $decisionResult->id)->with('success', 'Keputusan berhasil dibuat!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
    }
}</code></pre>
                </div>
                <p>
                    Fungsi ini memproses pembuatan keputusan baru untuk distribusi bantuan. Pertama, fungsi memvalidasi input dari form, memastikan semua field yang diperlukan telah diisi dengan benar. Berdasarkan pilihan cluster (cluster tertentu atau 'all' untuk semua cluster), fungsi menentukan penerima bantuan yang akan dipilih. Jika 'all' dipilih, fungsi akan menghitung prioritas cluster seperti pada fungsi create() dan mengambil penerima bantuan mulai dari cluster dengan prioritas tertinggi. Jika cluster tertentu dipilih, fungsi akan mengambil penerima bantuan secara acak dari cluster tersebut. Fungsi juga melakukan validasi untuk memastikan jumlah yang diminta tidak melebihi jumlah penerima yang tersedia. Semua proses pembuatan keputusan dijalankan dalam transaksi database untuk memastikan integritas data.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">show($id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function show($id)
{
    $decisionResult = DecisionResult::with('beneficiaries')->findOrFail($id);
    
    return view('decision.show', [
        'decisionResult' => $decisionResult
    ]);
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan detail lengkap dari keputusan distribusi bantuan yang telah dibuat. Fungsi mengambil data keputusan beserta daftar penerima bantuan yang terpilih menggunakan eager loading untuk optimasi performa. Data ini kemudian ditampilkan di view 'decision.show' untuk menampilkan informasi seperti judul keputusan, deskripsi, tanggal pembuatan, cluster yang dipilih, jumlah penerima bantuan, dan daftar lengkap penerima bantuan beserta detailnya.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">destroy($id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function destroy($id)
{
    $decisionResult = DecisionResult::findOrFail($id);
    $decisionResult->delete();
    
    return redirect()->route('decision.index')->with('success', 'Keputusan berhasil dihapus!');
}</code></pre>
                </div>
                <p>
                    Fungsi ini menghapus keputusan distribusi bantuan yang tidak diperlukan lagi. Fungsi mencari data keputusan berdasarkan ID yang diberikan, lalu menghapusnya dari database. Perlu dicatat bahwa Laravel akan secara otomatis menghapus semua item terkait dalam tabel decision_result_items melalui mekanisme cascade delete yang didefinisikan dalam model atau migrasi. Setelah berhasil dihapus, pengguna diarahkan kembali ke halaman index dengan pesan sukses.
                </p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">5. ProfileController</h3>
            <p class="mb-4">
                Controller ProfileController mengelola semua fitur terkait dengan pengelolaan profil pengguna, termasuk menampilkan dan memperbarui data profil, mengubah password, serta mengunggah avatar pengguna.
            </p>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">edit()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function edit()
{
    return view('profile.edit', [
        'user' => Auth::user()
    ]);
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan halaman edit profil pengguna yang sedang login. Fungsi menggunakan Auth::user() untuk mendapatkan data pengguna saat ini dan mengirimkannya ke view 'profile.edit'. Halaman ini biasanya berisi formulir untuk mengubah data pribadi pengguna seperti nama dan email, serta opsi untuk mengubah password dan avatar.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">update(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function update(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
    ]);

    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

    return redirect()->route('profile.edit')->with('status', 'Profil berhasil diperbarui.');
}</code></pre>
                </div>
                <p>
                    Fungsi ini memproses permintaan pembaruan data profil pengguna. Pertama, fungsi mengambil objek pengguna yang sedang login. Kemudian, fungsi memvalidasi input dari formulir, memastikan nama diisi dan tidak melebihi 255 karakter, serta email valid dan unik di database (kecuali untuk pengguna saat ini menggunakan Rule::unique('users')->ignore($user->id)). 
                    
                    Setelah validasi berhasil, fungsi menggunakan query builder (DB::table) untuk memperbarui data pengguna di database. Setelah pembaruan berhasil, pengguna diarahkan kembali ke halaman edit profil dengan pesan status sukses. Penggunaan query builder langsung alih-alih model Eloquent adalah pendekatan alternatif yang bisa digunakan untuk operasi database sederhana.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">updatePassword(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function updatePassword(Request $request)
{
    $validated = $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $user = Auth::user();
    
    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'password' => Hash::make($validated['password']),
        ]);

    return redirect()->route('profile.edit')->with('status', 'Password berhasil diperbarui.');
}</code></pre>
                </div>
                <p>
                    Fungsi ini menangani pembaruan password pengguna dengan langkah-langkah keamanan yang tepat. Pertama, fungsi memvalidasi input dari formulir menggunakan aturan validasi:
                    <ul class="list-disc ml-6 mt-2">
                        <li>'current_password' - memastikan password saat ini yang dimasukkan sesuai dengan yang tersimpan di database menggunakan validator bawaan Laravel 'current_password'</li>
                        <li>'password' - memastikan password baru diisi, dikonfirmasi (dengan field password_confirmation), dan minimal 8 karakter</li>
                    </ul>
                    
                    Setelah validasi berhasil, fungsi mengambil pengguna saat ini dan menggunakan DB query builder untuk memperbarui password dengan versi yang sudah di-hash menggunakan Hash::make(). Akhirnya, pengguna diarahkan kembali ke halaman edit profil dengan pesan sukses. Pendekatan ini memastikan password disimpan dengan aman dan pengguna memverifikasi password lama sebelum mengubahnya.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">updateAvatar(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => ['required', 'image', 'max:2048'], // Maksimal 2MB
    ]);

    $user = Auth::user();

    // Hapus avatar lama jika ada
    if ($user->avatar) {
        Storage::disk('public')->delete($user->avatar);
    }

    // Simpan avatar baru
    $path = $request->file('avatar')->store('avatars', 'public');

    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'avatar' => $path,
        ]);

    return redirect()->route('profile.edit')->with('status', 'Foto profil berhasil diperbarui.');
}</code></pre>
                </div>
                <p>
                    Fungsi ini memproses pengunggahan dan pembaruan foto profil (avatar) pengguna. Langkah-langkah yang dilakukan:
                    <ul class="list-disc ml-6 mt-2">
                        <li>Memvalidasi file yang diunggah, memastikan file tersebut ada, berupa gambar (menggunakan validator 'image'), dan ukurannya tidak melebihi 2MB</li>
                        <li>Mengambil data pengguna yang sedang login</li>
                        <li>Jika pengguna sudah memiliki avatar sebelumnya, hapus avatar lama dari storage untuk menghemat ruang penyimpanan</li>
                        <li>Menyimpan file avatar baru ke direktori 'avatars' pada disk 'public' menggunakan fitur storage Laravel</li>
                        <li>Memperbarui field 'avatar' pada record pengguna di database dengan path file yang baru disimpan</li>
                    </ul>
                    
                    Setelah proses selesai, pengguna diarahkan kembali ke halaman edit profil dengan pesan sukses. Fungsi ini menunjukkan implementasi yang baik untuk pengelolaan file dalam Laravel dengan penanganan file lama dan penggunaan sistem storage untuk keamanan dan fleksibilitas.
                </p>
            </div>
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
            Controller dalam aplikasi K-Means Clustering ini dirancang untuk mendukung seluruh proses dalam sistem, mulai dari autentikasi pengguna, pengelolaan data penerima bantuan (beneficiaries), implementasi algoritma clustering, hingga pengambilan keputusan berdasarkan hasil clustering. Setiap controller memiliki tanggung jawab yang jelas dan terfokus, mengikuti prinsip Single Responsibility dalam desain perangkat lunak. Dengan struktur yang terorganisir dan pemanfaatan fitur-fitur Laravel seperti validasi request, dependency injection, dan middleware, controller dalam aplikasi ini memberikan fondasi yang solid untuk implementasi logika aplikasi dan interaksi dengan pengguna.
        </p>
    </div>
@endsection 