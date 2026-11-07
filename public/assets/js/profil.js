/* ==========================================================================
   TEMU UMRAH - MODERN PROFILE & SECURITY SCRIPT
   Harmonized with AgroPulse UI/UX Design System
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {
  loadUserProfile();

  const newPwdInput = document.getElementById('inputPwdBaru');
  if (newPwdInput) {
    newPwdInput.addEventListener('input', checkPasswordStrength);
  }

  // Auto switch tab if URL has ?tab=sandi or #sandi
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('tab') === 'sandi' || window.location.hash === '#sandi') {
    switchProfilTab('sandi');
  }

  // Keyboard accessibility: ESC closes modal & dropdown
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeLogoutModal();
      closeAvatarDropdown();
    }
  });

  // Close avatar dropdown when clicking outside
  document.addEventListener('click', function(e) {
    const anchor = document.querySelector('.avatar-edit-anchor');
    if (anchor && !anchor.contains(e.target)) {
      closeAvatarDropdown();
    }
  });
});

/**
 * Load user identity into header, sidebar, and inputs
 */
function loadUserProfile() {
  const sess = window.TEMU_PROFILE_USER;
  if (!sess) return;

  const nama = sess.nama || sess.name || 'Mahasiswa UMRAH';
  const nim = sess.nim || '-';
  const noWa = sess.no_wa || '';
  const role = sess.role || 'mahasiswa';

  const elNamaHdr = document.getElementById('profilNamaHeading');
  if (elNamaHdr) elNamaHdr.textContent = nama;

  const elNimHdr = document.getElementById('profilNimHeading');
  if (elNimHdr) {
    elNimHdr.textContent = `NIM: ${nim}`;
  }

  const elInputHp = document.getElementById('inputHp');
  if (elInputHp && !elInputHp.value) {
    elInputHp.value = noWa;
  }
}

/**
 * Dropdown Avatar Edit Popover Controls
 */
function toggleAvatarDropdown(e) {
  if (e) e.stopPropagation();
  const menu = document.getElementById('avatarPopoverMenu');
  const btn = document.getElementById('btnToggleAvatarMenu');
  if (!menu) return;

  const isOpen = menu.style.display !== 'none';
  if (isOpen) {
    closeAvatarDropdown();
  } else {
    menu.style.display = 'flex';
    if (btn) btn.setAttribute('aria-expanded', 'true');
  }
}

function closeAvatarDropdown() {
  const menu = document.getElementById('avatarPopoverMenu');
  const btn = document.getElementById('btnToggleAvatarMenu');
  if (menu) menu.style.display = 'none';
  if (btn) btn.setAttribute('aria-expanded', 'false');
}

function triggerFotoUpload() {
  closeAvatarDropdown();
  const input = document.getElementById('fotoProfilInput');
  if (input) input.click();
}

function triggerFotoDelete() {
  closeAvatarDropdown();
  handleFotoDelete();
}

/**
 * Switch tabs between Informasi Akun and Ubah Sandi
 */
function switchProfilTab(tabName) {
  // Update Tab Buttons
  document.querySelectorAll('.sipa-tab-btn, .p-tab-btn').forEach(btn => {
    btn.classList.remove('active');
    btn.setAttribute('aria-selected', 'false');
  });
  const activeBtn = document.getElementById('tabBtn_' + tabName);
  if (activeBtn) {
    activeBtn.classList.add('active');
    activeBtn.setAttribute('aria-selected', 'true');
  }

  // Toggle Tab Panes
  document.querySelectorAll('.profil-tab-pane').forEach(pane => {
    pane.style.display = 'none';
  });
  const activePane = document.getElementById('tabPane_' + tabName);
  if (activePane) {
    activePane.style.display = 'block';
  }
}

/**
 * Toggle password visibility between text and password
 */
