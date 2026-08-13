<!--
    Global toast bus. Fire from anywhere with:
    $dispatch('toast', { type: 'success' | 'error', message: 'Text here' })
-->
<div
    x-data="{
        toasts: [],
        add(t) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type: t.type ?? 'success', message: t.message ?? '' });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
    }"
    @toast.window="add($event.detail)"
    class="pointer-events-none fixed right-4 top-4 z-50 flex w-full max-w-sm flex-col gap-2 sm:right-6 sm:top-6"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto flex items-start gap-3 rounded-xl border bg-white p-4 shadow-xl"
            :class="toast.type === 'success' ? 'border-emerald-200' : 'border-red-200'"
        >
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
                :class="toast.type === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'">
                <i class="fa-solid" :class="toast.type === 'success' ? 'fa-check' : 'fa-xmark'"></i>
            </span>
            <div class="min-w-0 flex-1 pt-0.5">
                <p class="text-sm font-semibold" :class="toast.type === 'success' ? 'text-emerald-700' : 'text-red-700'" x-text="toast.type === 'success' ? 'Berhasil' : 'Terjadi Kesalahan'"></p>
                <p class="mt-0.5 truncate text-sm text-slate-500" x-text="toast.message"></p>
            </div>
            <button @click="remove(toast.id)" class="text-slate-300 hover:text-slate-500">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    </template>
</div>
