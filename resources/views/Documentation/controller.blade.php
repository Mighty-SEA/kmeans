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
    ));</code></pre>
                </div>
                <p class="mb-4">
                    <b>dashboard()</b> adalah fungsi yang bertugas untuk menyiapkan dan menyajikan data ringkasan terkait penerima bantuan pada halaman utama dashboard aplikasi. Fungsi ini tidak hanya menampilkan jumlah total penerima, tetapi juga menampilkan data penerima terbaru, distribusi anggota per cluster, serta rata-rata fitur penting seperti usia, jumlah anak, kelayakan rumah, dan pendapatan per cluster. Fungsi ini melakukan beberapa operasi data penting: (1) Menghitung total jumlah penerima, (2) Mengambil 5 data penerima terbaru dengan metode latest(), (3) Menambahkan informasi cluster ke setiap data penerima terbaru, (4) Mengambil distribusi jumlah anggota per cluster, dan (5) Menghitung nilai rata-rata dari semua fitur untuk setiap cluster. Dengan demikian, fungsi ini memberikan gambaran menyeluruh kepada pengguna mengenai kondisi data penerima bantuan secara real-time yang sangat penting untuk mendukung proses monitoring, evaluasi, dan pengambilan keputusan berbasis data dalam aplikasi.
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
    
    return view('beneficiaries.index', compact('penerima', 'search', 'perPage'));</code></pre>
                </div>
                <p class="mb-4">
                    <b>index(Request $request)</b> berfungsi untuk menampilkan daftar lengkap penerima bantuan dengan fitur pencarian dan paginasi. Fungsi ini mengambil parameter <i>search</i> dan <i>perPage</i> dari request, yang memungkinkan pengguna untuk mencari data berdasarkan kata kunci dan mengatur jumlah data yang ditampilkan per halaman. Query pencarian dirancang untuk mencari pada beberapa kolom sekaligus (nama, NIK, alamat, dan nomor HP) menggunakan klausa where dengan operator like, sehingga hasil pencarian lebih komprehensif. Fungsi ini juga menerapkan paginasi dengan metode <i>paginate()</i> yang dilengkapi dengan <i>withQueryString()</i> untuk memastikan parameter pencarian tetap terbawa saat navigasi antar halaman. Hasil query kemudian dikirim ke view untuk ditampilkan dalam format tabel yang terstruktur. Pendekatan ini sangat efektif dalam mengelola data dalam jumlah besar, memberikan pengalaman pengguna yang optimal, dan memudahkan pencarian informasi spesifik.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">create()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function create()
{
    return view('beneficiaries.create');</code></pre>
                </div>
                <p class="mb-4">
                    <b>create()</b> adalah fungsi yang bertugas untuk menampilkan formulir penambahan data penerima bantuan baru. Fungsi ini mengikuti prinsip Separation of Concerns dalam Laravel, di mana logika tampilan dipisahkan dari logika pemrosesan data. Dengan hanya memanggil view 'beneficiaries.create', fungsi ini membuat kode menjadi lebih bersih, mudah dimengerti, dan mudah dikelola. View yang dimuat akan menampilkan formulir dengan berbagai field input yang diperlukan untuk membuat data penerima baru, seperti NIK, nama, alamat, nomor HP, usia, jumlah anak, kelayakan rumah, dan pendapatan perbulan. Formulir ini biasanya juga dilengkapi dengan validasi client-side untuk memberikan feedback langsung kepada pengguna saat mengisi data, sehingga meminimalisir kesalahan input sebelum data dikirim ke server.
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
    return redirect()->route('beneficiary.index')->with('success', 'Data berhasil ditambahkan!');</code></pre>
                </div>
                <p class="mb-4">
                    <b>store(Request $request)</b> merupakan fungsi yang menangani proses penyimpanan data penerima bantuan baru ke dalam database. Fungsi ini menerapkan validasi yang ketat dengan metode <i>validate()</i> untuk memastikan setiap field yang diinputkan memenuhi kriteria yang ditentukan. Aturan validasi yang diterapkan meliputi: field yang wajib diisi untuk semua data, tipe data integer untuk usia dan jumlah anak, serta tipe data numeric untuk pendapatan perbulan. Setelah data tervalidasi, fungsi ini menggunakan metode <i>create()</i> pada model Beneficiary untuk menyimpan data ke database, yang memanfaatkan fitur mass assignment Laravel dengan array $validated yang sudah aman. Setelah penyimpanan berhasil, fungsi ini mengarahkan pengguna kembali ke halaman daftar penerima bantuan dengan pesan sukses. Pendekatan ini memastikan bahwa hanya data yang valid yang masuk ke sistem, serta memberikan feedback yang jelas kepada pengguna tentang hasil dari tindakan yang dilakukan.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">edit($id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function edit($id)
{
    $penerima = Beneficiary::findOrFail($id);
    return view('beneficiaries.edit', compact('penerima'));</code></pre>
                </div>
                <p class="mb-4">
                    <b>edit($id)</b> adalah fungsi yang digunakan untuk menampilkan formulir pengeditan data penerima bantuan yang sudah ada. Fungsi ini menggunakan metode <i>findOrFail()</i> untuk mencari data penerima berdasarkan ID yang diberikan, yang akan otomatis menampilkan halaman 404 Not Found jika ID tidak ditemukan. Pendekatan ini meningkatkan keamanan aplikasi dengan mencegah manipulasi ID yang tidak valid. Setelah data ditemukan, fungsi ini memanggil view 'beneficiaries.edit' dan meneruskan data penerima menggunakan helper <i>compact()</i>, sehingga view dapat mengakses dan menampilkan data tersebut dalam form. Form edit biasanya sudah terisi dengan data yang ada, memudahkan pengguna untuk melihat dan mengubah hanya bagian yang perlu diperbarui. Pendekatan ini juga mendukung pengalaman pengguna yang lebih baik dengan menerapkan prinsip "less work for users".
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
    return redirect()->route('beneficiary.index')->with('success', 'Data berhasil diupdate!');</code></pre>
                </div>
                <p class="mb-4">
                    <b>update(Request $request, $id)</b> berfungsi untuk memproses pembaruan data penerima bantuan yang sudah ada di database. Mirip dengan fungsi store(), fungsi ini menerapkan validasi yang sama untuk memastikan data yang diinputkan valid dan konsisten. Setelah validasi berhasil, fungsi ini mencari data penerima dengan metode <i>findOrFail()</i> berdasarkan ID yang diberikan. Kemudian, metode <i>update()</i> dipanggil dengan data yang telah divalidasi untuk memperbarui record di database. Pendekatan ini menjaga integritas data dengan: (1) Memastikan validasi data yang konsisten antara proses create dan update, (2) Memverifikasi keberadaan data sebelum memperbarui, dan (3) Menggunakan mass assignment yang aman dengan data yang telah divalidasi. Setelah proses update selesai, pengguna diarahkan kembali ke halaman daftar dengan pesan sukses, memberikan konfirmasi visual bahwa perubahan telah berhasil disimpan.
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
    
    return redirect()->route('beneficiary.index')->with('success', 'Data penerima bantuan berhasil dihapus');</code></pre>
                </div>
                <p class="mb-4">
                    <b>destroy($id)</b> adalah fungsi yang bertanggung jawab untuk menghapus data penerima bantuan beserta data terkait lainnya dari sistem. Fungsi ini menggunakan pendekatan yang komprehensif dan terstruktur dalam proses penghapusan data. Pertama, fungsi mencari data penerima dengan metode <i>findOrFail()</i>, yang menjamin bahwa hanya ID valid yang dapat diproses. Selanjutnya, fungsi ini menghapus data-data terkait terlebih dahulu, yaitu data normalisasi dan clustering, menggunakan relationship methods yang didefinisikan dalam model Beneficiary. Pendekatan ini lebih elegan dibandingkan dengan menghapus data secara manual, karena memanfaatkan fitur Eloquent Relationships dari Laravel. Setelah data terkait dihapus, baru kemudian data utama penerima dihapus dengan metode <i>delete()</i>. Urutan penghapusan ini sangat penting untuk menjaga integritas referensial database dan mencegah terjadinya orphaned records. Setelah proses penghapusan selesai, pengguna diarahkan kembali ke halaman daftar dengan pesan sukses, memberikan konfirmasi bahwa operasi penghapusan telah berhasil dilakukan.
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
    return Excel::download(new BeneficiaryExport($columns), 'beneficiary.xlsx');</code></pre>
                </div>
                <p class="mb-4">
                    <b>exportExcel(Request $request)</b> merupakan fungsi yang memungkinkan pengguna untuk mengekspor data penerima bantuan ke dalam format Excel. Fungsi ini mengimplementasikan fleksibilitas dalam ekspor data dengan memungkinkan pengguna untuk memilih kolom-kolom yang ingin diekspor. Parameter <i>columns</i> dari request digunakan untuk menentukan kolom yang akan diekspor, dengan nilai default berupa array yang mencakup semua kolom utama jika tidak ada pilihan spesifik dari pengguna. Fungsi ini menggunakan paket Laravel Excel (Maatwebsite\Excel) yang populer, dengan membuat instance dari class BeneficiaryExport dan meneruskan kolom yang dipilih ke constructor-nya. Dengan metode <i>download()</i>, file Excel akan langsung diunduh oleh browser dengan nama 'beneficiary.xlsx'. Pendekatan ini sangat efisien dalam menyediakan data untuk analisis eksternal, pelaporan, atau backup data. Class BeneficiaryExport sendiri bertanggung jawab untuk mengatur format dan struktur data dalam file Excel yang dihasilkan, memastikan bahwa data yang diekspor terstruktur dengan baik dan mudah dibaca.
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
    return redirect()->route('beneficiary.index')->with('success', 'Data berhasil diimport!');</code></pre>
                </div>
                <p class="mb-4">
                    <b>importExcel(Request $request)</b> adalah fungsi yang memudahkan pengguna untuk menambahkan banyak data penerima bantuan sekaligus melalui file Excel. Fungsi ini dimulai dengan validasi file yang diunggah, memastikan bahwa file tersebut ada (required) dan merupakan file Excel dengan ekstensi xlsx atau xls. Validasi ini penting untuk mencegah pengunggahan file yang tidak valid atau potensial berbahaya. Setelah validasi berhasil, fungsi menggunakan paket Laravel Excel dengan metode <i>import()</i> untuk memproses file, dengan membuat instance dari class BeneficiaryImport yang bertanggung jawab untuk membaca dan memproses data dari file Excel. Class BeneficiaryImport biasanya mengimplementasikan logika untuk memvalidasi, memetakan, dan menyimpan data dari file Excel ke dalam database. Setelah proses import selesai, pengguna diarahkan kembali ke halaman daftar penerima dengan pesan sukses. Fitur impor ini sangat berharga dalam situasi di mana data perlu dimigrasikan dari sumber lain atau saat perlu menambahkan banyak data sekaligus, menghemat waktu dan mengurangi potensi kesalahan input manual.
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
    return redirect()->route('beneficiary.index')->with('success', 'Tidak ada data yang dipilih.');</code></pre>
                </div>
                <p class="mb-4">
                    <b>bulkDelete(Request $request)</b> adalah fungsi yang dirancang untuk menghapus banyak data penerima bantuan sekaligus, baik seluruh data maupun data terpilih. Fungsi ini menangani dua skenario utama: (1) Penghapusan semua data, yang ditandai dengan parameter <i>select_all</i> bernilai 1, dan (2) Penghapusan data tertentu berdasarkan ID yang dipilih. Untuk skenario pertama, fungsi menggunakan Eloquent Query Builder dengan metode <i>query()->delete()</i> untuk menghapus semua record dari tabel beneficiaries. Untuk skenario kedua, fungsi mengambil array ID dari request, memverifikasi bahwa array tersebut tidak kosong, kemudian menggunakan metode <i>whereIn('id', $ids)->delete()</i> untuk menghapus hanya record dengan ID yang dipilih. Jika tidak ada ID yang dipilih, fungsi mengembalikan pesan yang sesuai. Pendekatan ini memberikan fleksibilitas dalam manajemen data massal, memungkinkan pengguna untuk melakukan operasi batch yang efisien tanpa harus menghapus data satu per satu. Perlu dicatat bahwa fungsi ini hanya menghapus data utama penerima, tidak secara otomatis menghapus data terkait seperti pada fungsi destroy(), sehingga sebaiknya digunakan dengan hati-hati untuk mencegah orphaned records.
                </p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">3. StatisticController</h3>
            <p class="mb-4">
                <b>StatisticController</b> merupakan inti dari aplikasi K-Means Clustering yang bertanggung jawab atas seluruh proses analisis statistik dan implementasi algoritma clustering. Controller ini menjadi pusat logika bisnis yang menghubungkan data penerima bantuan dengan proses analisis berbasis machine learning, mulai dari visualisasi data, perhitungan cluster, hingga evaluasi kualitas hasil clustering. Dengan adanya StatisticController, aplikasi mampu memberikan insight yang mendalam dan berbasis data kepada pengguna, sehingga mendukung pengambilan keputusan yang lebih objektif dan terukur. Berikut penjelasan detail setiap fungsi utama dalam StatisticController:
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
                <p class="mb-4">
                    <b>index()</b> adalah fungsi yang bertugas untuk menampilkan halaman utama statistik, yang berisi visualisasi hasil clustering dan form untuk memulai proses clustering. Fungsi ini memeriksa kelengkapan data, memuat hasil clustering jika sudah ada, dan menyiapkan berbagai data statistik seperti distribusi cluster, scatter plot, serta statistik deskriptif untuk setiap cluster. Dengan demikian, fungsi ini menjadi pintu masuk utama bagi pengguna untuk memahami pola dan distribusi data penerima bantuan secara visual dan analitis.
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
                <p class="mb-4">
                    <b>recalculate(Request $request)</b> adalah fungsi yang memungkinkan pengguna untuk menjalankan ulang proses clustering dengan parameter yang berbeda, seperti jumlah cluster dan metode normalisasi. Fungsi ini sangat penting untuk eksperimen dan analisis sensitivitas, di mana pengguna dapat membandingkan hasil clustering dengan berbagai konfigurasi. Dengan validasi input yang ketat dan penghapusan hasil clustering lama sebelum perhitungan ulang, fungsi ini memastikan bahwa hasil analisis selalu akurat dan relevan dengan parameter yang dipilih.
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
                <p class="mb-4">
                    <b>showCluster($cluster, Request $request)</b> adalah fungsi yang menampilkan detail lengkap untuk cluster tertentu, termasuk daftar anggota cluster dan statistik deskriptifnya. Fungsi ini sangat berguna untuk analisis mendalam terhadap karakteristik masing-masing cluster, seperti nilai minimum, maksimum, rata-rata, median, dan standar deviasi setiap fitur. Dengan fitur pencarian dan paginasi, pengguna dapat dengan mudah menelusuri data dalam cluster yang besar. Fungsi ini juga menampilkan skor silhouette untuk mengevaluasi kualitas clustering pada cluster tersebut.
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
                <p class="mb-4">
                    <b>doClustering(Request $request, $successMessage = null)</b> adalah fungsi utama yang mengimplementasikan algoritma K-Means pada data penerima bantuan. Fungsi ini melakukan validasi input, menyiapkan dan menormalisasi data, menjalankan proses clustering menggunakan library machine learning, serta menyimpan hasil cluster dan skor silhouette ke database. Proses ini sangat penting untuk menghasilkan segmentasi data yang objektif dan dapat dipertanggungjawabkan secara ilmiah. Dengan adanya fungsi ini, aplikasi mampu memberikan hasil clustering yang siap digunakan untuk analisis lanjutan dan pengambilan keputusan.
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
                <p class="mb-4">
                    <b>calculateSilhouetteScores(array $samples, array $labels)</b> adalah fungsi privat yang menghitung skor silhouette untuk setiap data point dalam hasil clustering. Skor silhouette merupakan indikator seberapa baik suatu data cocok dengan cluster-nya dibandingkan dengan cluster lain. Nilai ini sangat penting dalam evaluasi kualitas clustering, karena dapat digunakan untuk mengidentifikasi cluster yang terlalu tumpang tindih atau data yang salah klasifikasi. Dengan perhitungan yang sistematis, fungsi ini membantu memastikan bahwa hasil clustering yang dihasilkan benar-benar optimal dan dapat diandalkan.
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
                <p class="mb-4">
                    <b>calculateAverageDistance(array $point, array $points)</b> adalah fungsi pembantu yang digunakan untuk menghitung jarak rata-rata antara satu titik data dengan kumpulan titik data lainnya. Fungsi ini sangat penting dalam perhitungan skor silhouette, karena menentukan seberapa dekat suatu data dengan cluster-nya sendiri maupun dengan cluster lain. Dengan perhitungan jarak yang akurat, evaluasi kualitas clustering menjadi lebih objektif dan terukur.
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
                <p class="mb-4">
                    <b>euclideanDistance(array $point1, array $point2)</b> adalah fungsi yang menghitung jarak Euclidean antara dua titik data dalam ruang multi-dimensi. Jarak Euclidean merupakan metrik yang paling umum digunakan dalam algoritma clustering, termasuk K-Means, karena memberikan gambaran seberapa mirip atau berbeda dua data. Dengan perhitungan jarak yang tepat, proses clustering dapat menghasilkan pembagian cluster yang lebih akurat dan bermakna.
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
                <p class="mb-4">
                    <b>normalizeData($data, array $usiaValues, array $anakValues, array $rumahValues, array $pendapatanValues)</b> adalah fungsi yang menormalisasi data sebelum dilakukan proses clustering. Normalisasi sangat penting untuk memastikan bahwa setiap fitur memiliki skala yang sebanding, sehingga tidak ada fitur yang mendominasi proses clustering hanya karena perbedaan skala. Dengan menggunakan metode normalisasi yang tepat, hasil clustering menjadi lebih adil, akurat, dan representatif terhadap karakteristik data sebenarnya.
                </p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">4. DecisionController</h3>
            <p class="mb-4">
                <b>DecisionController</b> adalah controller yang bertanggung jawab untuk mengelola proses pengambilan keputusan berbasis hasil clustering dalam distribusi bantuan. Controller ini menjadi penghubung antara hasil analisis data (clustering) dengan aksi nyata berupa penentuan penerima bantuan yang diprioritaskan. Dengan adanya DecisionController, aplikasi mampu menerjemahkan hasil analisis data menjadi kebijakan distribusi yang lebih adil, objektif, dan transparan. Berikut penjelasan detail setiap fungsi utama dalam DecisionController:
            </p>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">index()</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function index()
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>index()</b> adalah fungsi yang menampilkan halaman utama panel keputusan, berisi informasi distribusi cluster, statistik cluster, dan daftar keputusan yang telah dibuat sebelumnya. Fungsi ini sangat penting untuk memberikan gambaran menyeluruh kepada pengguna mengenai hasil clustering dan prioritas distribusi bantuan. Dengan menampilkan data statistik dan peringkat kebutuhan setiap cluster, pengguna dapat mengambil keputusan distribusi bantuan secara lebih terukur dan berbasis data.
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
                    <b>create()</b> adalah fungsi yang menampilkan formulir pembuatan keputusan distribusi bantuan baru. Fungsi ini memeriksa ketersediaan data hasil clustering, menghitung prioritas cluster, dan menyiapkan data yang dibutuhkan untuk membantu pengguna dalam memilih cluster yang tepat. Dengan adanya fitur ini, proses pengambilan keputusan menjadi lebih sistematis, transparan, dan dapat dipertanggungjawabkan secara ilmiah.
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
                    <b>store(Request $request)</b> adalah fungsi yang memproses pembuatan keputusan distribusi bantuan berdasarkan input pengguna. Fungsi ini melakukan validasi input, menentukan penerima bantuan berdasarkan prioritas cluster atau cluster tertentu, dan menyimpan hasil keputusan ke database. Proses ini dijalankan dalam transaksi database untuk menjaga integritas data. Dengan adanya fungsi ini, aplikasi memastikan bahwa setiap keputusan distribusi bantuan didasarkan pada data yang valid, logika prioritas yang jelas, dan dapat dilacak riwayatnya.
                </p>
            </div>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2">show($id)</h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">public function show($id)
{
    // ... kode ...
}</code></pre>
                </div>
                <p class="mb-4">
                    <b>show($id)</b> adalah fungsi yang menampilkan detail lengkap dari keputusan distribusi bantuan yang telah dibuat. Fungsi ini mengambil data keputusan beserta daftar penerima bantuan yang terpilih, dan menampilkannya secara informatif kepada pengguna. Dengan fitur ini, pengguna dapat menelusuri riwayat keputusan, memverifikasi penerima bantuan, dan memastikan transparansi dalam proses distribusi.
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
                    <b>destroy($id)</b> adalah fungsi yang bertanggung jawab untuk menghapus keputusan distribusi bantuan yang tidak diperlukan lagi. Fungsi ini memastikan bahwa data keputusan dan seluruh item terkait dihapus secara konsisten dari database. Dengan adanya fitur ini, aplikasi mendukung pengelolaan riwayat keputusan yang lebih rapi dan mencegah penumpukan data yang tidak relevan.
                </p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">5. ProfileController</h3>
            <p class="mb-4">
                <b>ProfileController</b> adalah controller yang mengelola seluruh fitur terkait pengelolaan profil pengguna dalam aplikasi. Controller ini memastikan bahwa setiap pengguna dapat memperbarui data pribadinya, mengganti password, serta mengunggah foto profil (avatar) dengan aman dan mudah. Dengan adanya ProfileController, aplikasi memberikan keleluasaan dan kontrol penuh kepada pengguna atas data pribadinya, sekaligus menjaga keamanan dan kenyamanan dalam penggunaan aplikasi. Berikut penjelasan detail setiap fungsi utama dalam ProfileController:
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
                <p class="mb-4">
                    <b>edit()</b> adalah fungsi yang menampilkan halaman edit profil pengguna yang sedang login. Fungsi ini mengambil data pengguna saat ini dan menampilkannya dalam form yang dapat diedit. Dengan fitur ini, pengguna dapat dengan mudah memperbarui informasi pribadi seperti nama dan email, sehingga data yang tersimpan di aplikasi selalu akurat dan up-to-date.
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
                <p class="mb-4">
                    <b>update(Request $request)</b> adalah fungsi yang memproses permintaan pembaruan data profil pengguna. Fungsi ini melakukan validasi data yang diinputkan, memastikan nama dan email yang baru sudah sesuai standar dan tidak duplikat. Setelah validasi berhasil, data pengguna akan diperbarui di database. Fitur ini sangat penting untuk menjaga integritas data pengguna dan memberikan pengalaman personalisasi yang lebih baik dalam aplikasi.
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
                <p class="mb-4">
                    <b>updatePassword(Request $request)</b> adalah fungsi yang menangani proses penggantian password pengguna. Fungsi ini memvalidasi password lama dan password baru, memastikan keamanan dan kerahasiaan akun pengguna. Dengan adanya fitur ini, pengguna dapat secara mandiri menjaga keamanan akunnya, serta mencegah akses tidak sah akibat kebocoran password lama.
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
                <p class="mb-4">
                    <b>updateAvatar(Request $request)</b> adalah fungsi yang memproses pengunggahan dan pembaruan foto profil (avatar) pengguna. Fungsi ini memvalidasi file gambar yang diunggah, menghapus avatar lama jika ada, dan menyimpan avatar baru ke storage aplikasi. Dengan fitur ini, pengguna dapat memperbarui identitas visualnya di aplikasi, sehingga pengalaman penggunaan menjadi lebih personal dan menyenangkan.
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