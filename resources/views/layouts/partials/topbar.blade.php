<header
    class="h-20 bg-white border-b border-slate-50 shadow-sm flex items-center justify-end px-8 sticky top-0 z-20"
>

    <div
        class="flex items-center gap-5"
    >

        <button
            class="text-slate-500 hover:text-indigo-600"
        >

            <i class="ri-notification-3-line text-xl"></i>

        </button>

        <div
            class="w-px h-8 bg-slate-200"
        ></div>

        <div
            class="flex items-center gap-3"
        >

            <div
                class="w-10 h-10 rounded-full
                bg-indigo-100
                flex items-center justify-center"
            >

                <i class="ri-user-3-line text-indigo-600"></i>

            </div>

            <div>

                <div
                    class="font-semibold text-slate-800"
                >

                    {{ Auth::user()->name }}

                </div>

                <div
                    class="text-xs text-slate-500"
                >

                    {{ Auth::user()->role }}

                </div>

            </div>

        </div>

    </div>

</header>