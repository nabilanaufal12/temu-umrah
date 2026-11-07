/**
 * TEMU UMRAH - PAYMENT GATEWAY CLIENT LOGIC (ANGGOTA 2)
 * Simulasi Webhook Callback & REST API Payment Gateway Resmi
 * Mengakomodasi QRIS Dinamis, Virtual Account, & E-Wallet
 * Sesuai Catatan Dosen: ZERO Manual Upload / ZERO Manual Approval
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Dapatkan Konteks Event dari URL Query (?event=X)
  const urlParams = new URLSearchParams(window.location.search);
  const eventId = parseInt(urlParams.get('event') || '2');

  const currentEvent = window.TEMU_PAYMENT_EVENT;
  if (!currentEvent) return;

  // Elemen DOM Invoice
  const invOrderTag = document.getElementById('invOrderTag');
  const invEventTitle = document.getElementById('invEventTitle');
  const invOrganizer = document.getElementById('invOrganizer');
  const invStudentName = document.getElementById('invStudentName');
  const invStudentEmail = document.getElementById('invStudentEmail');
  const invTicketPrice = document.getElementById('invTicketPrice');
  const invTotalAmount = document.getElementById('invTotalAmount');
  const qrisAmountLabel = document.getElementById('qrisAmountLabel');
  const vaAmountLabel = document.getElementById('vaAmountLabel');

  // Ambil Data Sesi Pengguna Aktif
  const user = window.TEMU_PAYMENT_USER || (typeof window.getCurrentUser === 'function' ? window.getCurrentUser() : null) ||
                JSON.parse(localStorage.getItem('temu_user') || 'null') || {
                  name: 'Mahasiswa UMRAH',
                  nim: '',
                  email: ''
               };

  const formattedPrice = 'Rp ' + Number(currentEvent.price || 35000).toLocaleString('id-ID') + ',-';
  const orderId = '#ORD-2026-08' + currentEvent.id;

  if (invOrderTag) invOrderTag.textContent = `ORDER ID: ${orderId}`;
  if (invEventTitle) invEventTitle.textContent = currentEvent.title;
  if (invOrganizer) invOrganizer.textContent = currentEvent.organizer || 'Ormawa FTTK UMRAH';
   if (invStudentName) invStudentName.textContent = `${user.name || user.nama || 'Mahasiswa UMRAH'} (NIM: ${user.nim || '-'})`;
   if (invStudentEmail) invStudentEmail.textContent = user.email || '-';
  if (invTicketPrice) invTicketPrice.textContent = formattedPrice;
  if (invTotalAmount) invTotalAmount.textContent = formattedPrice;
  if (qrisAmountLabel) qrisAmountLabel.textContent = formattedPrice;
  if (vaAmountLabel) vaAmountLabel.textContent = formattedPrice;

  // 2. Realtime Countdown Timer Transaksi (14 menit 59 detik)
  let totalSeconds = 14 * 60 + 59;
  const hoursBox = document.getElementById('timerHours');
  const minsBox = document.getElementById('timerMins');
  const secsBox = document.getElementById('timerSecs');

  function updateTimer() {
    if (totalSeconds <= 0) {
      clearInterval(timerInterval);
      if (window.showToast) {
        window.showToast('Waktu sesi transaksi gateway telah habis. Silakan checkout ulang.', 'warning', 'Sesi Kedaluwarsa');
      }
      return;
    }

    const h = Math.floor(totalSeconds / 3600);
    const m = Math.floor((totalSeconds % 3600) / 60);
    const s = totalSeconds % 60;

    if (hoursBox) hoursBox.textContent = String(h).padStart(2, '0');
    if (minsBox) minsBox.textContent = String(m).padStart(2, '0');
    if (secsBox) secsBox.textContent = String(s).padStart(2, '0');

    totalSeconds--;
  }

  const timerInterval = setInterval(updateTimer, 1000);
  updateTimer();

  // 3. Channel Switcher (QRIS / VA / E-Wallet)
  window.switchChannel = function(channel, btn) {
    document.querySelectorAll('.channel-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.channel-panel').forEach(p => p.classList.remove('active'));

    btn.classList.add('active');
    const targetPanel = document.getElementById(`panel-${channel}`);
    if (targetPanel) targetPanel.classList.add('active');
  };

  // 4. VA Selector & Copy Text Helper
  window.selectVA = function(bank, vaNumber, element) {
    document.querySelectorAll('.va-item-option').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    if (window.showToast) {
      window.showToast(`Virtual Account ${bank} dipilih: ${vaNumber}`, 'info', 'Kanal Pembayaran');
    }
  };

  window.copyVAText = function(text, event) {
    if (event) event.stopPropagation();
    if (typeof window.copyToClipboard === 'function') {
      window.copyToClipboard(text, 'Nomor Virtual Account');
    } else if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(() => {
        if (window.showToast) window.showToast('Nomor Virtual Account disalin!', 'success');
      });
    }
  };

  // 5. Eksekusi Pembayaran via Simulasi Webhook API Payment Gateway
  // Memenuhi RPS Anggota 2: Fetch API / async/await / respons sistem pembayaran
  window.executeGatewayPayment = async function(channelType) {
    const channelNames = {
      qris: 'QRIS Dinamis (ASPI / Bank Indonesia)',
      virtual_account: 'BCA Virtual Account (8801200000000)',
      ewallet: 'GoPay Direct Gateway'
    };

    const chosenChannel = channelNames[channelType] || 'QRIS Dinamis';
    const btnPay = document.getElementById(channelType === 'qris' ? 'btnPayQRIS' : (channelType === 'virtual_account' ? 'btnPayVA' : 'btnPayEwallet'));
    const origText = btnPay ? btnPay.innerHTML : '';

    if (btnPay) {
      btnPay.disabled = true;
      btnPay.innerHTML = `
        <svg class="icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
        <span>Menghubungkan ke API Payment Gateway...</span>
      `;
    }

    if (window.showToast) {
      window.showToast('Menghubungi server payment gateway...', 'info', 'Proses Transaksi');
    }

    // Simulasi Network Request Async/Await (800ms)
    await new Promise(resolve => setTimeout(resolve, 800));

    // Call server settlement API to update database and notifications
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="_token"]')?.value || '';
    
    let serverData = null;
    try {
      const settleUrl = window.TEMU_PAYMENT_SETTLE_URL || '/pembayaran/settle';
      const res = await fetch(settleUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          invoice: currentEvent.invoice || currentEvent.order_id || orderId,
          channel: chosenChannel
        })
      });
      if (res.ok) {
        serverData = await res.json();
      }
    } catch (err) {
      console.warn('Server settle call fallback:', err);
    }

    // Response Payload dari Gateway
    const gatewayResponse = {
      status_code: '200',
      status_message: 'Success, transaction is found and settled',
      transaction_id: (serverData && serverData.transaction_id) ? serverData.transaction_id : ('TRX-MIDTRANS-' + Date.now().toString().slice(-6)),
      order_id: (serverData && serverData.order_id) ? serverData.order_id : (currentEvent.invoice || orderId),
      gross_amount: currentEvent.price || 25000,
      payment_type: channelType,
      transaction_status: 'settlement',
      fraud_status: 'accept',
      settlement_time: (serverData && serverData.settlement_time) ? serverData.settlement_time : (new Date().toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) + ' WIB')
    };

    // 6. Simpan Status Transaksi Lunas di LocalStorage
    // Sesuai Persyaratan: status transaksi ditampilkan sesuai respons payment gateway
    const payments = JSON.parse(localStorage.getItem('temu_payments') || '[]');
    payments.unshift({
      orderId: gatewayResponse.order_id,
      transactionId: gatewayResponse.transaction_id,
      eventId: currentEvent.id,
      eventTitle: currentEvent.title,
      amount: gatewayResponse.gross_amount,
      channel: chosenChannel,
      time: gatewayResponse.settlement_time,
      status: 'SETTLEMENT',
      statusLabel: 'LUNAS (PAYMENT GATEWAY)'
    });
    localStorage.setItem('temu_payments', JSON.stringify(payments));

    // Perbarui atau Daftarkan ke Koleksi Event Saya (Registrations)
    const regs = JSON.parse(localStorage.getItem('temu_registrations') || '[]');
    const existingIdx = regs.findIndex(r => r.eventId === currentEvent.id);
    const newRegData = {
      eventId: currentEvent.id,
      eventTitle: currentEvent.title,
      ticketNo: 'TIK-FTTK-' + Math.floor(100000 + Math.random() * 900000),
      date: currentEvent.date || '15 Oktober 2026',
      location: currentEvent.location || 'Lab Komputer 3 FTTK Kampus Dompak',
      status: 'TERDAFTAR',
      price: currentEvent.price || 35000,
      paymentStatus: 'LUNAS',
      paymentMethod: chosenChannel,
      paymentRef: gatewayResponse.transaction_id,
      paidAt: gatewayResponse.settlement_time
    };

    if (existingIdx >= 0) {
      regs[existingIdx] = newRegData;
    } else {
      regs.unshift(newRegData);
    }
    localStorage.setItem('temu_registrations', JSON.stringify(regs));

    // Sinkronisasi ke Toko Data Panitia (window.TemuStore)
    if (window.TemuStore && typeof window.TemuStore.addPesertaFromPayment === 'function') {
      window.TemuStore.addPesertaFromPayment({
         nim: user.nim || '',
         nama: user.name || user.nama || 'Mahasiswa UMRAH',
         prodi: user.prodi || 'Mahasiswa UMRAH',
         email: user.email || '',
        invoice: gatewayResponse.order_id,
        nominal: 'Rp ' + (currentEvent.price || 35000).toLocaleString('id-ID'),
        bankPengirim: chosenChannel,
        status: 'LUNAS (PAYMENT GATEWAY)'
      });
    }

    // 7. Update Status Tampilan Invoice di Layar
    const badge = document.getElementById('invStatusBadge');
    if (badge) {
      badge.className = 'invoice-status-badge paid';
      badge.innerHTML = `
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span>Lunas (Payment Gateway)</span>
      `;
    }

    // 8. Tampilkan Modal Digital Receipt Sukses
    document.getElementById('rcptOrderId').textContent = gatewayResponse.order_id;
    document.getElementById('rcptTrxId').textContent = gatewayResponse.transaction_id;
    document.getElementById('rcptEventTitle').textContent = currentEvent.title;
    document.getElementById('rcptChannel').textContent = chosenChannel;
    document.getElementById('rcptAmount').textContent = formattedPrice;
    document.getElementById('rcptTimestamp').textContent = gatewayResponse.settlement_time;

    const modal = document.getElementById('receiptModal');
    if (modal) modal.classList.add('active');

    if (window.showToast) {
      window.showToast('Pembayaran berhasil diverifikasi secara otomatis oleh Payment Gateway!', 'success', 'Transaksi Selesai');
    }

    if (btnPay) {
      btnPay.disabled = false;
      btnPay.innerHTML = origText;
    }
  };
});