function togglePasswordVisibility(inputId, btnEl) {
  const input = document.getElementById(inputId);
  if (!input) return;

  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';

  if (isPassword) {
    btnEl.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="23" x2="23" y2="1"/></svg>';
    btnEl.title = 'Sembunyikan kata sandi';
  } else {
    btnEl.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    btnEl.title = 'Tampilkan kata sandi';
  }
}

/**
 * Realtime Password Strength Meter
 */
function checkPasswordStrength() {
  const val = document.getElementById('inputPwdBaru')?.value || '';
  const bar = document.getElementById('pwdMeterFill');
  const txt = document.getElementById('pwdStrengthText');

  if (!bar || !txt) return;

  if (val.length === 0) {
    bar.style.width = '0%';
    bar.style.background = '#e2e8f0';
    txt.textContent = '';
    return;
  }

  let score = 0;
  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  if (score <= 1) {
    bar.style.width = '25%';
    bar.style.background = '#ef4444';
    txt.textContent = 'Kekuatan: Lemah (Gunakan minimal 8 karakter dengan angka & huruf)';
    txt.style.color = '#ef4444';
  } else if (score === 2) {
    bar.style.width = '55%';
    bar.style.background = '#f59e0b';
    txt.textContent = 'Kekuatan: Cukup Baik (Tambahkan huruf besar atau simbol)';
    txt.style.color = '#d97706';
  } else if (score === 3) {
    bar.style.width = '80%';
    bar.style.background = '#10b981';
    txt.textContent = 'Kekuatan: Kuat (Kata sandi memenuhi standar keamanan)';
    txt.style.color = '#059669';
  } else {
    bar.style.width = '100%';
    bar.style.background = 'linear-gradient(90deg, #10b981, #059669)';
    txt.textContent = 'Kekuatan: Sangat Aman & Terlindungi!';
    txt.style.color = '#047857';
  }
}

/**
 * Save WhatsApp / Contact Number via AJAX
 */
function simpanProfilKontak(e) {
  e.preventDefault();
  const hpInput = document.getElementById('inputHp');
  const hp = hpInput ? hpInput.value.trim() : '';

  if (!hp) {
    showProfilToast('Nomor WhatsApp wajib diisi.', 'error');
    return;
  }

  if (hp.length < 10) {
    showProfilToast('Nomor WhatsApp minimal 10 digit.', 'warning');
    return;
  }

  const btn = document.getElementById('btnSubmitKontak');
  const origText = btn ? btn.innerHTML : '';
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> Menyimpan...';
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                    document.querySelector('input[name="_token"]')?.value || '';

  fetch('/profil/update-kontak', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ 
      no_hp: hp
    })
  })
  .then(res => res.json().then(data => ({ status: res.status, body: data })))
  .then(({ status, body }) => {
    if (status >= 200 && status < 300 && body.status === 'success') {
      showProfilToast(body.message || 'Nomor WhatsApp berhasil diperbarui.', 'success');

      if (window.TEMU_PROFILE_USER) {
        window.TEMU_PROFILE_USER.no_wa = hp;
      }
    } else {
      const errMsg = body.message || (body.errors && Object.values(body.errors).flat().join(', ')) || 'Terjadi kesalahan saat menyimpan nomor kontak.';
      showProfilToast(errMsg, 'error');
    }
  })
  .catch(err => {
    console.error('Update contact error:', err);
    showProfilToast('Koneksi terputus. Silakan coba kembali sesaat lagi.', 'error');
  })
  .finally(() => {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = origText;
    }
  });
}

// Backward compatibility alias
function simpanKontakBio(e) {
  return simpanProfilKontak(e);
}

/**
 * Update Password via AJAX
 */
