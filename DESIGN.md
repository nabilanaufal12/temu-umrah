# TEMU UMRAH — UI Redesign Brief

Dokumen ini menjadi arahan redesign UI TEMU UMRAH di Stitch. Hasil akhir harus
terasa seperti **sub-portal resmi Universitas Maritim Raja Ali Haji**, bukan
aplikasi SaaS generik: informatif, formal, tenang, khas UMRAH, dan siap dipakai
sehari-hari.

## Referensi Institusional

Gunakan `https://umrah.ac.id/` sebagai referensi struktur dan nuansa, bukan
untuk menyalin halaman secara literal.

Karakter yang perlu diambil:

- masthead institusional dengan logo dan nama universitas;
- utility bar untuk kanal sosial, kontak, dan akses penting;
- navigasi kategori yang jelas dan terasa seperti situs universitas;
- hero konten utama dengan foto kegiatan nyata;
- section berbasis editorial seperti `Pendidikan`, `Riset & Inovasi`,
  `Pengabdian`, dan `Pengumuman`;
- footer resmi dengan alamat, email, kanal sosial, dan identitas institusi;
- layout putih/lapang dengan aksen biru UMRAH, bukan panel dashboard gelap.

TEMU UMRAH tetap fokus pada event mahasiswa. Struktur institusional tersebut
diterjemahkan menjadi `Event Terbaru`, `Event Akademik`, `Kemaritiman`,
`Organisasi Mahasiswa`, dan `Pengumuman Pendaftaran`.

## Translasi Referensi Visual

Gunakan pola landing page ilustratif seperti referensi terbaru:

- hero besar berbentuk panel biru dengan copy singkat dan satu CTA utama;
- ilustrasi vektor kampus, laut, bukit, dan aktivitas mahasiswa sebagai visual
  utama, bukan foto stok generik;
- mockup ponsel atau kartu UI yang menunjukkan pengalaman menjelajah event;
- strip nama ekosistem/organisasi dengan gaya monokrom ringan;
- tiga kartu fitur dengan artwork sederhana dan warna pastel;
- section biru kedua dengan lengkung putih, mockup aplikasi, dan CTA;
- katalog event, FAQ, dan CTA penutup dalam halaman panjang berlatar putih;
- bentuk rounded hanya pada panel utama dan card fitur, dengan whitespace yang
  luas agar halaman tetap terasa ringan.

Implementasi vector hero berada di `public/assets/illustrations/`, sedangkan
aturan halaman landing berada di `public/assets/css/home.css`. Visual boleh
ceria seperti referensi, tetapi copy, data, dan alur tetap resmi serta spesifik
untuk TEMU UMRAH.

## Arah Produk

TEMU UMRAH adalah portal resmi untuk menemukan event mahasiswa, mendaftar,
menyimpan e-tiket, melakukan presensi, mengelola event oleh panitia Ormawa,
serta memoderasi event oleh Admin BAAK.

**Karakter:** official university portal, institutional, editorial, maritime,
practical, trustworthy, calm, structured.

## Anti-AI-Slop Rules

Jangan gunakan:

- gradient biru-ungu sebagai latar utama;
- header aplikasi yang terasa seperti admin SaaS tanpa identitas universitas;
- hero dengan heading besar yang tidak informatif;
- terlalu banyak card dengan radius besar dan shadow berat;
- glassmorphism, neon glow, blob abstract, atau mesh gradient;
- ilustrasi robot, hologram, atau ikon dekoratif berlebihan;
- emoji sebagai ikon antarmuka;
- statistik besar yang tidak membantu keputusan pengguna;
- semua elemen floating tanpa grid dan alignment yang jelas;
- copywriting hiperbolik atau jargon startup.

Gunakan logo resmi UMRAH sebagai identitas induk jika tersedia dan foto kegiatan kampus yang nyata jika tersedia, label status yang
spesifik, serta informasi tanggal, lokasi, biaya, kuota, dan penyelenggara
sebagai fokus utama.

