<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="space-y-3">
                <h2 class="text-xl font-semibold tracking-normal text-gray-950">
                    Panduan Pengguna E-Surat
                </h2>
                <p class="text-sm leading-6 text-gray-600">
                    Panduan ini merangkum cara menggunakan setiap menu pada aplikasi E-Surat. Menu dan tombol
                    yang tampil dapat berbeda untuk setiap pengguna, tergantung role dan hak akses akun.
                </p>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <h3 class="text-sm font-semibold text-gray-950">Catatan umum akses</h3>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm leading-6 text-gray-700">
                        <li>Admin dapat mengelola seluruh data sesuai hak akses sistem.</li>
                        <li>Pengguna non-admin hanya dapat melihat atau memproses data yang terkait dengannya.</li>
                        <li>Jika menu atau tombol tidak muncul, kemungkinan akun belum memiliki izin untuk aksi
                            tersebut.</li>
                        <li>Gunakan tombol notifikasi di bagian atas untuk melihat informasi baru dari sistem.</li>
                    </ul>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Daftar Menu</x-slot>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (['Dasbor', 'Surat Masuk', 'Surat Keluar', 'Generate Nomor', 'Arsip Surat', 'Disposisi', 'Unit Kerja', 'Klasifikasi Surat', 'Pengguna', 'Role Management', 'Template Surat'] as $menu)
                    <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-800">
                        {{ $menu }}
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Dasbor</x-slot>

            <div class="space-y-4 text-sm leading-6 text-gray-700">
                <p>
                    Menu Dasbor adalah halaman awal setelah login. Halaman ini digunakan untuk melihat ringkasan
                    aktivitas persuratan sebelum membuka menu lain.
                </p>
                <ol class="list-decimal space-y-2 pl-5">
                    <li>Login ke aplikasi E-Surat.</li>
                    <li>Setelah berhasil login, pengguna diarahkan ke halaman Dasbor.</li>
                    <li>Perhatikan ringkasan aktivitas yang tersedia.</li>
                    <li>Gunakan sidebar kiri untuk berpindah ke Surat Masuk, Surat Keluar, Disposisi, atau Master Data.
                    </li>
                </ol>
                <p>
                    Untuk notifikasi, klik ikon notifikasi di kanan atas. Jika tugas baru belum terlihat, refresh
                    halaman terlebih dahulu.
                </p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Surat Masuk</x-slot>

            <div class="space-y-5 text-sm leading-6 text-gray-700">
                <p>
                    Menu Surat Masuk digunakan untuk mencatat, melihat, mengelola, menindaklanjuti,
                    menyelesaikan, dan mengarsipkan surat yang diterima instansi.
                </p>

                <div>
                    <h3 class="font-semibold text-gray-950">Hak akses</h3>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li>Admin dan staf administrasi dapat melihat dan mengelola data surat masuk.</li>
                        <li>Pengguna non-admin dapat melihat surat jika menjadi penerima surat.</li>
                        <li>Pengguna juga dapat melihat surat jika menerima disposisi, langsung maupun melalui unit
                            kerja.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-950">Membuat surat masuk</h3>
                    <ol class="mt-2 list-decimal space-y-2 pl-5">
                        <li>Buka menu <strong>Surat Masuk</strong>, lalu klik <strong>Buat</strong>.</li>
                        <li>Isi informasi surat: nomor surat, nomor agenda, tanggal surat, dan tanggal diterima.</li>
                        <li>Isi pengirim: nama pengirim wajib, alamat pengirim opsional.</li>
                        <li>Isi detail surat: perihal, sifat surat, prioritas, dan penerima jika ada.</li>
                        <li>Upload lampiran PDF atau gambar scan jika tersedia, lalu isi keterangan tambahan bila perlu.
                        </li>
                        <li>Klik <strong>Buat</strong> untuk menyimpan.</li>
                    </ol>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-950">Disposisi, selesai, dan arsip</h3>
                    <ol class="mt-2 list-decimal space-y-2 pl-5">
                        <li>Gunakan aksi <strong>Buat Disposisi</strong> untuk meneruskan surat ke user atau unit kerja.
                        </li>
                        <li>Isi instruksi, catatan, dan batas waktu disposisi bila diperlukan.</li>
                        <li>Gunakan aksi <strong>Tandai Selesai</strong> jika pekerjaan terhadap surat sudah selesai.
                        </li>
                        <li>Gunakan aksi <strong>Arsipkan</strong> untuk memindahkan surat selesai ke Arsip Surat.</li>
                    </ol>
                </div>

                <p>
                    Pastikan nomor surat dan tanggal surat sesuai dokumen asli. Jangan menghapus surat yang masih
                    memiliki proses disposisi aktif kecuali benar-benar diperlukan.
                </p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Surat Keluar</x-slot>

            <div class="space-y-5 text-sm leading-6 text-gray-700">
                <p>
                    Menu Surat Keluar digunakan untuk membuat, mengunggah, mengajukan review, menyetujui,
                    mengirim, mengunduh, dan mengarsipkan surat keluar.
                </p>

                <div>
                    <h3 class="font-semibold text-gray-950">Metode pembuatan</h3>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li><strong>Buat di Web</strong>: surat dibuat dari editor aplikasi, lalu diajukan review.</li>
                        <li><strong>Upload File PDF</strong>: surat sudah dibuat di luar aplikasi dan PDF final diunggah
                            ke sistem.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-950">Membuat surat di web</h3>
                    <ol class="mt-2 list-decimal space-y-2 pl-5">
                        <li>Buka <strong>Surat Keluar</strong>, klik <strong>Buat</strong>, lalu pilih metode
                            <strong>Buat di Web</strong>.</li>
                        <li>Isi klasifikasi, tanggal, tujuan, alamat tujuan, perihal, lampiran, sifat surat,
                            penandatangan, dan tembusan bila perlu.</li>
                        <li>Isi bagian <strong>Isi Surat</strong> menggunakan editor.</li>
                        <li>Simpan sebagai draft, lalu klik <strong>Submit Review</strong> jika siap diperiksa.</li>
                        <li>Penandatangan atau admin dapat klik <strong>Setujui</strong> atau <strong>Tolak</strong>.
                        </li>
                        <li>Setelah disetujui, klik <strong>Kirim</strong> untuk mengubah status menjadi terkirim.</li>
                    </ol>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-950">Upload surat PDF</h3>
                    <ol class="mt-2 list-decimal space-y-2 pl-5">
                        <li>Buat nomor terlebih dahulu melalui menu <strong>Generate Nomor</strong>.</li>
                        <li>Buka <strong>Surat Keluar</strong>, klik <strong>Buat</strong>, lalu pilih metode
                            <strong>Upload File PDF</strong>.</li>
                        <li>Pilih nomor surat yang masih dicadangkan, lengkapi data, upload PDF final, lalu simpan.</li>
                    </ol>
                    <p class="mt-2">
                        File upload harus PDF dengan ukuran maksimal 3 MB. PDF upload dianggap sudah final dan sudah
                        ditandatangani, sehingga tidak melewati review lagi.
                    </p>
                </div>

                <p>
                    Gunakan <strong>Download PDF</strong> untuk surat yang dibuat di web dan <strong>Download
                        File</strong>
                    untuk surat yang dibuat melalui upload PDF. Surat yang prosesnya selesai dapat diarsipkan.
                </p>
            </div>
        </x-filament::section>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-filament::section>
                <x-slot name="heading">Generate Nomor</x-slot>

                <div class="space-y-4 text-sm leading-6 text-gray-700">
                    <p>
                        Menu Generate Nomor digunakan untuk mencadangkan nomor surat keluar yang dokumennya dibuat
                        di luar aplikasi, seperti KB, SPD, S.Kep, atau dokumen khusus lain.
                    </p>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Buka menu <strong>Generate Nomor</strong>.</li>
                        <li>Isi klasifikasi surat, tanggal surat, sifat surat, dan keterangan jika perlu.</li>
                        <li>Isi tujuan dan perihal jika sudah diketahui. Keduanya boleh dikosongkan.</li>
                        <li>Klik generate untuk mencadangkan nomor.</li>
                        <li>Gunakan nomor tersebut saat membuat Surat Keluar dengan metode Upload File PDF.</li>
                    </ol>
                    <p>Jangan membuat nomor ganda untuk dokumen yang sama.</p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Arsip Surat</x-slot>

                <div class="space-y-4 text-sm leading-6 text-gray-700">
                    <p>
                        Menu Arsip Surat digunakan untuk melihat surat masuk dan surat keluar yang sudah diarsipkan,
                        sehingga surat aktif dan surat selesai tidak tercampur.
                    </p>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Buka menu <strong>Arsip Surat</strong>.</li>
                        <li>Pilih tab <strong>Surat Masuk</strong> atau <strong>Surat Keluar</strong>.</li>
                        <li>Cari arsip berdasarkan nomor, pengirim atau tujuan, perihal, dan tanggal jika tersedia.</li>
                        <li>Klik <strong>View</strong> untuk melihat detail surat.</li>
                        <li>Gunakan aksi <strong>Unarchive</strong> atau <strong>Keluarkan dari Arsip</strong> untuk
                            mengembalikan surat.</li>
                    </ol>
                    <p>Mengarsipkan surat tidak sama dengan menghapus surat.</p>
                </div>
            </x-filament::section>
        </div>

        <x-filament::section>
            <x-slot name="heading">Disposisi</x-slot>

            <div class="space-y-5 text-sm leading-6 text-gray-700">
                <p>
                    Menu Disposisi digunakan untuk mengelola tindak lanjut surat masuk. Disposisi dapat diarahkan
                    kepada pengguna tertentu atau unit kerja tertentu.
                </p>

                <div>
                    <h3 class="font-semibold text-gray-950">Hak akses</h3>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li>Admin dapat melihat semua disposisi.</li>
                        <li>Pengguna dapat melihat disposisi jika dirinya pembuat atau penerima disposisi.</li>
                        <li>Pengguna dapat melihat disposisi jika unit kerjanya menjadi penerima disposisi.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-950">Alur kerja</h3>
                    <ol class="mt-2 list-decimal space-y-2 pl-5">
                        <li>Buat disposisi dari Surat Masuk atau menu Disposisi.</li>
                        <li>Pilih <strong>Jenis Tujuan</strong>: User atau Unit Kerja.</li>
                        <li>Pilih user tujuan atau unit kerja tujuan sesuai jenis yang dipilih.</li>
                        <li>Isi instruksi, catatan, dan batas waktu bila ada.</li>
                        <li>Klik <strong>Proses</strong> saat disposisi mulai dikerjakan.</li>
                        <li>Klik <strong>Selesai</strong> jika instruksi sudah dilaksanakan.</li>
                        <li>Gunakan <strong>Teruskan</strong> jika disposisi perlu diteruskan ke penerima lanjutan.</li>
                    </ol>
                </div>

                <p>
                    Pastikan instruksi jelas dan gunakan batas waktu untuk disposisi yang perlu diprioritaskan.
                    Menyelesaikan disposisi tindak lanjut akan ikut menandai Surat Masuk terkait sebagai selesai
                    dan menutup disposisi lain yang masih aktif pada surat tersebut.
                </p>
            </div>
        </x-filament::section>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-filament::section>
                <x-slot name="heading">Unit Kerja</x-slot>

                <div class="space-y-4 text-sm leading-6 text-gray-700">
                    <p>Menu Unit Kerja digunakan admin untuk mengelola struktur unit atau bagian instansi.</p>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Buka <strong>Unit Kerja</strong>, lalu klik <strong>Buat</strong>.</li>
                        <li>Isi nama unit kerja, kode jika ada, dan parent unit jika unit memiliki induk.</li>
                        <li>Simpan data. Untuk perubahan, buka data lalu klik <strong>Edit</strong>.</li>
                        <li>Hapus unit hanya jika tidak lagi dipakai oleh pengguna atau disposisi aktif.</li>
                    </ol>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Klasifikasi Surat</x-slot>

                <div class="space-y-4 text-sm leading-6 text-gray-700">
                    <p>
                        Menu Klasifikasi Surat digunakan admin untuk mengatur kode dan jenis klasifikasi yang dipakai
                        pada Surat Keluar dan Generate Nomor.
                    </p>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Buka <strong>Klasifikasi Surat</strong>, lalu klik <strong>Buat</strong>.</li>
                        <li>Isi kode, nama, kategori, kode surat, dan keterangan jika diperlukan.</li>
                        <li>Pastikan status <strong>Aktif</strong> menyala jika klasifikasi boleh digunakan.</li>
                        <li>Untuk menonaktifkan, edit data lalu matikan status Aktif.</li>
                    </ol>
                    <p>Menonaktifkan klasifikasi lebih aman daripada menghapus jika sudah pernah dipakai.</p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Pengguna</x-slot>

                <div class="space-y-4 text-sm leading-6 text-gray-700">
                    <p>
                        Menu Pengguna digunakan admin untuk mengelola akun, nama, email, password, jabatan,
                        unit kerja, dan role.
                    </p>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Buka <strong>Pengguna</strong>, lalu klik <strong>Buat</strong>.</li>
                        <li>Isi nama, email, password, jabatan, unit kerja, dan role.</li>
                        <li>Untuk mengubah akun, klik <strong>Edit</strong>. Kosongkan password jika tidak ingin
                            menggantinya.</li>
                        <li>Minta pengguna logout dan login kembali jika perubahan role belum terasa.</li>
                    </ol>
                    <p>Pastikan email unik dan unit kerja benar karena data ini dipakai pada akses disposisi.</p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Role Management</x-slot>

                <div class="space-y-4 text-sm leading-6 text-gray-700">
                    <p>
                        Menu Role Management digunakan admin untuk mengatur role dan permission. Role menentukan
                        menu dan aksi yang boleh dilakukan pengguna.
                    </p>
                    <ol class="list-decimal space-y-2 pl-5">
                        <li>Buka <strong>Role Management</strong>, lalu klik <strong>Buat</strong>.</li>
                        <li>Isi nama role dan pilih permission yang diperlukan.</li>
                        <li>Untuk mengubah akses, edit role lalu centang atau hapus permission sesuai kebutuhan.</li>
                        <li>Minta pengguna terdampak logout dan login kembali jika akses belum berubah.</li>
                    </ol>
                    <p>Berikan permission minimal terlebih dahulu, lalu tambahkan jika memang diperlukan.</p>
                </div>
            </x-filament::section>
        </div>

        {{-- <x-filament::section>
            <x-slot name="heading">Template Surat</x-slot>

            <div class="space-y-4 text-sm leading-6 text-gray-700">
                <p>
                    Menu Template Surat digunakan untuk menyimpan format isi surat yang sering dipakai agar pembuatan
                    surat lebih cepat dan konsisten.
                </p>
                <ol class="list-decimal space-y-2 pl-5">
                    <li>Buka menu <strong>Template Surat</strong>, lalu klik <strong>Buat</strong>.</li>
                    <li>Isi nama template.</li>
                    <li>Pilih unit kerja jika template hanya digunakan oleh unit tertentu.</li>
                    <li>Isi template menggunakan rich editor, lalu simpan.</li>
                    <li>Untuk memperbarui template, pilih data lalu klik <strong>Edit</strong>.</li>
                    <li>Hapus template hanya jika sudah tidak digunakan lagi.</li>
                </ol>
                <p>
                    Gunakan nama template yang jelas dan placeholder seperti "(Nama)", "(Tanggal)", atau "(Perihal)"
                    jika isi perlu diganti pengguna saat dipakai.
                </p>
            </div>
        </x-filament::section> --}}
    </div>
</x-filament-panels::page>
