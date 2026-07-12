<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Penerimaan Barang - {{ $penerimaan->no_penerimaan }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        /* Info Area */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }
        .info-table td {
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .info-table td:first-child {
            border-right: 1px solid #000;
        }
        
        /* Items Table dengan Garis Kotak Penuh */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            border: 1px solid #000;
            padding: 6px;
            font-size: 9.5pt;
            font-weight: bold;
            background-color: #fff;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 9.5pt;
        }
        
        /* Bagian Total */
        .total-row td {
            border: none !important;
            padding: 6px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div style="width: 100%;">
        <!-- Header Dokumen -->
        <table class="header-table">
            <tr>
                <td>
                    <h2 style="margin:0;">POS MINIMARKET</h2>
                    <p style="margin:0; font-size:9pt;">Jl. Raya Utama No. 123, Kota Administrasi</p>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <h2 style="margin:0;">BUKTI PENERIMAAN BARANG</h2>
                    <p style="margin:0;"><strong>{{ $penerimaan->no_penerimaan }}</strong></p>
                </td>
            </tr>
        </table>

        <hr style="border: 0; border-top: 1px solid #000; margin-bottom: 20px;">

        <!-- Informasi Supplier & Penerimaan -->
        <table class="info-table">
            <tr>
                <td>
                    <strong>INFO SUPPLIER:</strong><br>
                    {{ $penerimaan->supplier_name }}<br>
                    No. Dokumen / Nota Supplier: {{ $penerimaan->no_dokumen_supplier ?? '-' }}
                </td>
                <td>
                    <strong>DETAIL PENERIMAAN:</strong><br>
                    No. Rujukan PO: {{ $penerimaan->no_po ?? 'Penerimaan Bebas (Non-PO)' }}<br>
                    Tanggal Terima: {{ \Carbon\Carbon::parse($penerimaan->tanggal_terima)->format('d-m-Y') }}<br>
                    Petugas Penerima: {{ $penerimaan->kasir_name }}
                </td>
            </tr>
        </table>

        <!-- Daftar Item Pesanan (Garis Kotak Sempurna) -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="text-align: center; width: 5%;">No</th>
                    <th style="text-align: left;">Nama Barang</th>
                    <th style="text-align: center; width: 12%;">Qty PO</th>
                    <th style="text-align: center; width: 12%;">Qty Terima</th>
                    <th style="text-align: right; width: 18%;">Harga Beli</th>
                    <th style="text-align: right; width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($items as $index => $item)
                    @php 
                        $subtotal = $item->qty_terima * $item->harga_beli; 
                        $grandTotal += $subtotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ $item->nama_barang }}<br>
                            <small style="color: #555;">{{ $item->kode_barang }}</small>
                            @if($item->qty_po == 0)
                                <span style="font-size: 8pt; font-weight: bold; color: #7c3aed;">(Item Luar PO)</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->qty_po }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ $item->qty_terima }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                
                <!-- Baris Total Bersih Tanpa Border Kotak Tabel Utama -->
                <tr class="total-row">
                    <td colspan="5" class="text-right">GRAND TOTAL NOTA</td>
                    <td class="text-right" style="border: 1px double #000 !important;">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Catatan Opsional -->
        @if($penerimaan->catatan)
            <div style="font-size: 9pt; margin-bottom: 40px;">
                <strong>Catatan Penerimaan:</strong> {{ $penerimaan->catatan }}
            </div>
        @endif

        <!-- Tanda Tangan -->
        <table style="width: 100%; margin-top: 50px; text-align: center;">
            <tr>
                <td>
                    <p>Diterima Oleh,</p>
                    <div style="height: 60px;"></div>
                    <p><strong>{{ $penerimaan->kasir_name }}</strong></p>
                    <p style="font-size: 8pt; color: #555;">Gudang / Logistik Staff</p>
                </td>
                <td>
                    <p>Diserahkan Oleh,</p>
                    <div style="height: 60px;"></div>
                    <p>__________________</p>
                    <p style="font-size: 8pt; color: #555;">Driver / Sales Supplier</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>