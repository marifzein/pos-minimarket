@extends('layouts.app')

@section('title', 'Buat Stock Adjustment')

@section('content')

<x-page-header
    title="Buat Stock Adjustment"
    subtitle="Input penyesuaian barang hilang, rusak, atau expired"
>
    <x-slot:action>
        <a href="{{ route('stock-adjustments.index') }}">
            <x-button color="gray" type="button">
                <i class="ri-arrow-left-line"></i>
                Kembali
            </x-button>
        </a>
    </x-slot:action>
</x-page-header>

{{-- Tampilkan Notifikasi Error Validasi Jika Ada --}}
@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
        <ul class="list-disc pl-5 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('stock-adjustments.store') }}" onsubmit="return validateForm()">
    @csrf

    {{-- Card Informasi Dokumen --}}
    <x-card>
        <div class="grid md:grid-cols-3 gap-6">
            <x-input
                label="Nomor SA"
                name="nomor_sa"
                readonly
                :value="$nomor_sa"
                icon="ri-file-list-3-line"
            />

            <x-input
                label="Tanggal Penyesuaian"
                name="tgl_sa"
                type="date"
                :value="date('Y-m-d')"
                required
            />

            <x-input
                label="Catatan Umum Dokumen"
                name="catatan"
                placeholder="Contoh: Pembuangan barang rusak rak depan"
                icon="ri-chat-4-line"
            />
        </div>
    </x-card>

    {{-- Layout Pencarian dan Tabel Item --}}
    <div class="grid lg:grid-cols-3 gap-6 mt-6">
        
        {{-- Sisi Kiri: Panel Pencarian Produk --}}
        <div class="lg:col-span-1">
            <x-card>
                <div class="font-semibold mb-4">Cari Produk Terganggu</div>
                
                <x-input
                    id="search-product"
                    name="search"
                    placeholder="Barcode / Kode / Nama Produk"
                    icon="ri-search-line"
                    autocomplete="off"
                />

                <div id="product-result" class="mt-4 rounded-xl border bg-white max-h-96 overflow-y-auto">
                    <div class="p-8 text-center text-slate-400">
                        Ketik nama / barcode produk minimarket
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Sisi Kanan: Daftar Item Penyesuaian --}}
        <div class="lg:col-span-2">
            <x-card>
                <div class="mb-4">
                    <h3 class="font-semibold">Daftar Item Dibuang / Dikurangi</h3>
                    <p class="text-sm text-slate-500">Kuantitas di bawah ini akan memotong stok di komputer</p>
                </div>

                <x-table>
                    <x-table-header>
                        <tr>
                            <x-table-head>Produk</x-table-head>
                            <x-table-head class="text-center w-32">Qty Dikurangi</x-table-head>
                            <x-table-head>Alasan / Keterangan Spesifik</x-table-head>
                            <x-table-head class="text-center w-20">Aksi</x-table-head>
                        </tr>
                    </x-table-header>

                    <tbody id="sa-items">
                        <tr>
                            <td colspan="4">
                                <x-empty-state
                                    icon="ri-delete-bin-5-line"
                                    title="Belum ada produk dipilih"
                                    description="Cari produk rusak/expired pada kolom pencarian di sebelah kiri."
                                />
                            </td>
                        </tr>
                    </tbody>
                </x-table>
            </x-card>
        </div>
    </div>

    {{-- Tombol Submit --}}
    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('stock-adjustments.index') }}">
            <x-button color="secondary" type="button" full>
                <i class="ri-close-circle-line text-red-500 text-base"></i>
                Batal
            </x-button>
        </a>

        <!-- value="draft" -> Masuk ke controller berstatus draft -->
        <x-button color="orange" type="submit" name="action" value="draft" full>
            <i class="ri-save-line"></i>
            Simpan Draft SA
        </x-button>

        <!-- value="closed" -> Masuk ke controller langsung diposting & potong stok -->
        <x-button color="green" type="submit" name="action" value="closed" full>
            <i class="ri-checkbox-circle-line"></i>
            Posting & Kunci Stok
        </x-button>
    </div>
</form>

@push('scripts')
<script>
const search = document.getElementById('search-product');
const result = document.getElementById('product-result');
const saItems = document.getElementById('sa-items');
let cart = [];
let timer;
let selectedIndex = -1; // Menyimpan indeks baris yang sedang aktif dipilih keyboard

// Fungsi membersihkan hasil pencarian & reset focus
function clearSearch() {
    result.innerHTML = `
        <div class="p-8 text-center text-slate-400">
            Ketik nama / barcode produk minimarket
        </div>
    `;
    search.value = '';
    selectedIndex = -1;
    search.focus();
}

// Menambahkan produk ke keranjang belanja (cart)
function addToCart(id, name, code) {
    const existing = cart.find(x => x.id == id);

    if (existing) {
        existing.qty++;
    } else {
        cart.push({
            id: id,
            name: name,
            code: code,
            qty: 1,
            notes: ''
        });
    }
    renderTable();
    clearSearch(); // Bersihkan dropdown dan fokuskan kembali ke input search
}

