// ==========================================================================
// TEMU UMRAH - DIGITAL CERTIFICATE VIEWER CLIENT LOGIC
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
  // 1. Zoom Controls
  let zoomLevel = 100;
  const zoomText = document.getElementById('zoomText');
  const canvasWrapper = document.getElementById('canvasWrapper');

  window.zoomCert = function(delta) {
    zoomLevel = Math.max(60, Math.min(150, zoomLevel + delta));
    applyZoom();
  };

  window.resetZoom = function() {
    zoomLevel = 100;
    applyZoom();
  };

  function applyZoom() {
    if (zoomText) zoomText.textContent = `${zoomLevel}%`;
    if (canvasWrapper) {
      canvasWrapper.style.transform = `scale(${zoomLevel / 100})`;
    }
  }

  // 2. Print Document Handler
  window.printCertificate = function() {
    // Reset zoom to 100% before printing for crisp layout
    resetZoom();
    window.print();
  };

  // 3. Download PDF / Print Handler
  window.downloadPdf = function() {
    if (window.showToast) {
      window.showToast('Membuka dialog cetak & simpan PDF resmi A4 Landscape (Siap Cetak 300 DPI)...', 'info', 'Simpan PDF');
    }
    resetZoom();
    setTimeout(() => {
      window.print();
    }, 300);
  };

  // 4. LinkedIn Certification Share Link
  window.shareLinkedIn = function() {
    const certSerial = document.getElementById('certSerial')?.textContent?.replace('NOMOR SERI RESMI:', '').trim() || 'SERT/UMRAH/2026/001';
    const certEvent = document.getElementById('certEvent')?.textContent?.trim() || 'Seminar & Kegiatan Mahasiswa UMRAH';
    
    const params = new URLSearchParams({
      startTask: 'CERTIFICATION_NAME',
      name: certEvent,
      organizationName: 'Universitas Maritim Raja Ali Haji',
      certId: certSerial,
      certUrl: window.location.href
    });

    const linkedinUrl = `https://www.linkedin.com/profile/add?${params.toString()}`;
    window.open(linkedinUrl, '_blank', 'noopener,noreferrer');
  };
});