## Arah Visual

### Layout

- Desktop: max-width 1180–1240px dengan grid 12 kolom.
- Tablet: grid 8 kolom.
- Mobile: satu kolom dengan padding 20px.
- Gunakan alignment kiri yang kuat; jangan memusatkan semua konten.
- Gunakan dua tingkat header pada halaman publik: utility bar tipis dan
  masthead utama.
- Header katalog/detail boleh sticky setelah masthead terlewati.
- Konten penting berada di atas fold: judul, tanggal, lokasi, biaya, CTA.
- Gunakan section heading dengan garis atau aksen kecil, seperti portal berita
  universitas; jangan membuat setiap section menjadi card.

### Warna

```text
Ink / Navy       #102A43   teks utama dan header
Ocean            #176B87   link dan elemen interaktif
Deep Teal        #0B4F5C   aksen maritim
Sand             #F4EFE6   permukaan hangat
Paper            #FCFBF8   background utama
Gold             #B58A3A   aksen resmi, gunakan hemat
Success          #2F6F55
Warning          #9A6B22
Danger           #A44747
Muted            #667085
Border           #D9E0E5
```

Gold bukan warna tombol utama. Tombol utama gunakan Ink atau Ocean.

### Tipografi dan bentuk

- Heading landing page: **DM Serif Display** dengan fallback Georgia untuk
  headline besar bergaya ilustratif.
- Body/UI landing page: **Manrope** dengan fallback system sans-serif.
- Maksimal dua keluarga font.
- Gunakan angka tabular untuk harga, tanggal, kuota, dan statistik.
- Radius utama 4–8px. Beberapa blok berita boleh tanpa radius.
- Utamakan border dan perbedaan permukaan daripada shadow.
- Button height 42–46px dengan focus ring yang jelas.
- Gunakan satu gaya icon line, sekitar 1.75px.

## Struktur Header Publik

### Utility bar

Bar tipis di bagian paling atas, dengan informasi yang tidak mengganggu:

- `Universitas Maritim Raja Ali Haji`;
- `Kampus Dompak, Tanjungpinang`;
- tautan Instagram, YouTube, dan kontak resmi;
- `Masuk` atau nama pengguna di sisi kanan.

Gunakan ukuran teks kecil dan warna biru tua. Jangan membuat utility bar
terlihat seperti toolbar aplikasi.

### Masthead utama

Bar utama menampilkan logo universitas/portal di kiri dan navigasi di kanan.
Untuk TEMU UMRAH, gunakan susunan:

- `Beranda`;
- `Event`;
- `Organisasi Mahasiswa`;
- `Kalender Kegiatan`;
- `Tentang TEMU`;
- tombol `Masuk`.

Logo TEMU UMRAH boleh muncul sebagai sub-brand, tetapi jangan menggantikan
identitas UMRAH. Tampilkan relasi secara eksplisit: `TEMU UMRAH · Portal
Kegiatan Mahasiswa UMRAH`.

### Navigasi publik mobile

Gunakan menu drawer dengan kelompok:

- Jelajah Event;
- Kategori Event;
- Akses Mahasiswa;
- Portal Panitia;
- Tentang TEMU UMRAH.

## Navigasi Aplikasi Internal

### Mahasiswa

`Jelajah Event` · `Event Saya` · `Presensi` · `Sertifikat` · `Masuk`

Setelah login, utility bar menampilkan nama pengguna. Menu berisi `Profil`,
`Notifikasi`, dan `Keluar`.

### Panitia

`Dashboard` · `Event Saya` · `Peserta` · `Presensi` · `Sertifikat`

### Admin

Gunakan label `Admin BAAK`, bukan tampilan “super admin dark dashboard”.

`Ringkasan` · `Moderasi Event` · `Verifikasi Ormawa` · `Pengguna` · `Audit`

### Sistem visual internal

