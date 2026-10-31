<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#dc2626">
    <meta name="description" content="Kebijakan Privasi Swaratani">
    <link rel="icon" href="{{ asset(config('app.logo', 'images/logo.png')) }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <title>Kebijakan Privasi - {{ env('APP_NAME', 'Swaratani') }}</title>

    @include('partials.theme')

    <style>
        /* Reusing styles from beranda.blade.php for consistency */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: floatUp 15s infinite linear;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100px) rotate(720deg);
                opacity: 0;
            }
        }

        /* Main Content */
        .main-wrapper {
            flex: 1;
            padding: 2rem 1rem;
            position: relative;
            z-index: 1;
        }

        .content-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            max-width: 900px;
            margin: 0 auto;
            color: var(--text-main);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        h2 {
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        p,
        li {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        ul {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: var(--primary);
            transform: translateX(-5px);
        }

        /* Footer */
        .page-footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
            position: relative;
            z-index: 1;
        }

        /* ========= Mobile Responsive ========= */
        @media (max-width: 576px) {
            .content-card {
                padding: 1.5rem 1.25rem;
                border-radius: 16px;
            }

            h1 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }

            h2 {
                font-size: 1.1rem;
                margin-top: 1.5rem;
            }

            .main-wrapper {
                padding: 1rem 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Particles -->
    <div class="particles">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 4s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 5s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 1s;"></div>
    </div>

    <div class="main-wrapper">
        <div class="container">
            <a href="{{ route('home') }}" class="back-btn">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>

            <div class="content-card">
                <h1>Kebijakan Privasi</h1>
                <p><strong>Terakhir diperbarui: 14 Februari 2026</strong></p>

                <p>Aplikasi <strong>{{ env('APP_NAME', 'Swaratani') }}</strong> ("Aplikasi") dikembangkan sebagai layanan Gratis. Layanan ini
                    disediakan tanpa biaya dan dimaksudkan untuk digunakan apa adanya.</p>

                <p>Halaman ini digunakan untuk menginformasikan pengunjung mengenai kebijakan kami terkait pengumpulan,
                    penggunaan, dan pengungkapan Informasi Pribadi jika memutuskan untuk menggunakan Layanan kami.</p>

                <p>Jika Anda memilih untuk menggunakan Layanan kami, maka Anda menyetujui pengumpulan dan penggunaan
                    informasi sehubungan dengan kebijakan ini. Informasi Pribadi yang kami kumpulkan digunakan untuk
                    menyediakan dan meningkatkan Layanan. Kami tidak akan menggunakan atau membagikan informasi Anda
                    dengan siapa pun kecuali sebagaimana dijelaskan dalam Kebijakan Privasi ini.</p>

                <h2>Pengumpulan dan Penggunaan Informasi</h2>
                <p>Untuk pengalaman yang lebih baik saat menggunakan Layanan kami, kami mungkin meminta Anda untuk
                    memberikan kami informasi pengenal pribadi tertentu. Karena aplikasi ini berfungsi sebagai
                    <strong>WebView</strong> yang memuat situs web <code>https://swaratani.id</code>, sebagian besar
                    data yang Anda masukkan (seperti saat login atau mengisi formulir) diproses langsung oleh situs web
                    tersebut sesuai dengan kebijakan privasi situs web ini.</p>
                <p>Aplikasi itu sendiri <strong>tidak</strong> mengumpulkan data pribadi sensitif dari perangkat Anda
                    (seperti kontak, lokasi GPS, kamera, atau mikrofon) secara diam-diam. Akses internet digunakan
                    semata-mata untuk memuat konten dari <code>https://swaratani.id</code>.</p>

                <p>Informasi yang mungkin dikumpulkan secara otomatis oleh layanan pihak ketiga yang digunakan dalam
                    aplikasi (jika ada, seperti Google Play Services) dapat mencakup:</p>
                <ul>
                    <li>Alamat Protokol Internet (IP) perangkat Anda</li>
                    <li>Nama perangkat</li>
                    <li>Versi sistem operasi</li>
                    <li>Konfigurasi aplikasi saat menggunakan Layanan kami</li>
                    <li>Waktu dan tanggal penggunaan Anda atas Layanan</li>
                    <li>Statistik lainnya</li>
                </ul>

                <h2>Cookies</h2>
                <p>Cookies adalah file dengan sejumlah kecil data yang biasanya digunakan sebagai pengenal unik anonim.
                    Ini dikirim ke browser Anda dari situs web yang Anda kunjungi dan disimpan di memori internal
                    perangkat Anda.</p>
                <p>Layanan ini (Aplikasi) tidak menggunakan "cookies" secara eksplisit. Namun, karena aplikasi ini
                    memuat situs web pihak ketiga (<code>swaratani.id</code>), situs web tersebut mungkin menggunakan
                    "cookies" untuk mengumpulkan informasi dan meningkatkan layanan mereka. Anda memiliki opsi untuk
                    menerima atau menolak cookies ini di situs web tersebut.</p>

                <h2>Penyedia Layanan</h2>
                <p>Kami mungkin mempekerjakan perusahaan dan individu pihak ketiga untuk memfasilitasi Layanan kami,
                    menyediakan Layanan atas nama kami, atau membantu kami dalam menganalisis bagaimana Layanan kami
                    digunakan.</p>
                <p>Kami ingin menginformasikan pengguna bahwa pihak ketiga ini mungkin memiliki akses ke Informasi
                    Pribadi Anda untuk melakukan tugas yang diberikan kepada mereka atas nama kami. Namun, mereka
                    berkewajiban untuk tidak mengungkapkan atau menggunakan informasi tersebut untuk tujuan lain apa
                    pun.</p>

                <h2>Keamanan</h2>
                <p>Kami menghargai kepercayaan Anda dalam memberikan Informasi Pribadi Anda kepada kami, oleh karena itu
                    kami berupaya untuk menggunakan cara yang dapat diterima secara komersial untuk melindunginya. Namun
                    ingatlah bahwa tidak ada metode transmisi melalui internet, atau metode penyimpanan elektronik yang
                    100% aman dan andal.</p>

                <h2>Tautan ke Situs Lain</h2>
                <p>Layanan ini mungkin berisi tautan ke situs lain. Jika Anda mengklik tautan pihak ketiga, Anda akan
                    diarahkan ke situs tersebut. Kami sangat menyarankan Anda untuk meninjau Kebijakan Privasi situs web
                    ini.</p>

                <h2>Privasi Anak-anak</h2>
                <p>Layanan ini tidak ditujukan kepada siapa pun yang berusia di bawah 13 tahun. Kami tidak secara sadar
                    mengumpulkan informasi yang dapat diidentifikasi secara pribadi dari anak-anak di bawah 13 tahun.
                </p>

                <h2>Perubahan pada Kebijakan Privasi Ini</h2>
                <p>Kami dapat memperbarui Kebijakan Privasi kami dari waktu ke waktu. Oleh karena itu, Anda disarankan
                    untuk meninjau halaman ini secara berkala untuk setiap perubahan.</p>

                <h2>Hubungi Kami</h2>
                <p>Jika Anda memiliki pertanyaan atau saran tentang Kebijakan Privasi kami, jangan ragu untuk
                    menghubungi kami di:</p>
                <ul>
                    <li>Email: <strong>swaratani.dev@gmail.com</strong></li>
                    <li>Website: <a href="https://swaratani.id">https://swaratani.id</a></li>
                </ul>
            </div>
        </div>
    </div>

    <footer class="page-footer">
        <p>© 2026 Swaratani IoT &bull; Tim Engineering Pertanian</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>