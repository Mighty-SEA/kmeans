<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('simpan_penerima') }}" method="POST">
        @csrf
        <input type="text" name="nami" placeholder="Nama">
        <input type="text" name="alamat" placeholder="Alamat">
        <input type="text" name="no_hp" placeholder="No HP">
        <input type="text" name="usia" placeholder="Usia">
        <input type="text" name="jumlah_anak" placeholder="Jumlah Anak">
        <input type="text" name="pendapatan_perbulan" placeholder="Pendapatan Perbulan">
        <input type="text" name="kelayakan_rumah" placeholder="Kelayakan Rumah">
        <button type="submit">Simpan</button>
    </form>
</body>
</html>