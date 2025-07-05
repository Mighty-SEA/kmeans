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
                Controller AuthController menangani seluruh proses autentikasi pengguna dalam aplikasi, termasuk login, registrasi, dan logout.
            </p>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">showLoginForm()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function showLoginForm()
{
    return view('auth.login');
}</code></pre>
            </div>
            <p>
                    Fungsi ini bertanggung jawab untuk menampilkan halaman login kepada pengguna. Fungsi ini merender view 'auth.login' yang berisi formulir untuk memasukkan email dan password.
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
                <p>
                    Fungsi ini memproses permintaan login dengan memvalidasi kredensial yang dimasukkan pengguna. Pertama, fungsi memvalidasi data yang dikirim, memastikan email dan password telah diisi. Kemudian mencoba mengautentikasi pengguna menggunakan Auth::attempt(). Jika berhasil, sesi pengguna diregenerasi untuk keamanan dan pengguna diarahkan ke halaman yang dimaksud (atau halaman utama jika tidak ada). Jika gagal, pengguna dikembalikan ke halaman login dengan pesan kesalahan.
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
            <p>
                    Fungsi ini bertanggung jawab untuk menampilkan halaman registrasi kepada pengguna. Fungsi ini merender view 'auth.register' yang berisi formulir pendaftaran pengguna baru.
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
                <p>
                    Fungsi ini memproses pendaftaran pengguna baru. Pertama, data yang dikirim divalidasi dengan beberapa aturan: nama harus diisi, email harus valid dan unik, dan password minimal 8 karakter dan harus dikonfirmasi. Setelah validasi, pengguna baru dibuat di database dengan password yang di-hash untuk keamanan. Setelah berhasil dibuat, pengguna langsung diautentikasi dan diarahkan ke halaman utama.
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
            <p>
                    Fungsi ini menangani proses logout pengguna. Pertama, fungsi mengakhiri sesi autentikasi pengguna dengan Auth::logout(). Kemudian, sesi saat ini diinvalidasi dan token CSRF diregenerasi untuk keamanan. Terakhir, pengguna diarahkan kembali ke halaman login.
                </p>
            </div>
        </div>
        
        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">2. BeneficiaryController</h3>
            <p class="mb-4">
                Controller BeneficiaryController mengelola seluruh operasi terkait data penerima bantuan (beneficiaries), yang merupakan data utama dalam aplikasi ini.
            </p>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">dashboard()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function dashboard()
{
    // Mengambil total penerima
    $totalPenerima = Beneficiary::count();
    
    // Mengambil 5 data penerima terbaru
    $latestData = Beneficiary::latest()->take(5)->get();
    
    // Menambahkan data cluster ke penerima terbaru
    foreach ($latestData as $beneficiary) {
        $clusterResult = ClusteringResult::where('beneficiary_id', $beneficiary->id)->first();
        $beneficiary->cluster = $clusterResult ? $clusterResult->cluster : null;
    }
    
    // Mengambil distribusi cluster
    $clusterDistribution = ClusteringResult::select('cluster', DB::raw('count(*) as total'))
        ->groupBy('cluster')
        ->pluck('total', 'cluster')
        ->toArray();
    
    // Memastikan semua indeks cluster (0, 1, 2) tersedia
    $clusterCounts = [
        0 => $clusterDistribution[0] ?? 0,
        1 => $clusterDistribution[1] ?? 0,
        2 => $clusterDistribution[2] ?? 0
    ];
    
    // Menghitung rata-rata fitur per cluster
    $clusterMeans = [];
    for ($i = 0; $i < 3; $i++) {
        $clusterData = Beneficiary::join('clustering_results', 'beneficiaries.id', '=', 'clustering_results.beneficiary_id')
            ->where('clustering_results.cluster', $i)
            ->get();
        
        $count = $clusterData->count();
        $clusterMeans[$i] = [
            'usia' => $count ? $clusterData->avg('usia') : 0,
            'jumlah_anak' => $count ? $clusterData->avg('jumlah_anak') : 0,
            'kelayakan_rumah' => $count ? $clusterData->avg('kelayakan_rumah') : 0,
            'pendapatan' => $count ? $clusterData->avg('pendapatan_perbulan') : 0,
        ];
    }
    
    return view('dashboard', compact(
        'totalPenerima',
        'latestData',
        'clusterCounts',
        'clusterMeans'
    ));
}</code></pre>
                </div>
                <p>
                    Fungsi ini menyiapkan data untuk tampilan dashboard utama. Pertama, menghitung total jumlah penerima bantuan dan mengambil 5 data penerima terbaru. Kemudian, untuk setiap data terbaru, fungsi menambahkan informasi cluster (jika ada). Fungsi juga mengambil distribusi jumlah anggota per cluster dan memastikan semua indeks cluster tersedia dengan nilai default 0 jika tidak ada data. Fungsi juga menghitung rata-rata fitur (usia, jumlah anak, kelayakan rumah, dan pendapatan) untuk setiap cluster. Semua data ini kemudian dilewatkan ke view 'dashboard' untuk ditampilkan.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">index(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function index(Request $request)
{
    $search = $request->input('search');
    $perPage = $request->input('perPage', 10);
    
    $query = Beneficiary::query();
    
    // Filter berdasarkan pencarian
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('alamat', 'like', "%{$search}%")
              ->orWhere('no_hp', 'like', "%{$search}%");
        });
    }
    
    $penerima = $query->paginate($perPage)->withQueryString();
    
    return view('beneficiaries.index', compact('penerima', 'search', 'perPage'));
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan daftar penerima bantuan dengan fitur pencarian dan paginasi. Fungsi mengambil parameter pencarian dan jumlah item per halaman dari request. Jika ada parameter pencarian, fungsi akan memfilter data berdasarkan nama, NIK, alamat, atau nomor HP. Hasil query kemudian di-paginate sesuai dengan jumlah item per halaman yang diminta dan ditampilkan di view 'beneficiaries.index'.
            </p>
        </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">create()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function create()
{
    return view('beneficiaries.create');
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan halaman formulir untuk menambahkan data penerima bantuan baru. Fungsi ini hanya merender view 'beneficiaries.create' yang berisi formulir input data.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">store(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function store(Request $request)
{
    $validated = $request->validate([
        'nik' => 'required',
        'nama' => 'required',
        'alamat' => 'required',
        'no_hp' => 'required',
        'usia' => 'required|integer',
        'jumlah_anak' => 'required|integer',
        'kelayakan_rumah' => 'required',
        'pendapatan_perbulan' => 'required|numeric',
    ]);
    Beneficiary::create($validated);
    return redirect()->route('beneficiary.index')->with('success', 'Data berhasil ditambahkan!');
}</code></pre>
                </div>
                <p>
                    Fungsi ini memproses penambahan data penerima bantuan baru. Pertama, fungsi memvalidasi input dari formulir, memastikan semua field yang diperlukan telah diisi dengan benar. Kemudian, fungsi membuat record baru di database menggunakan model Beneficiary. Setelah berhasil, pengguna diarahkan kembali ke halaman daftar penerima bantuan dengan pesan sukses.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">edit($id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function edit($id)
{
    $penerima = Beneficiary::findOrFail($id);
    return view('beneficiaries.edit', compact('penerima'));
}</code></pre>
                </div>
                <p>
                    Fungsi ini menampilkan halaman formulir untuk mengedit data penerima bantuan yang sudah ada. Fungsi mengambil data penerima berdasarkan ID yang diberikan dan mengirimkannya ke view 'beneficiaries.edit' untuk ditampilkan dalam formulir.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">update(Request $request, $id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function update(Request $request, $id)
{
    $validated = $request->validate([
        'nik' => 'required',
        'nama' => 'required',
        'alamat' => 'required',
        'no_hp' => 'required',
        'usia' => 'required|integer',
        'jumlah_anak' => 'required|integer',
        'kelayakan_rumah' => 'required',
        'pendapatan_perbulan' => 'required|numeric',
    ]);
    $penerima = Beneficiary::findOrFail($id);
    $penerima->update($validated);
    return redirect()->route('beneficiary.index')->with('success', 'Data berhasil diupdate!');
}</code></pre>
                </div>
                <p>
                    Fungsi ini memproses pembaruan data penerima bantuan yang sudah ada. Fungsi memvalidasi input dari formulir, kemudian mencari data penerima berdasarkan ID yang diberikan. Setelah data ditemukan, fungsi memperbarui data tersebut dengan nilai-nilai baru dari formulir. Setelah berhasil, pengguna diarahkan kembali ke halaman daftar penerima bantuan dengan pesan sukses.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">destroy($id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function destroy($id)
{
    // Cari data penerima bantuan berdasarkan ID
    $beneficiary = Beneficiary::findOrFail($id);
    
    // Hapus data normalisasi dan clustering jika ada
    $beneficiary->normalizationResult()->delete();
    $beneficiary->clusteringResult()->delete();
    
    // Hapus data penerima
    $beneficiary->delete();
    
    return redirect()->route('beneficiary.index')->with('success', 'Data penerima bantuan berhasil dihapus');
}</code></pre>
                </div>
                <p>
                    Fungsi ini menghapus data penerima bantuan beserta data terkait lainnya. Fungsi mencari data penerima berdasarkan ID, lalu menghapus data normalisasi dan clustering yang terkait dengan penerima tersebut (jika ada), kemudian menghapus data penerima itu sendiri. Pendekatan ini memastikan tidak ada data yang tertinggal atau menjadi orphaned setelah penghapusan. Setelah berhasil, pengguna diarahkan kembali ke halaman daftar penerima bantuan dengan pesan sukses.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">exportExcel(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function exportExcel(Request $request)
{
    $columns = $request->input('columns', [
        'nik',
        'nama',
        'alamat',
        'no_hp',
        'usia',
        'jumlah_anak',
        'kelayakan_rumah',
        'pendapatan_perbulan',
    ]);
    return Excel::download(new BeneficiaryExport($columns), 'beneficiary.xlsx');
}</code></pre>
                </div>
                <p>
                    Fungsi ini mengekspor data penerima bantuan ke format Excel. Fungsi mengambil parameter kolom yang akan diekspor dari request, dengan nilai default semua kolom. Kemudian menggunakan class BeneficiaryExport untuk menghasilkan file Excel dan mengunduhkannya dengan nama 'beneficiary.xlsx'. Fitur ini memudahkan pengguna untuk melakukan analisis data lebih lanjut di luar aplikasi.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">importExcel(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function importExcel(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);
    Excel::import(new BeneficiaryImport, $request->file('file'));
    return redirect()->route('beneficiary.index')->with('success', 'Data berhasil diimport!');
}</code></pre>
                </div>
                <p>
                    Fungsi ini mengimpor data penerima bantuan dari file Excel. Fungsi memvalidasi bahwa file yang diunggah adalah file Excel (format xlsx atau xls), kemudian menggunakan class BeneficiaryImport untuk memproses file dan menyimpan data ke database. Setelah berhasil, pengguna diarahkan kembali ke halaman daftar penerima bantuan dengan pesan sukses. Fitur ini memudahkan pengguna untuk menambahkan banyak data sekaligus tanpa harus menginputnya satu per satu.
                </p>
            </div>

            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">bulkDelete(Request $request)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function bulkDelete(Request $request)
{
    if ($request->input('select_all') == 1) {
        Beneficiary::query()->delete();
        return redirect()->route('beneficiary.index')->with('success', 'Semua data berhasil dihapus!');
    }
    $ids = $request->input('ids', []);
    if (!empty($ids)) {
        Beneficiary::whereIn('id', $ids)->delete();
        return redirect()->route('beneficiary.index')->with('success', 'Data terpilih berhasil dihapus!');
    }
    return redirect()->route('beneficiary.index')->with('success', 'Tidak ada data yang dipilih.');
}</code></pre>
                </div>
                <p>
                    Fungsi ini menghapus banyak data penerima bantuan sekaligus. Jika parameter 'select_all' bernilai 1, semua data penerima akan dihapus dari database. Jika tidak, fungsi mengambil array ID dari request dan menghapus data penerima yang sesuai. Fungsi juga menangani kasus di mana tidak ada ID yang dipilih. Setelah berhasil, pengguna diarahkan kembali ke halaman daftar penerima bantuan dengan pesan sukses. Fitur ini sangat berguna untuk manajemen data massal.
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