function ubahKataSandi(e) {
  e.preventDefault();
  const oldPwd = document.getElementById('inputPwdLama').value;
  const newPwd = document.getElementById('inputPwdBaru').value;
  const confPwd = document.getElementById('inputPwdKonfirmasi').value;

  if (!oldPwd || !newPwd || !confPwd) {
    showProfilToast('Mohon lengkapi seluruh kolom kata sandi.', 'warning');
    return;
  }

  if (newPwd.length < 8) {
    showProfilToast('Kata sandi baru minimal 8 karakter.', 'warning');
    return;
  }

  if (newPwd !== confPwd) {
    showProfilToast('Konfirmasi kata sandi tidak cocok. Mohon teliti kembali.', 'error');
    return;
  }

  const btn = document.getElementById('btnSubmitPassword');
  const origText = btn ? btn.innerHTML : '';
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> Memperbarui...';
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  fetch('/profil/update-password', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      password_lama: oldPwd,
      password_baru: newPwd,
      password_baru_confirmation: confPwd
    })
  })
  .then(res => res.json().then(data => ({ status: res.status, body: data })))
  .then(({ status, body }) => {
    if (status >= 200 && status < 300 && body.status === 'success') {
      showProfilToast(body.message || 'Kata sandi berhasil diperbarui!', 'success');
      document.getElementById('formGantiPwd').reset();
      checkPasswordStrength();
    } else {
      const errMsg = body.message || (body.errors && Object.values(body.errors).flat().join(', ')) || 'Gagal mengubah kata sandi.';
      showProfilToast(errMsg, 'error');
    }
  })
  .catch(err => {
    console.error('Update password error:', err);
    showProfilToast('Gagal menghubungi server. Silakan coba kembali.', 'error');
  })
  .finally(() => {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = origText;
    }
  });
}

/**
 * Logout Modal Controls
 */
