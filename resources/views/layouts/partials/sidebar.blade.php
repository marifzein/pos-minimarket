{{-- <aside
class="w-64 bg-white border-r border-zinc-100 shadow-lg z-30 flex flex-col"> --}}
<aside
class="w-64 shadow-lg z-30 flex flex-col">

    {{-- Logo --}}
    <div class="px-6 py-6 ">

        <div class="flex items-center gap-3">

            <div
                class="w-11 h-11 rounded-xl
                bg-indigo-600
                text-white
                flex items-center justify-center">

                <i class="ri-shopping-cart-2-line text-2xl"></i>

            </div>

            <div>

                <h1 class="font-bold text-lg">

                    POS Minimarket

                </h1>

                <p class="text-xs text-slate-500">

                    Sistem Penjualan

                </p>

            </div>

        </div>

    </div>

    {{-- Menu --}}
    <nav class="flex-1 py-5 overflow-hidden">

        {{-- Dashboard --}}
        {{-- <a
            class="
            mx-3 mb-1 flex items-center gap-3 rounded-xl px-4 py-3 transition
            submenu {{ request()->is('dashboard*') ? 'submenu-active' : '' }}"
            href="/dashboard">
            <i class="ri-home-5-line text-lg"></i>
            Dashboard
        </a> --}}
        <a href="/dashboard"
        class="menu-parent menu-group w-full flex items-center gap-3 px-4 py-3
        {{ request()->is('dashboard*') ? 'submenu-active' : '' }}">
            <i class="ri-home-5-line"></i>
            <span>Dashboard</span>
        </a>

        {{-- ===================== --}}
        {{-- KASIR --}}
        {{-- ===================== --}}

        <button
            type="button"
            onclick="toggleMenu('kasir')"
            class="menu-parent menu-group w-full px-4 py-3 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <i class="ri-shopping-cart-2-line"></i>

                <span>Kasir</span>

            </div>

            <i id="icon-kasir"
                class="ri-arrow-right-s-line transition-all"></i>

        </button>

        <div id="menu-kasir" class="menu-content">

            <a href="/pos"
                class="submenu {{ request()->is('pos*') ? 'submenu-active' : '' }}">
                POS
            </a>

        </div>



        {{-- ===================== --}}
        {{-- MASTER DATA --}}
        {{-- ===================== --}}

        <button
            type="button"
            onclick="toggleMenu('master')"
            class="menu-parent menu-group w-full px-4 py-3 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <i class="ri-database-2-line"></i>

                <span>Master Data</span>

            </div>

            <i id="icon-master"
                class="ri-arrow-right-s-line transition-all"></i>

        </button>

        <div id="menu-master" class="menu-content">

            <a href="/products"
                class="submenu
                {{
                request()->is('products*')
                &&
                !request()->is('products/import*')
                ? 'submenu-active':''
                }}">
                Produk
            </a>

            <a href="/products/import"
                class="submenu
                {{
                request()->is('products/import*')
                ? 'submenu-active':''
                }}">
                Import Produk
            </a>

            <a href="/categories"
                class="submenu {{ request()->is('categories*') ? 'submenu-active':'' }}">
                Kategori
            </a>

            <a href="/suppliers"
                class="submenu {{ request()->is('suppliers*') ? 'submenu-active':'' }}">
                Supplier
            </a>

            <a href="/customers"
                class="submenu {{ request()->is('customers*') ? 'submenu-active':'' }}">
                Pelanggan
            </a>

        </div>



        {{-- ===================== --}}
        {{-- INVENTORY --}}
        {{-- ===================== --}}

        <button
            type="button"
            onclick="toggleMenu('inventory')"
            class="menu-parent menu-group w-full px-4 py-3 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <i class="ri-archive-line"></i>

                <span>Inventory</span>

            </div>

            <i id="icon-inventory"
                class="ri-arrow-right-s-line transition-all"></i>

        </button>

        <div id="menu-inventory" class="menu-content">

            <a href="/purchasing"
                class="submenu {{ request()->is('purchasing*') ? 'submenu-active':'' }}">
                Purchasing
            </a>

            <a href="/stock-opname"
                class="submenu {{ request()->is('stock-opname*') ? 'submenu-active':'' }}">
                Stock Opname
            </a>

        </div>



        {{-- ===================== --}}
        {{-- PENJUALAN --}}
        {{-- ===================== --}}

        <button
            type="button"
            onclick="toggleMenu('penjualan')"
            class="menu-parent menu-group w-full px-4 py-3 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <i class="ri-file-list-3-line"></i>

                <span>Penjualan</span>

            </div>

            <i id="icon-penjualan"
                class="ri-arrow-right-s-line transition-all"></i>

        </button>

        <div id="menu-penjualan" class="menu-content">

            <a href="/transactions"
                class="submenu {{ request()->is('transactions*') ? 'submenu-active':'' }}">
                Transaksi
            </a>

        </div>



        {{-- ===================== --}}
        {{-- SISTEM --}}
        {{-- ===================== --}}

        <button
            type="button"
            onclick="toggleMenu('system')"
            class="menu-parent menu-group w-full px-4 py-3 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <i class="ri-settings-3-line"></i>

                <span>Sistem</span>

            </div>

            <i id="icon-system"
                class="ri-arrow-right-s-line transition-all"></i>

        </button>

        <div id="menu-system" class="menu-content">

            <a href="/users"
                class="submenu {{ request()->is('users*') ? 'submenu-active':'' }}">
                User
            </a>

            <a href="/developer"
                class="submenu {{ request()->is('developer*') ? 'submenu-active':'' }}">
                Developer
            </a>

        </div>

    </nav>


{{-- Logout --}}
    <div class="sidebar-footer p-4">

        <form
            method="POST"
            action="{{ route('logout') }}">

            @csrf

            <button
                class="logout-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl transition">

                <i class="ri-logout-box-r-line"></i>

                Logout

            </button>

        </form>

    </div>

</aside>