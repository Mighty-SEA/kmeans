@extends('Documentation.layout')

@section('title', 'View - Dokumentasi')
@section('header', 'View (Tampilan)')
@section('breadcrumb')
    <nav class="mb-4 text-sm text-blue-700 font-medium flex items-center space-x-2">
        <a href="{{ route('documentation.index') }}" class="hover:underline">Dokumentasi</a>
        <span>/</span>
        <span class="text-blue-900">View</span>
    </nav>
@endsection
@section('content')
    <div class="prose max-w-none">
        <h2 class="text-2xl font-bold mb-4">Penjelasan View dalam Aplikasi K-Means Clustering</h2>
        
        <p class="mb-4">
            View dalam arsitektur Laravel bertanggung jawab untuk menyajikan data kepada pengguna dalam format HTML. Pada aplikasi K-Means Clustering ini, view dibangun menggunakan Blade, templating engine bawaan Laravel, yang memungkinkan penulisan sintaks yang bersih dan penggunaan kembali komponen. View-view ini dirancang untuk menyediakan antarmuka pengguna yang interaktif dan informatif, mulai dari form input, tabel data, hingga visualisasi hasil clustering.
        </p>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">1. Layouts Utama</h3>
            <p class="mb-4">
                Layouts adalah kerangka dasar dari halaman-halaman aplikasi. Mereka mendefinisikan struktur umum seperti header, sidebar, dan footer, sehingga komponen-komponen ini tidak perlu ditulis berulang kali di setiap halaman.
            </p>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2"><code>layouts/app.blade.php</code></h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-html">&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;!-- Meta tags and CSS links --&gt;
    &lt;title&gt;@@yield('title', 'Aplikasi K-Means')&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;div class="flex h-screen bg-gray-100"&gt;
        &lt;!-- Sidebar --&gt;
        &lt;aside&gt;...&lt;/aside&gt;

        &lt;div class="flex-1 flex flex-col overflow-hidden"&gt;
            &lt;!-- Header --&gt;
            &lt;header&gt;...&lt;/header&gt;

            &lt;!-- Main Content --&gt;
            &lt;main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200"&gt;
                &lt;div class="container mx-auto px-6 py-8"&gt;
                    @@yield('content')
                &lt;/div&gt;
            &lt;/main&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
                </div>
                <p class="mb-4">
                    <b><code>app.blade.php</code></b> adalah layout utama untuk halaman-halaman yang memerlukan autentikasi (setelah pengguna login). Layout ini mencakup struktur dasar halaman seperti sidebar untuk navigasi, header yang berisi informasi pengguna, dan area konten utama. Halaman-halaman lain seperti Dashboard, Beneficiaries, dan Statistics akan "meng-extend" layout ini dan mengisi bagian <code>@@yield('content')</code> dengan konten spesifik mereka.
                </p>
            </div>
            
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2"><code>layouts/auth.blade.php</code></h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-html">&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;!-- Meta tags and CSS links --&gt;
&lt;/head&gt;
&lt;body class="bg-gray-100"&gt;
    &lt;div class="min-h-screen flex items-center justify-center"&gt;
        &lt;div class="max-w-md w-full p-6 bg-white rounded-lg shadow-lg"&gt;
            @@yield('content')
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
                </div>
                <p class="mb-4">
                    <b><code>auth.blade.php</code></b> adalah layout yang didedikasikan untuk halaman autentikasi seperti login dan registrasi. Tampilannya lebih sederhana, biasanya hanya berupa sebuah card di tengah layar untuk menampung form. Ini memisahkan tampilan untuk pengguna yang belum login dari antarmuka aplikasi utama.
                </p>
            </div>
        </div>
        
        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">2. Halaman Dashboard</h3>
            <p class="mb-4">
                Dashboard adalah halaman pertama yang dilihat pengguna setelah login. Halaman ini menyajikan ringkasan informasi penting dari seluruh aplikasi.
            </p>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2"><code>dashboard.blade.php</code></h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">@@extends('layouts.app')

@@section('content')
    &lt;h1&gt;Dashboard&lt;/h1&gt;
    
    &lt;!-- Cards for Total Beneficiaries, etc. --&gt;
    &lt;div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6"&gt;
        &lt;div class="bg-white p-6 rounded-lg shadow"&gt;
            &lt;h3&gt;Total Penerima&lt;/h3&gt;
            &lt;p&gt;@{{ $totalPenerima }}&lt;/p&gt;
        &lt;/div&gt;
        &lt;!-- ... more cards ... --&gt;
    &lt;/div&gt;

    &lt;!-- Chart for Cluster Distribution --&gt;
    &lt;div class="bg-white p-6 rounded-lg shadow mb-6"&gt;
        &lt;canvas id="clusterDistributionChart"&gt;&lt;/canvas&gt;
    &lt;/div&gt;
    
    &lt;!-- Table for Latest Beneficiaries --&gt;
    &lt;table&gt;...&lt;/table&gt;
