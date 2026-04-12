# Panduan Menu Surat Keluar

## Fungsi Menu

Menu Surat Keluar digunakan untuk membuat, mengunggah, mengajukan review, menyetujui, mengirim, mengunduh, dan mengarsipkan surat keluar.

## Hak Akses

- Admin dapat melihat dan mengelola seluruh surat keluar.
- Pengguna non-admin hanya dapat melihat surat keluar jika dirinya adalah pembuat surat atau penandatangan surat.
- Untuk surat yang dibuat di web, proses review dilakukan oleh penandatangan.
- Untuk surat keluar via upload PDF, file dianggap sudah ditandatangani sehingga tidak perlu review.

## Metode Pembuatan Surat

Terdapat dua metode:

1. **Buat di Web**
   - Digunakan untuk membuat surat langsung dari editor aplikasi.
   - Nomor surat dapat dibuat otomatis oleh sistem.
   - Surat perlu diajukan review sebelum disetujui.

2. **Upload File PDF**
   - Digunakan jika surat sudah dibuat dan ditandatangani di luar aplikasi.
   - Wajib memilih nomor dari menu Generate Nomor.
   - File PDF yang diunggah otomatis dianggap sudah disetujui.

## Membuat Surat Keluar di Web

1. Buka menu **Surat Keluar**.
2. Klik **Buat**.
3. Pilih metode **Buat di Web**.
4. Isi data surat:
   - **Klasifikasi Surat**
   - **Tanggal Surat**
   - **Tujuan**
   - **Alamat Tujuan** jika ada
   - **Perihal**
   - **Lampiran** jika ada
   - **Sifat Surat**
   - **Penandatangan**
   - **Tembusan** jika diperlukan
5. Isi bagian **Isi Surat** pada rich editor.
6. Klik **Buat** untuk menyimpan sebagai draft.

## Mengajukan Review

1. Pastikan surat masih berstatus draft.
2. Klik aksi **Submit Review**.
3. Surat akan masuk ke proses review oleh penandatangan.
4. Setelah diajukan review, pembuat tidak boleh mengubah isi surat secara sembarangan kecuali status dikembalikan atau ditolak sesuai alur.

## Menyetujui atau Menolak Surat

1. Login sebagai penandatangan atau admin.
2. Buka menu **Surat Keluar**.
3. Pilih surat yang perlu direview.
4. Klik **Setujui** jika surat sudah benar.
5. Klik **Tolak** jika surat perlu diperbaiki.

## Mengirim Surat

1. Pastikan surat sudah disetujui.
2. Klik aksi **Kirim**.
3. Status surat akan berubah menjadi terkirim.

## Upload Surat Keluar PDF

1. Buka menu **Surat Keluar**.
2. Klik **Buat**.
3. Pilih metode **Upload File PDF**.
4. Pilih nomor surat dari daftar nomor yang sudah dibuat melalui menu Generate Nomor.
5. Lengkapi data yang belum terisi otomatis.
6. Upload file PDF surat.
7. Klik **Buat**.

## Ketentuan Upload PDF

- File harus berformat PDF.
- Ukuran file maksimal 3 MB.
- Jika file lebih dari 3 MB, kompres terlebih dahulu melalui tautan berikut: <https://www.ilovepdf.com/compress_pdf>
- PDF yang diunggah dianggap sudah final dan sudah ditandatangani, sehingga tidak melewati review lagi.

## Mengunduh Surat

1. Buka menu **Surat Keluar**.
2. Pilih surat yang ingin diunduh.
3. Gunakan aksi **Download PDF** untuk surat yang dibuat di web.
4. Gunakan aksi **Download File** untuk surat yang dibuat melalui upload PDF.

## Mengarsipkan Surat Keluar

1. Buka menu **Surat Keluar**.
2. Pilih surat yang sudah selesai prosesnya.
3. Klik aksi **Arsipkan**.
4. Surat akan masuk ke menu **Arsip Surat**.

## Catatan Penggunaan

- Untuk surat resmi yang membutuhkan QR code atau persetujuan sistem, gunakan metode **Buat di Web**.
- Untuk dokumen seperti KB, SPD, S.Kep, atau dokumen lain yang sudah diproses di luar sistem, gunakan **Generate Nomor** lalu upload PDF final.
- Pembuat surat hanya dapat mengedit, menghapus, mengajukan review, dan mengirim surat yang dibuat olehnya, kecuali admin.