Seluruh halaman selain landing page memakai `public/assets/css/portal.css` sebagai
lapisan visual bersama. Sistem ini menyamakan navbar role-aware, bar konteks
portal, breadcrumb, kartu, form, tombol, footer, responsive spacing, dan focus
ring. Halaman internal juga memiliki section pendukung sesuai alurnya: quick
actions mahasiswa, workflow panitia/admin, panduan pembayaran, presensi,
sertifikat, keamanan profil, dan ringkasan event.

## Halaman yang Dibuat di Stitch

### 1. Beranda / Katalog Event — `/`

Struktur halaman mengikuti portal universitas, tetapi kontennya event mahasiswa:

1. Utility bar institusional.
2. Masthead dengan logo/sub-brand TEMU UMRAH.
3. Hero editorial dengan satu event unggulan dan foto nyata atau bidang foto
   yang tenang; hindari carousel yang bergerak otomatis.
4. Section `Event Terbaru` dengan satu berita utama dan beberapa listing kecil.
5. Section kategori: `Akademik`, `Kemaritiman`, `Organisasi`, `Seni & Olahraga`.
6. Section `Pengumuman Pendaftaran` untuk informasi deadline atau perubahan
   jadwal.
7. Search dan filter event sebagai toolbar yang mudah ditemukan, bukan hero
   dashboard.
8. Footer institusi dengan alamat UMRAH, email, kanal sosial, dan link portal.

Listing event boleh 3 kolom desktop dan 1 kolom mobile, tetapi tampil seperti
daftar berita/kegiatan resmi: foto atau thumbnail, label kategori, tanggal,
judul, penyelenggara, dan `Baca Selengkapnya` / `Lihat Event`.

Event item menampilkan kategori, judul, penyelenggara, tanggal, lokasi, biaya,
sisa kuota, dan CTA `Lihat Detail`. Bentuknya seperti listing editorial, bukan
card dashboard dengan gradient dan ikon besar.

### 2. Detail Event — `/event/{slug}`

Pertahankan header institusional dan breadcrumb. Gunakan dua kolom desktop:

- kiri: judul, deskripsi, jadwal, lokasi, narasumber, fasilitas;
- kanan: sticky registration panel dengan tanggal, biaya, kuota, dan CTA.

CTA memiliki state `Daftar Gratis`, `Lanjut Pembayaran`, `Kuota Penuh`, dan
`Sudah Terdaftar`.

### 3. Login — `/login`

Halaman login berdiri sendiri. Aktivasi mahasiswa dan pendaftaran ormawa
berada di halaman `/register` agar pengguna tidak melihat dua alur utama dalam
satu layar.

Hindari split-screen marketing hero. Letakkan form di atas background putih atau
foto kampus yang sangat halus, dengan masthead TEMU/UMRAH yang formal:

- wordmark kecil;
- judul `Masuk ke TEMU UMRAH`;
- identifier NIM/email;
- password dan remember session;
- tombol masuk;
- aktivasi mahasiswa dan daftar Ormawa.

Jangan tampilkan shortcut akun, password, atau tombol pengisian otomatis di
halaman login. Pengguna masuk memakai data akun yang tersimpan di database.

### 4. Dashboard Peserta — `/event-saya`

Gunakan summary strip sederhana: event aktif, menunggu pembayaran, event selesai,
dan sertifikat tersedia. Di bawahnya gunakan list/tabel responsif, bukan card
besar. Row berisi event, tanggal, status pembayaran, status presensi, dan action.

### 5. Dashboard Panitia — `/panitia/dashboard`

Fokus pada pekerjaan panitia: tombol `Ajukan Event`, daftar event Ormawa, status
`Draft`, `Menunggu Review`, `Dipublikasikan`, `Ditolak`, `Selesai`, jumlah peserta,
serta shortcut peserta, presensi, dan sertifikat.