@@endsection</code></pre>
                </div>
                <p class="mb-4">
                    View ini menampilkan ringkasan data dalam bentuk kartu (cards), grafik (charts), dan tabel. Data seperti total penerima, distribusi cluster, dan daftar penerima terbaru dikirim dari <code>BeneficiaryController::dashboard()</code> dan ditampilkan di sini. Halaman ini menggunakan Chart.js untuk merender grafik, memberikan visualisasi data yang interaktif kepada pengguna.
                </p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">3. Manajemen Penerima (Beneficiaries)</h3>
            <p class="mb-4">
                Serangkaian view ini digunakan untuk operasi CRUD (Create, Read, Update, Delete) pada data penerima bantuan.
            </p>
            <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2"><code>beneficiaries/index.blade.php</code></h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-html">&lt;!-- Search and Action Buttons --&gt;
&lt;div class="flex justify-between items-center mb-4"&gt;
    &lt;input type="search" name="search" placeholder="Cari..."&gt;
    &lt;a href="@{{ route('beneficiary.create') }}"&gt;Tambah Data&lt;/a&gt;
&lt;/div&gt;

&lt;!-- Beneficiaries Table --&gt;
&lt;table class="min-w-full bg-white"&gt;
    &lt;thead&gt;
        &lt;tr&gt;
            &lt;th&gt;NIK&lt;/th&gt;
            &lt;th&gt;Nama&lt;/th&gt;
            &lt;!-- ... other headers ... --&gt;
            &lt;th&gt;Aksi&lt;/th&gt;
        &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
        @@foreach($penerima as $item)
            &lt;tr&gt;
                &lt;td&gt;@{{ $item-&gt;nik }}&lt;/td&gt;
                &lt;td&gt;@{{ $item-&gt;nama }}&lt;/td&gt;
                &lt;!-- ... other data ... --&gt;
                &lt;td&gt;
                    &lt;a href="@{{ route('beneficiary.edit', $item-&gt;id) }}"&gt;Edit&lt;/a&gt;
                    &lt;form action="@{{ route('beneficiary.destroy', $item-&gt;id) }}" method="POST"&gt;
                        @@csrf
                        @@method('DELETE')
                        &lt;button type="submit"&gt;Hapus&lt;/button&gt;
                    &lt;/form&gt;
                &lt;/td&gt;
            &lt;/tr&gt;
        @@endforeach
    &lt;/tbody&gt;
&lt;/table&gt;

&lt;!-- Pagination --&gt;
@{{ $penerima-&gt;links() }}</code></pre>
                </div>
                <p class="mb-4">
                    <b><code>index.blade.php</code></b> adalah halaman utama untuk menampilkan daftar semua penerima bantuan dalam format tabel. View ini dilengkapi dengan fitur pencarian, tombol untuk menambah data baru, serta tombol aksi (Edit dan Hapus) untuk setiap baris data. Paginasi juga diimplementasikan menggunakan <code>@{{ $penerima-&gt;links() }}</code> untuk menangani data dalam jumlah besar.
                </p>
            </div>
             <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2"><code>beneficiaries/create.blade.php</code> dan <code>edit.blade.php</code></h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">&lt;form action="@{{ isset($penerima) ? route('beneficiary.update', $penerima-&gt;id) : route('beneficiary.store') }}" method="POST"&gt;
    @@csrf
    @@if(isset($penerima))
        @@method('PUT')
    @@endif

    &lt;div class="mb-4"&gt;
        &lt;label for="nik"&gt;NIK&lt;/label&gt;
        &lt;input type="text" id="nik" name="nik" value="@{{ old('nik', $penerima-&gt;nik ?? '') }}"&gt;
        @@error('nik') &lt;span class="text-red-500"&gt;@{{ $message }}&lt;/span&gt; @@enderror
    &lt;/div&gt;

    &lt;!-- ... other form fields ... --&gt;

    &lt;button type="submit"&gt;
        @{{ isset($penerima) ? 'Update' : 'Simpan' }}
    &lt;/button&gt;
