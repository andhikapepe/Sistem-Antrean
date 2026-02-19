<div class="space-y-6">
    <header>
        <flux:heading size="xl">Pengaturan Sistem</flux:heading>
        <flux:subheading size="lg">Konfigurasi identitas organisasi, kontak, dan SEO aplikasi.</flux:subheading>
    </header>

    <form wire:submit.prevent="save" class="space-y-6">
        <flux:card class="space-y-6">
            <flux:heading variant="subtle">Identitas Organisasi</flux:heading>

            <div class="gap-6 grid grid-cols-1 md:grid-cols-2">
                <flux:input label="Nama Aplikasi" wire:model="appName" />
                <flux:input label="Tipe Organisasi" placeholder="Contoh: Instansi Pemerintah, Perusahaan Swasta" wire:model="appOrgType" />
            </div>

            <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    <p class="mb-2 text-gray-400 text-xs italic">Preview Logo:</p>
                    <div class="flex justify-center items-center bg-gray-50 border-2 border-dashed rounded-lg size-20 overflow-hidden">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="size-full object-contain">
                        @elseif ($currentLogo)
                            <img src="{{ asset('storage/' . $currentLogo) }}" class="size-full object-contain">
                        @else
                            <flux:icon name="photo" variant="outline" class="size-8 text-gray-300" />
                        @endif
                    </div>
                </div>
                <div class="flex-grow">
                    <flux:input type="file" label="Ganti Logo / Favicon" wire:model="logo" />
                    <flux:subheading class="mt-2">Maks. 1MB (PNG, JPG, SVG, ICO)</flux:subheading>
                    <div wire:loading wire:target="logo" class="mt-2 font-semibold text-blue-600 text-xs animate-pulse">
                        Mengunggah gambar...
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading variant="subtle">Informasi Kontak & Footer</flux:heading>
            <div class="gap-6 grid grid-cols-1 md:grid-cols-2">
                <flux:input label="Nomor Kontak" wire:model="appContact" icon="phone" />
                <flux:input label="Email Support" wire:model="appEmail" icon="envelope" />
            </div>
            <flux:textarea label="Alamat Kantor" wire:model="appAddress" />
            <flux:input label="Teks Copyright Footer" wire:model="appCopyright" placeholder="© 2026 Nama Instansi. All rights reserved." />
        </flux:card>

        <flux:card class="space-y-6">
            <flux:heading variant="subtle">Search Engine Optimization (SEO)</flux:heading>
            <flux:textarea label="Meta Description" wire:model="metaDescription" rows="2" />
            <flux:input label="Meta Keywords" wire:model="metaKeywords" placeholder="antrean, sistem, manajemen" />
        </flux:card>

        <div class="flex justify-end pt-4">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </flux:button>
        </div>
    </form>
</div>
