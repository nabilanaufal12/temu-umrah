// ==========================================================================
// TEMU UMRAH - AUTHENTICATION CLIENT LOGIC
// NIM Verification (SIPA), Tab Switcher, and Form Validation
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
  // Data mahasiswa aktif yang dipakai untuk verifikasi aktivasi akun.
  const SIPA_STUDENTS = [
    {
      nim: '2401020073',
      name: 'Nabil',
      prodi: 'Teknik Informatika (S1)',
      email: '2401020073@student.umrah.ac.id',
      status: 'Aktif'
    },
    {
      nim: '2401020069',
      name: 'Balqis Manisa',
      prodi: 'Teknik Informatika (S1)',
      email: '2401020069@student.umrah.ac.id',
      status: 'Aktif'
    },
    {
      nim: '2401020087',
      name: 'Annisa Darmawan',
      prodi: 'Teknik Informatika (S1)',
      email: '2401020087@student.umrah.ac.id',
      status: 'Aktif'
    },
    {
      nim: '2401020110',
      name: 'Yehezkiel Alman Siambaton',
      prodi: 'Teknik Informatika (S1)',
      email: '2401020110@student.umrah.ac.id',
      status: 'Aktif'
    }
  ];

  // DOM Elements - Login
  const loginForm = document.getElementById('loginForm');
  const loginUserInput = document.getElementById('loginUser');
  const loginPassInput = document.getElementById('loginPass');
  const loginAlert = document.getElementById('loginAlert');
  const btnTogglePass = document.getElementById('btnTogglePass');

  if (btnTogglePass && loginPassInput) {
    btnTogglePass.addEventListener('click', () => {
      const eyeOpen = document.getElementById('eyeOpenIcon');
      const eyeClosed = document.getElementById('eyeClosedIcon');
      if (loginPassInput.type === 'password') {
        loginPassInput.type = 'text';
        if (eyeOpen) eyeOpen.style.display = 'none';
        if (eyeClosed) eyeClosed.style.display = 'block';
      } else {
        loginPassInput.type = 'password';
        if (eyeOpen) eyeOpen.style.display = 'block';
        if (eyeClosed) eyeClosed.style.display = 'none';
      }
    });
  }

  // DOM Elements - Activation
  const tabMahasiswaBtn = document.getElementById('tabMahasiswa');
  const tabOrmawaBtn = document.getElementById('tabOrmawa');
  const formSectionMahasiswa = document.getElementById('formSectionMahasiswa');
  const formSectionOrmawa = document.getElementById('formSectionOrmawa');

  const nimInput = document.getElementById('nimInput');
  const birthdateInput = document.getElementById('birthdateInput');
  const checkNimBtn = document.getElementById('checkNimBtn');
  const studentResultBox = document.getElementById('studentResultBox');
  const step2Card = document.getElementById('step2Card');
  const lockedEmailInput = document.getElementById('lockedEmailInput');
  const activateBtn = document.getElementById('activateBtn');
  const activationNimHidden = document.getElementById('activationNimHidden');

  // 1. Tab Switcher (Mahasiswa vs Panitia/Ormawa)
  if (tabMahasiswaBtn && tabOrmawaBtn) {
    tabMahasiswaBtn.addEventListener('click', () => {
      tabMahasiswaBtn.classList.add('active');
      tabOrmawaBtn.classList.remove('active');
      formSectionMahasiswa.style.display = 'block';
      formSectionOrmawa.style.display = 'none';
    });

    tabOrmawaBtn.addEventListener('click', () => {
      tabOrmawaBtn.classList.add('active');
      tabMahasiswaBtn.classList.remove('active');
      formSectionMahasiswa.style.display = 'none';
      formSectionOrmawa.style.display = 'block';
    });
  }

  // 2. Check NIM & Birthdate (Step 1 Verification)
  if (checkNimBtn) {
    checkNimBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const enteredNim = nimInput.value.trim();

      if (!enteredNim) {
        alert('Silakan masukkan NIM Anda terlebih dahulu.');
        nimInput.focus();
        return;
      }

       // Find in the current SIPA dataset
      const match = SIPA_STUDENTS.find(s => s.nim === enteredNim);

      if (match) {
        // Show Success Box
        studentResultBox.style.display = 'flex';
        studentResultBox.innerHTML = `
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <div class="student-details">
            <h4>Mahasiswa Ditemukan: ${match.name}</h4>
            <p>${match.prodi} • Status Akademik: <strong>${match.status}</strong></p>
          </div>
        `;

        // Unlock Step 2
        step2Card.style.opacity = '1';
        step2Card.style.pointerEvents = 'auto';
        step2Card.classList.add('active');
        lockedEmailInput.value = match.email;
        if (activationNimHidden) activationNimHidden.value = match.nim;
        activateBtn.disabled = false;
        
        // Focus to password
        document.getElementById('newPassInput').focus();
      } else {
        studentResultBox.style.display = 'flex';
        studentResultBox.style.background = 'var(--danger-bg)';
        studentResultBox.style.borderColor = 'var(--danger-border)';
        studentResultBox.innerHTML = `
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <div class="student-details">
            <h4 style="color: var(--danger-text);">Data Mahasiswa Tidak Ditemukan</h4>
             <p style="color: #7F1D1D;">NIM "${enteredNim}" belum terdaftar di pangkalan data akademik UMRAH. Pastikan NIM diketik sesuai data kampus.</p>
          </div>
        `;
      }
    });
  }

  // 3. Activation Submit (Step 2)
  const activationForm = document.getElementById('activationForm');
  if (activationForm) {
    activationForm.addEventListener('submit', (e) => {
      if (activationForm.dataset.backendAuth === 'true') return;
      e.preventDefault();
      const p1 = document.getElementById('newPassInput').value;
      const p2 = document.getElementById('confirmPassInput').value;
      const wa = document.getElementById('waInput').value;

      if (p1.length < 8) {
        alert('Kata sandi harus minimal 8 karakter!');
        return;
      }
      if (p1 !== p2) {
        alert('Konfirmasi kata sandi tidak cocok!');
        return;
      }
      if (!wa) {
        alert('Nomor WhatsApp wajib diisi untuk penerimaan tiket & token presensi!');
        return;
      }

      alert(`Selamat! Akun Mahasiswa Anda dengan NIM ${nimInput.value} berhasil diaktivasi.\\n\\nSilakan login menggunakan NIM dan kata sandi baru Anda.`);
      
      // Auto-fill login form
      loginUserInput.value = nimInput.value;
      loginPassInput.value = p1;
      loginUserInput.focus();
    });
  }

  // 4. Login Form Handling
  // Autentikasi produksi ditangani sepenuhnya oleh endpoint Laravel.
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      if (loginForm.dataset.backendAuth === 'true') return;
      e.preventDefault();
      showLoginAlert('Form login harus diproses melalui server TEMU UMRAH.', 'danger');
    });
  }

  function showLoginAlert(msg, type) {
    loginAlert.style.display = 'flex';
    loginAlert.className = `alert-popup alert-${type}`;
    loginAlert.textContent = msg;
  }

});