&lt;/form&gt;</code></pre>
                </div>
                <p class="mb-4">
                    Kedua view ini berisi form untuk menambah atau mengedit data penerima. Seringkali, keduanya dapat digabungkan menjadi satu file komponen (partial) untuk mengurangi duplikasi kode. View ini menampilkan field input untuk setiap atribut penerima. Untuk form edit, nilai-nilai yang ada ditampilkan menggunakan <code>value="@{{ old('nama', $penerima-&gt;nama ?? '') }}"</code>, yang akan menampilkan data lama dari database, atau data input sebelumnya jika terjadi error validasi. Pesan error dari validasi controller ditampilkan di bawah setiap field.
                </p>
            </div>
        </div>

        <div class="mb-10">
            <h3 class="text-2xl font-bold mb-2">4. Statistik dan Clustering</h3>
            <p class="mb-4">
                View-view ini adalah inti dari fungsionalitas analisis, menampilkan hasil dari algoritma K-Means.
            </p>
             <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2"><code>statistics/statistics.blade.php</code></h4>
                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                    <pre><code class="language-php">&lt;!-- Form for Recalculating Cluster --&gt;
&lt;form action="@{{ route('statistic.recalculate') }}" method="POST"&gt;
    @@csrf
    &lt;label for="num_clusters"&gt;Jumlah Cluster:&lt;/label&gt;
    &lt;input type="number" name="num_clusters" value="@{{ $lastNumClusters }}"&gt;
    
    &lt;label for="normalization"&gt;Metode Normalisasi:&lt;/label&gt;
    &lt;select name="normalization"&gt;
        &lt;option value="robust" @{{ $lastNormalization == 'robust' ? 'selected' : '' }}&gt;Robust&lt;/option&gt;
        &lt;!-- ... other options ... --&gt;
    &lt;/select&gt;

    &lt;button type="submit"&gt;Hitung Ulang&lt;/button&gt;
&lt;/form&gt;

&lt;!-- Scatter Plot Chart --&gt;
&lt;div&gt;
    &lt;canvas id="scatter-plot"&gt;&lt;/canvas&gt;
&lt;/div&gt;

&lt;!-- Cluster Information Cards --&gt;
@@foreach($clusters as $cluster)
    &lt;div class="card"&gt;
        &lt;h3&gt;Cluster @{{ $cluster-&gt;id }}&lt;/h3&gt;
        &lt;p&gt;Jumlah Anggota: @{{ $cluster-&gt;count }}&lt;/p&gt;
        &lt;a href="@{{ route('statistic.cluster', $cluster-&gt;id) }}"&gt;Lihat Detail&lt;/a&gt;
    &lt;/div&gt;
@@endforeach</code></pre>
                </div>
                <p class="mb-4">
                    Halaman ini adalah pusat kontrol untuk proses clustering. Pengguna dapat mengatur parameter seperti jumlah cluster dan metode normalisasi, lalu menjalankan ulang perhitungan. Hasilnya divisualisasikan dalam bentuk <b>Scatter Plot</b> yang menampilkan sebaran data dalam cluster-cluster yang berbeda. Selain itu, ringkasan informasi untuk setiap cluster (seperti jumlah anggota) ditampilkan dalam bentuk kartu, dengan tautan untuk melihat detail lebih lanjut.
                </p>
            </div>
             <div class="mb-6">
                <h4 class="text-xl font-semibold mb-2"><code>statistics/cluster_detail.blade.php</code></h4>
                <p class="mb-4">
                    Saat pengguna mengklik "Lihat Detail" pada salah satu cluster di halaman statistik, mereka akan diarahkan ke view ini. <b><code>cluster_detail.blade.php</code></b> menampilkan daftar semua anggota dari cluster yang dipilih dalam sebuah tabel, lengkap dengan data asli dan data yang sudah dinormalisasi. Selain itu, view ini juga menyajikan statistik deskriptif (rata-rata, median, min, max, std dev) untuk setiap fitur dalam cluster tersebut, memberikan wawasan mendalam tentang karakteristik cluster.
                </p>
            </div>
        </div>

        <h2 class="text-2xl font-bold mb-4">Kesimpulan</h2>
        <p>
            Struktur view dalam aplikasi ini dirancang secara modular dan dapat digunakan kembali (reusable) berkat Blade templating. Pemisahan antara layout, halaman utama, form, dan halaman detail memungkinkan pengembangan dan pemeliharaan yang lebih mudah. Penggunaan komponen seperti tabel, form, dan grafik secara konsisten di seluruh aplikasi memberikan pengalaman pengguna yang baik dan intuitif. Setiap view menerima data dari controller yang sesuai dan hanya berfokus pada penyajian data tersebut, sesuai dengan prinsip arsitektur MVC.
        </p>
    </div>
@endsection 