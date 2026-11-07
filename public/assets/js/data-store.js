// ==========================================================================
// TEMU UMRAH - SHARED REACTIVE STORE & REALTIME BUS (CROSS-TAB)
// Sinkronisasi Real-time Modul Mahasiswa, Panitia, dan Super Admin BAAK
// ==========================================================================
(function() {
  const BUS_NAME = 'temu_umrah_realtime_bus';
  const bus = typeof BroadcastChannel !== 'undefined' ? new BroadcastChannel(BUS_NAME) : null;

  // 1. DATA PESERTA LOKAL (Audit Webhook & Status Kehadiran)
  const SEED_PESERTA = [
    {
      id: 1, nim: "2401020073", nama: "Nabil", prodi: "Mahasiswa UMRAH", email: "2401020073@student.umrah.ac.id",
      invoice: "#INV-2026-0891", nominal: "Rp 35.000", paymentMethod: "QRIS Dinamis ASPI",
      waktuUpload: "05 Okt 2026, 16.12 WIB", statusBayar: "lunas", statusHadir: "belum", alasanTolak: "",
      strukRef: "SETTLED-QRIS-994182", gatewayStatus: "SETTLEMENT"
    },
    {
      id: 2, nim: "2401020110", nama: "Alman", prodi: "Mahasiswa UMRAH", email: "2401020110@student.umrah.ac.id",
      invoice: "#INV-2026-0892", nominal: "Rp 35.000", paymentMethod: "BCA Virtual Account",
      waktuUpload: "05 Okt 2026, 14.30 WIB", statusBayar: "lunas", statusHadir: "belum", alasanTolak: "",
      strukRef: "SETTLED-VA-887120", gatewayStatus: "SETTLEMENT"
    },
    {
      id: 8, nim: "2401020069", nama: "Balqis", prodi: "Mahasiswa UMRAH", email: "2401020069@student.umrah.ac.id",
      invoice: "#INV-2026-0891", nominal: "Rp 35.000", paymentMethod: "QRIS Dinamis ASPI",
      waktuUpload: "02 Okt 2026, 08.40 WIB", statusBayar: "pending", statusHadir: "belum", alasanTolak: "",
      strukRef: "PENDING-QRIS-102938", gatewayStatus: "PENDING"
    }
  ];

  // 2. DATA SEED ORMAWA (Dengan Batasan Kuota Maksimal 2 Panitia Aktif per Ormawa)
  const DEFAULT_ORMAWA_QUEUE = [
    {
      id: 1,
      nama: "Himpunan Mahasiswa Teknik Maritim (HMTM)",
      kategori: "Himpunan Jurusan (FTTK)",
      ketua: "Muhammad Farhan (NIM: 23050201)",
      pembina: "Dr. Ir. Hendra S., M.T.",
      email: "hmtm@umrah.ac.id",
      noSk: "SK-DEK/FTTK-UMRAH/2026/012",
      tglDaftar: "07 Okt 2026, 14.15 WIB",
      dokumen: "SK_Pengesahan_HMTM_2026.pdf",
      status: "pending",
      kuotaPanitiaMaks: 2,
      panitiaList: [
        { nama: "Muhammad Farhan", nim: "23050201", email: "farhan.m@mhs.umrah.ac.id", role: "Ketua Ormawa" }
      ]
    },
    {
      id: 2,
      nama: "UKM Paduan Suara Gita Bahari",
      kategori: "Unit Kegiatan Mahasiswa (Universitas)",
      ketua: "Clarissa Dewi (NIM: 22010144)",
      pembina: "Dra. Nurhayati, M.Hum.",
      email: "gitabahari@umrah.ac.id",
      noSk: "SK-REK/UMRAH/KM/2026/088",
      tglDaftar: "06 Okt 2026, 10.30 WIB",
      dokumen: "SK_Rektor_Gita_Bahari_2026.pdf",
      status: "pending",
      kuotaPanitiaMaks: 2,
      panitiaList: [
        { nama: "Clarissa Dewi", nim: "22010144", email: "clarissa.d@mhs.umrah.ac.id", role: "Ketua UKM" }
      ]
    },
    {
      id: 3,
      nama: "Komunitas Riset Robotika Bahari",
      kategori: "Komunitas Khusus Fakultas",
      ketua: "Rifqi Zulkarnain (NIM: 24050180)",
      pembina: "Muhamad Radzi Rathomi, M.Cs.",
      email: "robotika@umrah.ac.id",
      noSk: "SK-PRODI/TI-FTTK/2026/005",
      tglDaftar: "05 Okt 2026, 16.45 WIB",
      dokumen: "Surat_Rekomendasi_Robotika.pdf",
      status: "pending",
      kuotaPanitiaMaks: 2,
      panitiaList: [
        { nama: "Rifqi Zulkarnain", nim: "24050180", email: "rifqi.z@mhs.umrah.ac.id", role: "Ketua Komunitas" },
        { nama: "Budi Hermawan", nim: "24050188", email: "budi.h@mhs.umrah.ac.id", role: "Sekretaris" }
      ]
    }
  ];

  // 3. DATA SEED ANTREAN MODERASI EVENT PANITIA (Menunggu Persetujuan Admin)
  const DEFAULT_MODERATION_EVENTS = [
    {
      id: 4,
      title: "Bazar Kreatif Mahasiswa & Expo Inovasi Startup Maritim 2026",
      ormawa: "BEM Fakultas Teknik & Teknologi Kemaritiman",
      category: "Bazar",
      date: "12 Nov 2026",
      time: "08:00 - 17:00 WIB",
      location: "Plaza Rektorat Kampus Dompak",
      quotaMax: 350,
      quotaFilled: 0,
      price: 0,
      priceLabel: "Gratis",
      hasCert: false,
      status: "pending_approval", // Menunggu Persetujuan Admin BAAK
      statusLabel: "Menunggu Persetujuan",
      submittedAt: "08 Okt 2026, 11:20 WIB",
      proposalDoc: "Proposal_Bazar_Inovasi_2026.pdf",
      desc: "Pameran produk kewirausahaan mahasiswa, gelar wicara inkubator bisnis maritim, dan showcase proyek teknologi.",
      catatanAdmin: ""
    }
  ];

  window.TemuStore = {
    // ========================================================================
    // MODUL 1: MANAJEMEN & VERIFIKASI ORMAWA (Anggota 4)
    // ========================================================================
    getOrmawaQueue: function() {
      const stored = localStorage.getItem('temu_ormawa_queue');
      if (!stored) {
        localStorage.setItem('temu_ormawa_queue', JSON.stringify(DEFAULT_ORMAWA_QUEUE));
        return [...DEFAULT_ORMAWA_QUEUE];
      }
      try {
        return JSON.parse(stored);
      } catch (e) {
        return [...DEFAULT_ORMAWA_QUEUE];
      }
    },

    saveOrmawaQueue: function(queue) {
      localStorage.setItem('temu_ormawa_queue', JSON.stringify(queue));
    },

    registerOrmawa: function(ormawaData) {
      const queue = this.getOrmawaQueue();
      ormawaData.kuotaPanitiaMaks = 2;
      ormawaData.panitiaList = ormawaData.panitiaList || [
        { nama: ormawaData.ketua || "Ketua Terdaftar", email: ormawaData.email, role: "Admin Utama" }
      ];
      queue.unshift(ormawaData);
      this.saveOrmawaQueue(queue);
      this.broadcast('ORMAWA_REGISTERED', ormawaData);
      return ormawaData;
    },

    approveOrmawa: function(id) {
      const queue = this.getOrmawaQueue();
      const o = queue.find(item => item.id === id || String(item.id) === String(id));
      if (!o) return null;
      o.status = "approved";
      this.saveOrmawaQueue(queue);
      this.broadcast('ORMAWA_STATUS_CHANGED', { id, status: 'approved', ormawa: o });
      return o;
    },

    rejectOrmawa: function(id, alasan) {
      const queue = this.getOrmawaQueue();
      const o = queue.find(item => item.id === id || String(item.id) === String(id));
      if (!o) return null;
      o.status = "rejected";
      o.alasan = alasan || "Dokumen SK legalitas tidak memenuhi syarat dekanat";
      this.saveOrmawaQueue(queue);
      this.broadcast('ORMAWA_STATUS_CHANGED', { id, status: 'rejected', ormawa: o, alasan });
      return o;
    },

    // Validasi aturan bisnis dosen: Maksimal 2 panitia aktif per Ormawa
    canAddPanitiaToOrmawa: function(ormawaId) {
      const queue = this.getOrmawaQueue();
      const o = queue.find(item => item.id === ormawaId || String(item.id) === String(ormawaId));
      if (!o) return { allowed: false, message: "Organisasi tidak ditemukan." };
      const currentCount = (o.panitiaList || []).length;
      const maxAllowed = o.kuotaPanitiaMaks || 2;
      if (currentCount >= maxAllowed) {
        return {
          allowed: false,
          current: currentCount,
          max: maxAllowed,
          message: `Organisasi "${o.nama}" telah mencapai kuota maksimal ${maxAllowed} panitia aktif sesuai kebijakan BAAK.`
        };
      }
      return { allowed: true, current: currentCount, max: maxAllowed };
    },

    addPanitiaToOrmawa: function(ormawaId, panitiaData) {
      const check = this.canAddPanitiaToOrmawa(ormawaId);
      if (!check.allowed) {
        return { success: false, message: check.message };
      }
      const queue = this.getOrmawaQueue();
      const o = queue.find(item => item.id === ormawaId || String(item.id) === String(ormawaId));
      if (!o) return { success: false, message: 'Organisasi tidak ditemukan.' };
      o.panitiaList = o.panitiaList || [];
      o.panitiaList.push(panitiaData);
      this.saveOrmawaQueue(queue);
      this.broadcast({
        type: 'PANITIA_ADDED_TO_ORMAWA',
        payload: { ormawaId, panitia: panitiaData, total: o.panitiaList.length }
      });
      return { success: true, ormawa: o, total: o.panitiaList.length };
    },

    // ========================================================================
    // MODUL 2: MODERASI EVENT KAMPUS (Anggota 3 Panitia & Anggota 4 Admin)
    // Alur: Panitia Submit (Pending) -> Admin Review (Setujui/Tolak) -> Publik
    // ========================================================================
    getEventsModerationQueue: function() {
      const stored = localStorage.getItem('temu_events_moderation');
      if (!stored) {
        localStorage.setItem('temu_events_moderation', JSON.stringify(DEFAULT_MODERATION_EVENTS));
        return [...DEFAULT_MODERATION_EVENTS];
      }
      try {
        return JSON.parse(stored);
      } catch (e) {
        return [...DEFAULT_MODERATION_EVENTS];
      }
    },

    saveEventsModerationQueue: function(queue) {
      localStorage.setItem('temu_events_moderation', JSON.stringify(queue));
    },

    submitEventForApproval: function(eventData) {
      const queue = this.getEventsModerationQueue();
      eventData.status = 'pending_approval';
      eventData.statusLabel = 'Menunggu Persetujuan';
      eventData.submittedAt = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) +
        ', ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
      
      const existingIdx = queue.findIndex(e => e.id === eventData.id);
      if (existingIdx >= 0) {
        queue[existingIdx] = Object.assign({}, queue[existingIdx], eventData);
      } else {
        queue.unshift(eventData);
      }
      this.saveEventsModerationQueue(queue);

      // Sinkronkan juga ke daftar event panitia lokal
      const panitiaEvents = JSON.parse(localStorage.getItem('temu_panitia_events') || '[]');
      const pIdx = panitiaEvents.findIndex(e => e.id === eventData.id);
      if (pIdx >= 0) {
        panitiaEvents[pIdx] = Object.assign({}, panitiaEvents[pIdx], eventData);
      } else {
        panitiaEvents.unshift(eventData);
      }
      localStorage.setItem('temu_panitia_events', JSON.stringify(panitiaEvents));

      this.broadcast('EVENT_SUBMITTED_FOR_APPROVAL', eventData);
      return eventData;
    },

    approveEvent: function(id) {
      const queue = this.getEventsModerationQueue();
      const ev = queue.find(e => e.id === id || String(e.id) === String(id));
      if (!ev) return null;

      ev.status = 'published';
      ev.statusLabel = 'Publik';
      ev.approvedAt = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) +
        ', ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
      ev.catatanAdmin = '';
      this.saveEventsModerationQueue(queue);

      // Update di panitia events
      const panitiaEvents = JSON.parse(localStorage.getItem('temu_panitia_events') || '[]');
      const pEv = panitiaEvents.find(e => e.id === id || String(e.id) === String(id));
      if (pEv) {
        pEv.status = 'Publik';
        pEv.statusLabel = 'Publik';
        localStorage.setItem('temu_panitia_events', JSON.stringify(panitiaEvents));
      }

      // Masukkan ke daftar publik mahasiswa
      const publicEvents = this.getPublicEvents();
      const pubIdx = publicEvents.findIndex(e => e.id === id || String(e.id) === String(id));
      const formattedPublic = {
        id: ev.id,
        title: ev.title,
        slug: 'event-' + ev.id,
        category: (ev.category || 'SEMINAR').toUpperCase(),
        price: ev.price || 0,
        priceLabel: ev.priceLabel || (ev.price === 0 ? 'GRATIS' : `Rp ${ev.price.toLocaleString('id-ID')}`),
        ormawa: ev.ormawa || 'Organisasi Mahasiswa UMRAH',
        speaker: ev.speaker || 'Narasumber FTTK UMRAH',
        date: ev.date || 'Segera Diumumkan',
        time: ev.time || '09:00 WIB',
        location: ev.location || 'Kampus UMRAH Dompak',
        quotaMax: ev.quotaMax || 100,
        quotaFilled: ev.quotaFilled || 0,
        bannerGradient: ev.gradient || 'linear-gradient(135deg, #003E6D, #005696)',
        iconCode: 'layout',
        desc: ev.desc || 'Kegiatan resmi mahasiswa terverifikasi BAAK UMRAH.',
        benefits: ['E-Sertifikat SKP', 'Materi Kegiatan', 'Networking Mahasiswa']
      };

      if (pubIdx >= 0) {
        publicEvents[pubIdx] = formattedPublic;
      } else {
        publicEvents.unshift(formattedPublic);
      }
      localStorage.setItem('temu_public_events', JSON.stringify(publicEvents));

      this.broadcast('EVENT_APPROVED', { id, event: ev, publicEvent: formattedPublic });
      return ev;
    },

    rejectEvent: function(id, alasan) {
      const queue = this.getEventsModerationQueue();
      const ev = queue.find(e => e.id === id || String(e.id) === String(id));
      if (!ev) return null;

      ev.status = 'rejected';
      ev.statusLabel = 'Ditolak';
      ev.catatanAdmin = alasan || 'Proposal kegiatan belum memenuhi standar kelayakan BAAK.';
      this.saveEventsModerationQueue(queue);

      // Update di panitia events
      const panitiaEvents = JSON.parse(localStorage.getItem('temu_panitia_events') || '[]');
      const pEv = panitiaEvents.find(e => e.id === id || String(e.id) === String(id));
      if (pEv) {
        pEv.status = 'Ditolak';
        pEv.statusLabel = 'Ditolak';
        pEv.catatanAdmin = ev.catatanAdmin;
        localStorage.setItem('temu_panitia_events', JSON.stringify(panitiaEvents));
      }

      this.broadcast('EVENT_REJECTED', { id, event: ev, alasan });
      return ev;
    },

    getPublicEvents: function() {
      const stored = localStorage.getItem('temu_public_events');
      if (stored) {
        try { return JSON.parse(stored); } catch(e) {}
      }
      return [];
    },

    // ========================================================================
    // MODUL 3: PESERTA & GATEWAY AUDIT (Anggota 2)
    // ========================================================================
    getPesertaList: function() {
      const stored = localStorage.getItem('temu_peserta_data');
      if (!stored) {
        localStorage.setItem('temu_peserta_data', JSON.stringify(SEED_PESERTA));
        return [...SEED_PESERTA];
      }
      try {
        return JSON.parse(stored);
      } catch(e) {
        return [...SEED_PESERTA];
      }
    },

    savePesertaList: function(list) {
      localStorage.setItem('temu_peserta_data', JSON.stringify(list));
    },

    addPesertaFromPayment: function(paymentData) {
      const list = this.getPesertaList();
      const newEntry = {
        id: Date.now(),
        nim: paymentData.nim || "",
        nama: paymentData.nama || "Mahasiswa UMRAH",
        prodi: paymentData.prodi || "Mahasiswa UMRAH",
        email: paymentData.email || "",
        invoice: paymentData.invoice || ("#INV-2026-0" + Math.floor(100 + Math.random() * 900)),
        nominal: paymentData.nominal || "Rp 35.000",
        paymentMethod: paymentData.channelDetail || "QRIS Dinamis ASPI",
        waktuUpload: "Hari ini, " + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + " WIB",
        statusBayar: "lunas",
        statusHadir: "belum",
        alasanTolak: "",
        strukRef: paymentData.transactionId || ("SETTLED-PG-" + Math.floor(100000 + Math.random() * 900000)),
        gatewayStatus: "SETTLEMENT"
      };

      const existingIdx = list.findIndex(p => p.nim === newEntry.nim || p.invoice === newEntry.invoice);
      if (existingIdx >= 0) {
        list[existingIdx] = Object.assign({}, list[existingIdx], newEntry);
      } else {
        list.unshift(newEntry);
      }

      this.savePesertaList(list);
      this.broadcast('NEW_PAYMENT_SUBMITTED', newEntry);
      return newEntry;
    },

    recordAttendance: function(studentInfo) {
      const list = this.getPesertaList();
      const p = list.find(item => item.nim === studentInfo.nim);
      if (p) {
        p.statusHadir = "hadir";
        this.savePesertaList(list);
      }
      this.broadcast('ATTENDANCE_CHECKIN', studentInfo);
    },

    updateStudentRegStatus: function(nim, statusPay, statusLabel) {
      const regs = JSON.parse(localStorage.getItem('temu_registrations') || '[]');
      regs.forEach(r => {
        if (r.eventId === 2 || r.ticketNo?.includes('0891') || r.ticketNo?.includes('PENDING')) {
          r.statusPay = statusPay;
          r.statusPayLabel = statusLabel;
          if (statusPay === 'LUNAS') {
            r.ticketNo = 'TIK-UMRAH-20891';
            r.statusPresensi = 'BELUM HADIR (SIAP ABSENSI)';
          }
        }
      });
      localStorage.setItem('temu_registrations', JSON.stringify(regs));
    },

    // ========================================================================
    // EVENT BUS (BroadcastChannel + LocalStorage Fallback)
    // ========================================================================
    broadcast: function(type, payload) {
      if (bus) bus.postMessage({ type, payload, timestamp: Date.now() });
      localStorage.setItem('temu_last_event', JSON.stringify({ type, payload, ts: Date.now() }));
    },

    onEvent: function(callback) {
      if (bus) bus.onmessage = (e) => callback(e.data);
      window.addEventListener('storage', (e) => {
        if (e.key === 'temu_last_event' && e.newValue) {
          try { callback(JSON.parse(e.newValue)); } catch(err) {}
        }
      });
    }
  };
})();