function openLogoutModal() {
  const modal = document.getElementById('modalLogout');
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeLogoutModal() {
  const modal = document.getElementById('modalLogout');
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

/**
 * Modern AgroPulse Floating Toast
 */
function showProfilToast(msg, type) {
  type = type || 'success';
  if (typeof window.showToast === 'function') {
    const title = type === 'success' ? 'Berhasil' : (type === 'error' ? 'Peringatan' : 'Informasi');
    window.showToast(msg, type, title);
    return;
  }

  const toast = document.createElement('div');
  const isErr = type === 'error';
  const isWarn = type === 'warning';

  const bgColor = isErr ? '#e11d48' : (isWarn ? '#d97706' : '#0f172a');
  const iconSvg = isErr
    ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
    : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';

  toast.style.cssText = 'position:fixed;bottom:2rem;right:2rem;background:' + bgColor + ';color:#ffffff;padding:0.9rem 1.4rem;border-radius:999px;box-shadow:0 14px 35px rgba(15,23,42,0.25);z-index:10000;font-size:0.88rem;font-weight:700;display:inline-flex;align-items:center;gap:0.75rem;max-width:440px;transform:translateY(20px);opacity:0;transition:all 0.3s cubic-bezier(0.16,1,0.3,1);';
  toast.innerHTML = iconSvg + '<span>' + msg + '</span>';

  document.body.appendChild(toast);

  requestAnimationFrame(() => {
    toast.style.transform = 'translateY(0)';
    toast.style.opacity = '1';
  });

  setTimeout(() => {
    toast.style.transform = 'translateY(20px)';
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

/**
 * Handle Profile Photo Upload via AJAX
 */
function handleFotoUpload(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];

  const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
  if (!validTypes.includes(file.type)) {
    showProfilToast('Format berkas harus gambar (JPG, PNG, atau WEBP).', 'warning');
    input.value = '';
    return;
  }

  if (file.size > 3 * 1024 * 1024) {
    showProfilToast('Ukuran foto maksimal adalah 3 MB.', 'warning');
    input.value = '';
    return;
  }

  const reader = new FileReader();
  reader.onload = function(e) {
    const avatarImg = document.getElementById('avatarLargeImg');
    const avatarInit = document.getElementById('avatarLargeInitial');
    if (avatarImg) {
      avatarImg.src = e.target.result;
      avatarImg.style.display = 'block';
    }
    if (avatarInit) {
      avatarInit.style.display = 'none';
    }
  };
  reader.readAsDataURL(file);

  const formData = new FormData();
  formData.append('foto', file);

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                    document.querySelector('input[name="_token"]')?.value || '';

  showProfilToast('Sedang mengunggah foto profil...', 'info');

  fetch('/profil/update-foto', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(res => res.json().then(data => ({ status: res.status, body: data })))
  .then(({ status, body }) => {
    if (status >= 200 && status < 300 && body.status === 'success') {
      showProfilToast(body.message || 'Foto profil berhasil diperbarui!', 'success');
      
      const avatarImg = document.getElementById('avatarLargeImg');
      if (avatarImg && body.avatar_url) {
        avatarImg.src = body.avatar_url;
        avatarImg.style.display = 'block';
      }
      const avatarInit = document.getElementById('avatarLargeInitial');
      if (avatarInit) avatarInit.style.display = 'none';

      const btnHapus = document.getElementById('popoverBtnHapus');
      if (btnHapus) btnHapus.style.display = 'flex';

      const navImg = document.getElementById('navbarAvatarImg');
      const navInit = document.getElementById('navbarAvatarInitial');
      if (navImg && body.avatar_url) {
        navImg.src = body.avatar_url;
        navImg.classList.remove('user-avatar-hidden');
      }
      if (navInit) {
        navInit.classList.add('user-avatar-hidden');
      }

      if (window.TEMU_PROFILE_USER) {
        window.TEMU_PROFILE_USER.foto = body.avatar_url;
        window.TEMU_PROFILE_USER.avatar_url = body.avatar_url;
      }
    } else {
      const errMsg = body.message || (body.errors && Object.values(body.errors).flat().join(', ')) || 'Gagal mengunggah foto profil.';
      showProfilToast(errMsg, 'error');
    }
  })
  .catch(err => {
    console.error('Upload foto error:', err);
    showProfilToast('Koneksi terputus saat mengunggah foto.', 'error');
  })
  .finally(() => {
    input.value = '';
  });
}

/**
 * Handle Profile Photo Delete (Reset to initials)
 */
function handleFotoDelete() {
  if (!confirm('Apakah Anda yakin ingin menghapus foto profil dan kembali menggunakan inisial nama?')) {
    return;
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                    document.querySelector('input[name="_token"]')?.value || '';

  fetch('/profil/delete-foto', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    }
  })
  .then(res => res.json().then(data => ({ status: res.status, body: data })))
  .then(({ status, body }) => {
    if (status >= 200 && status < 300 && body.status === 'success') {
      showProfilToast(body.message || 'Foto profil berhasil dihapus.', 'success');

      const avatarImg = document.getElementById('avatarLargeImg');
      const avatarInit = document.getElementById('avatarLargeInitial');
      if (avatarImg) {
        avatarImg.src = '';
        avatarImg.style.display = 'none';
      }
      if (avatarInit) {
        avatarInit.textContent = body.initial || 'M';
        avatarInit.style.display = 'flex';
      }

      const btnHapus = document.getElementById('popoverBtnHapus');
      if (btnHapus) btnHapus.style.display = 'none';

      const navImg = document.getElementById('navbarAvatarImg');
      const navInit = document.getElementById('navbarAvatarInitial');
      if (navImg) {
        navImg.src = '';
        navImg.classList.add('user-avatar-hidden');
      }
      if (navInit) {
        navInit.textContent = body.initial || 'M';
        navInit.classList.remove('user-avatar-hidden');
      }

      if (window.TEMU_PROFILE_USER) {
        window.TEMU_PROFILE_USER.foto = null;
        window.TEMU_PROFILE_USER.avatar_url = null;
      }
    } else {
      showProfilToast(body.message || 'Gagal menghapus foto profil.', 'error');
    }
  })
  .catch(err => {
    console.error('Delete foto error:', err);
    showProfilToast('Terjadi kesalahan saat menghapus foto.', 'error');
  });
}
