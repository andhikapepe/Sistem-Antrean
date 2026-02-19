<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Masmerise\Toaster\Toaster;

class SettingManager extends Component
{
    use WithFileUploads;

    // Form properties - General
    public $appName, $appOrgType, $appAddress, $appContact, $appEmail, $appCopyright;

    // SEO & Visual
    public $metaDescription, $metaKeywords, $logo, $currentLogo;

    public function mount()
    {
        // Ambil data menggunakan helper static get() yang sudah kita buat di Model
        $this->appName         = Setting::get('app_name');
        $this->appOrgType      = Setting::get('app_org_type');
        $this->appAddress      = Setting::get('app_address');
        $this->appContact      = Setting::get('app_contact');
        $this->appEmail        = Setting::get('app_email');
        $this->appCopyright    = Setting::get('app_copyright');
        $this->metaDescription = Setting::get('meta_description');
        $this->metaKeywords    = Setting::get('meta_keywords');
        $this->currentLogo     = Setting::get('app_logo');
    }

    public function save()
    {
        $this->validate([
            'appName'         => 'required|min:3|max:50',
            'appOrgType'      => 'nullable|string|max:100',
            'appAddress'      => 'nullable|string',
            'appContact'      => 'nullable|string',
            'appEmail'        => 'nullable|email',
            'appCopyright'    => 'nullable|string',
            'metaDescription' => 'nullable|max:160',
            'metaKeywords'    => 'nullable|max:255',
            'logo'            => 'nullable|image|mimes:png,jpg,jpeg,svg,ico|max:1024',
        ]);

        // Simpan data teks secara kolektif
        $settings = [
            'app_name'         => $this->appName,
            'app_org_type'     => $this->appOrgType,
            'app_address'      => $this->appAddress,
            'app_contact'      => $this->appContact,
            'app_email'        => $this->appEmail,
            'app_copyright'    => $this->appCopyright,
            'meta_description' => $this->metaDescription,
            'meta_keywords'    => $this->metaKeywords,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Proses Upload Logo
        if ($this->logo) {
            if ($this->currentLogo && Storage::disk('public')->exists($this->currentLogo)) {
                Storage::disk('public')->delete($this->currentLogo);
            }

            $path = $this->logo->store('site', 'public');
            Setting::updateOrCreate(['key' => 'app_logo'], ['value' => $path]);

            $this->currentLogo = $path;
            $this->logo = null;
        }

        Toaster::success('Pengaturan berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.admin.setting-manager')->title('Manajemen Situs');
    }
}