// Event Handler Pencarian Ajax dengan Debounce
search.addEventListener('keyup', function(e) {
    // Abaikan jika menekan tombol navigasi keyboard agar tidak mengacaukan request Ajax
    if (['ArrowUp', 'ArrowDown', 'Enter'].includes(e.key)) {
        return;
    }

    clearTimeout(timer);
    const q = this.value.trim();

    if (q.length < 2) {
        result.innerHTML = `
            <div class="p-8 text-center text-slate-400">
                Ketik minimal 2 karakter
            </div>
        `;
        selectedIndex = -1;
        return;
    }

    timer = setTimeout(() => {
        fetch(`/api/products/search?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                result.innerHTML = `
                    <div class="p-6 text-center text-slate-400">
                        Produk tidak ditemukan
                    </div>
                `;
                selectedIndex = -1;
                return;
            }

            let html = '';
            data.forEach((item, index) => {
                html += `
                <div class="border-b p-3 hover:bg-indigo-50 cursor-pointer product-row"
                     id="product-row-${index}"
                     data-id="${item.id}"
                     data-name="${item.nama_barang}"
                     data-code="${item.kode_barang}">
                    <div class="font-semibold product-title">${item.nama_barang}</div>
                    <div class="text-sm text-slate-500">${item.kode_barang} | Stok saat ini: ${item.stok}</div>
                </div>
                `;
            });
            result.innerHTML = html;
            selectedIndex = -1; // Reset index pilihan setiap kali ada hasil baru

            // Handler klik mouse (tetap dipertahankan)
            document.querySelectorAll('.product-row').forEach(row => {
                row.onclick = function() {
                    addToCart(this.dataset.id, this.dataset.name, this.dataset.code);
                }
            });
        });
    }, 300);
});

// Event Handler Navigasi Keyboard (KeyDown)
search.addEventListener('keydown', function(e) {
    const rows = document.querySelectorAll('.product-row');
    if (rows.length === 0) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault(); // Mencegah kursor text box lompat
        selectedIndex++;
        if (selectedIndex >= rows.length) selectedIndex = 0; // Loop ke atas lagi
        updateRowHighlight(rows);
    } 
    else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex--;
        if (selectedIndex < 0) selectedIndex = rows.length - 1; // Loop ke paling bawah
        updateRowHighlight(rows);
    } 
    else if (e.key === 'Enter') {
        e.preventDefault(); // Mencegah form ke-submit tidak sengaja
        if (selectedIndex >= 0 && selectedIndex < rows.length) {
            const activeRow = rows[selectedIndex];
            addToCart(activeRow.dataset.id, activeRow.dataset.name, activeRow.dataset.code);
        }
    }
});

// Fungsi memberikan efek visual highlight biru saat dipanah up/down
function updateRowHighlight(rows) {
    rows.forEach((row, index) => {
        if (index === selectedIndex) {
            row.classList.add('bg-indigo-100');
            row.scrollIntoView({ block: 'nearest' }); // Otomatis scroll mengikuti pilihan keyboard
        } else {
            row.classList.remove('bg-indigo-100');
        }
    });
}

// Render isi tabel item terpilih
function renderTable() {
    if (cart.length === 0) {
        saItems.innerHTML = `
        <tr>
            <td colspan="4">
                <div class="text-center py-10 text-slate-400">
                    Belum ada produk dipilih
                </div>
            </td>
        </tr>
        `;
        return;
    }

    let html = '';
    cart.forEach((item, index) => {
        html += `
        <tr>
            <td>
                <div class="font-semibold">${item.name}</div>
                <div class="text-xs text-slate-400">${item.code}</div>
                <input type="hidden" name="product_id[]" value="${item.id}">
            </td>
            <td class="text-center">
                <input type="number" name="qty[]" min="1" value="${item.qty}" data-index="${index}" class="qty border rounded w-24 px-2 py-1 text-center font-bold text-indigo-600">
            </td>
            <td>
                <input type="text" value="${item.notes}" data-index="${index}" placeholder="Alasan (Misal: Bocor, Expired)" class="item-notes border rounded w-full px-3 py-1 text-sm">
            </td>
            <td class="text-center">
                <button type="button" class="delete text-red-600 hover:text-red-800 p-1" data-index="${index}">
                    <i class="ri-delete-bin-line text-lg"></i>
                </button>
            </td>
        </tr>
        `;
    });
    saItems.innerHTML = html;
}

// Handler Perubahan Input Qty & Catatan secara langsung
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty')) {
        const index = e.target.dataset.index;
        cart[index].qty = parseInt(e.target.value) || 1;
    }
    if (e.target.classList.contains('item-notes')) {
        const index = e.target.dataset.index;
        cart[index].notes = e.target.value;
    }
});

// Validasi client-side agar tidak bisa submit data kosong
function validateForm() {
    if (cart.length === 0) {
        alert("Peringatan: Anda belum memilih produk apa pun untuk disesuaikan!");
        return false; // Membatalkan proses submit form
    }
    return true; // Mengizinkan form dikirim ke controller jika ada minimal 1 barang
}

// Handler Tombol Hapus Baris Item
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.delete');
    if (!btn) return;
    
    cart.splice(btn.dataset.index, 1);
    renderTable();
});
</script>
@endpush

@endsection