Gunakan tabel dengan kolom jelas. KPI maksimal tiga atau empat dan harus
berisi angka yang membantu keputusan.

### 6. Admin BAAK — `/admin/panel`

Tampilan administratif yang rapi, bukan dashboard analytics generik. Sediakan
ringkasan antrean, tab Event/Ormawa, tabel dengan filter status, drawer review,
aksi `Setujui` dan `Tolak` dengan catatan, serta audit log yang ringan.

Prioritaskan keterbacaan data dan keamanan aksi daripada dekorasi.

## Komponen Reusable

Stitch harus menghasilkan komponen konsisten berikut:

- `UniversityUtilityBar`, `InstitutionalMasthead`, `AppHeader`, `PageContainer`,
  `Breadcrumbs`;
- `SearchBar`, `FilterBar`, `Pagination`;
- `FeaturedStory`, `EventListItem`, `EventStatusBadge`, `RegistrationPanel`;
- `DataTable`, `EmptyState`, `ConfirmDialog`, `Toast`;
- `InstitutionalFooter`, `MobileBottomNav`.

Setiap komponen perlu memiliki state default, hover, focus, disabled, loading,
empty, dan error.

## UX dan Aksesibilitas

- Kontras teks minimal WCAG AA.
- Semua field memiliki label yang terlihat.
- Focus ring jelas untuk keyboard.
- Jangan mengandalkan warna saja untuk status.
- Status memiliki teks, misalnya `Menunggu Review`, bukan hanya titik warna.
- Aksi destructive membutuhkan konfirmasi.
- Empty state menjelaskan langkah berikutnya.
- Loading state memakai skeleton sederhana.
- Mobile tidak memerlukan horizontal scrolling untuk aksi utama.

## Data dan Copy

Pertahankan data domain dari Laravel. Jangan membuat data dummy baru yang
terdengar generik.

Gunakan copy ringkas dan spesifik:

- `Daftar Event`;
- `Menunggu Pembayaran`;
- `Menunggu Persetujuan Admin`;
- `Kuota Tersisa`;
- `Diterbitkan oleh`;
- `Lihat E-Tiket`.

Hindari kalimat seperti `Empower your journey`, `Seamless experience`, atau
`Unlock your potential`.

## Prompt Utama untuk Stitch

```text
Redesign TEMU UMRAH as an official sub-portal of Universitas Maritim Raja Ali
Haji, visually inspired by the institutional structure of umrah.ac.id. Create
a formal university website with a thin utility bar, an institutional masthead,
clear navigation, editorial news-like sections, real campus photography, and a
complete institutional footer. The subject is student events, not university
news in general.

Use a white and warm-paper background, UMRAH navy and ocean blue, restrained gold
accents, thin borders, modest 4–8px corner radius, and very subtle shadows. Use
an editorial serif for section headings and a neutral sans serif for UI text.

The interface must feel human-designed, practical, and trustworthy. Prioritize
event title, organizer, date, location, fee, quota, and status. Use clear grids,
left-aligned content, real information density, and restrained visual hierarchy.

Do not use purple-blue gradients, glassmorphism, neon, blobs, oversized hero
headlines, generic SaaS analytics cards, robot/AI illustrations, emoji icons,
excessive rounded cards, or vague marketing copy. Do not invent product areas
that are not part of an event portal.

Design responsive desktop and mobile states for the public institutional home /
event catalog, event detail, login, participant dashboard, committee dashboard,
and BAAK admin moderation. Include loading, empty, error, disabled, focus, and
confirmation states.
```

## Handoff ke Laravel

Hasil Stitch diterjemahkan ke:

- `resources/views/layouts/app.blade.php`;
- `resources/views/components/`;
- `resources/views/pages/`;
- CSS di `public/assets/css/`;
- JavaScript browser biasa di `public/assets/js/`.

Jangan menambahkan framework frontend baru. Interaksi cukup memakai JavaScript
browser biasa atau endpoint Laravel yang sudah tersedia.
