<!DOCTYPE html>
<html>
<head>
    <title>Data Keluhan Pelanggan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Data Keluhan Pelanggan</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Nomor HP</th>
                <th>Status Keluhan</th>
                <th>Keluhan</th>
                <th>Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($keluhan as $item)
                <tr>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->nomor_hp }}</td>
                    <td>{{ $item->status_keluhan }}</td>
                    <td>{{ $item->keluhan }}</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
