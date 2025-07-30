# Blackbox Testing

## Halaman Dashboard (/)

| No. | Skenario                                      | Input                                                                 | Output yang Diharapkan                                              | Hasil |
|-----|-----------------------------------------------|-----------------------------------------------------------------------|---------------------------------------------------------------------|-------|
| 1   | Akses dashboard utama                        | GET / (user login)                                                  | Status 200 (halaman dashboard tampil, statistik, grafik, tabel data terbaru) |       |
| 2   | Lihat ringkasan total penerima               | -                                                                     | Total penerima tampil sesuai data                                   |       |
| 3   | Lihat distribusi cluster                     | -                                                                     | Jumlah cluster 1, 2, 3 tampil sesuai data                           |       |
| 4   | Lihat grafik proporsi cluster                | -                                                                     | Grafik pie tampil sesuai data cluster                                |       |
| 5   | Lihat grafik rata-rata fitur per cluster     | -                                                                     | Grafik bar tampil sesuai data cluster                                |       |
| 6   | Lihat tabel data penerima terbaru            | -                                                                     | Tabel menampilkan 5 data penerima terbaru beserta cluster           |       |
| 7   | Klik "Lihat Semua" pada data terbaru        | Klik link/button                                                     | Redirect ke halaman /beneficiary                                    |       |

## Halaman Data Penerima (/beneficiary)

| No. | Skenario                                      | Input                                                                 | Output yang Diharapkan                                              | Hasil |
|-----|-----------------------------------------------|-----------------------------------------------------------------------|---------------------------------------------------------------------|-------|
| 1   | Akses halaman data penerima                  | GET /beneficiary (user login)                                        | Status 200 (tabel data penerima tampil)                             |       |
| 2   | Cari data penerima                           | Input kata kunci di form pencarian                                   | Tabel menampilkan hasil pencarian                                   |       |
| 3   | Reset pencarian                              | Klik tombol reset                                                    | Tabel menampilkan semua data                                        |       |
| 4   | Ubah jumlah data per halaman                 | Pilih jumlah data (10/20/30/50)                                      | Tabel menampilkan sesuai jumlah per halaman                         |       |
| 5   | Klik "Tambah Penerima"                      | Klik tombol/link                                                     | Redirect ke halaman /beneficiary/create                             |       |
| 6   | Klik "Edit" pada data                       | Klik tombol edit pada baris data                                     | Redirect ke halaman /beneficiary/{id}/edit                          |       |
| 7   | Klik "Hapus" pada data                      | Klik tombol hapus pada baris data, konfirmasi                        | Data terhapus, reload ke /beneficiary, pesan sukses                 |       |
| 8   | Bulk delete data                             | Pilih beberapa data, klik "Hapus Terpilih", konfirmasi              | Data terpilih terhapus, reload ke /beneficiary, pesan sukses        |       |
| 9   | Bulk delete semua data                       | Klik "Pilih Semua Data", klik "Hapus Terpilih", konfirmasi         | Semua data terhapus, reload ke /beneficiary, pesan sukses           |       |
| 10  | Export data ke Excel                         | Klik tombol Export, pilih kolom, submit                              | File Excel terunduh, data sesuai                                    |       |
| 11  | Import data dari Excel                       | Klik tombol Import, upload file valid, submit                        | Data bertambah, reload ke /beneficiary, pesan sukses                |       |

## Halaman Tambah Penerima (/beneficiary/create)

| No. | Skenario                                      | Input                                                                 | Output yang Diharapkan                                              | Hasil |
|-----|-----------------------------------------------|-----------------------------------------------------------------------|---------------------------------------------------------------------|-------|
| 1   | Akses halaman tambah penerima                | GET /beneficiary/create (user login)                                 | Status 200 (form tambah tampil)                                     |       |
| 2   | Submit form dengan data valid                | Isi semua field, submit                                              | Redirect ke /beneficiary, data tersimpan, pesan sukses              |       |
| 3   | Submit form dengan data tidak valid          | Kosongkan/isi field tidak valid, submit                              | Validasi error, tetap di halaman, pesan error tampil                |       |
| 4   | Klik "Batal"                                | Klik tombol batal                                                    | Redirect ke /beneficiary                                            |       |

## Halaman Edit Penerima (/beneficiary/{id}/edit)

| No. | Skenario                                      | Input                                                                 | Output yang Diharapkan                                              | Hasil |
|-----|-----------------------------------------------|-----------------------------------------------------------------------|---------------------------------------------------------------------|-------|
| 1   | Akses halaman edit penerima                  | GET /beneficiary/{id}/edit (user login)                              | Status 200 (form edit tampil)                                       |       |
| 2   | Submit form dengan data valid                | Ubah field, submit                                                   | Redirect ke /beneficiary, data terupdate, pesan sukses              |       |
| 3   | Submit form dengan data tidak valid          | Kosongkan/isi field tidak valid, submit                              | Validasi error, tetap di halaman, pesan error tampil                |       |
| 4   | Klik "Batal"                                | Klik tombol batal                                                    | Redirect ke /beneficiary                                            |       | 