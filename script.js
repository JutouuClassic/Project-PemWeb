// ==========================================
// File: script.js - Sistem E-BPJS Antrean
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    // --- 1. REAL-TIME JAM & TANGGAL SISTEM ---
    const updateRealtimeClock = () => {
        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            clockEl.textContent = timeString;
        }
    };
    setInterval(updateRealtimeClock, 1000);
    updateRealtimeClock();

    // --- 2. VALIDASI INPUT ANGKA SAJA (NIK & NO BPJS) ---
    const numericInputs = document.querySelectorAll('input[name="nik"], input[name="no_bpjs"]');
    numericInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    });

    // Validasi panjang NIK wajib 16 digit saat form submit
    const formsWithNik = document.querySelectorAll('form');
    formsWithNik.forEach(form => {
        const nikInput = form.querySelector('input[name="nik"]');
        if (nikInput) {
            form.addEventListener('submit', (e) => {
                if (nikInput.value && nikInput.value.length !== 16) {
                    e.preventDefault();
                    alert('NIK wajib berjumlah tepat 16 digit angka!');
                    nikInput.focus();
                }
            });
        }
    });

    // --- 3. AUTO-DISMISS ALERT NOTIFIKASI SETELAH 4 DETIK ---
    const autoAlerts = document.querySelectorAll('.alert-dismissible');
    autoAlerts.forEach(alertEl => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 4000);
    });

    // --- 4. HIGHLIGHT INTERAKTIF CARD OPSI POLI ---
    const poliRadios = document.querySelectorAll('input[name="poli_tujuan"]');
    const updatePoliSelection = () => {
        poliRadios.forEach(radio => {
            const card = radio.closest('.poli-option');
            if (card) {
                if (radio.checked) {
                    card.classList.add('border-success', 'bg-light');
                    card.style.boxShadow = '0 0 0 2px rgba(0, 155, 76, 0.4)';
                } else {
                    card.classList.remove('border-success', 'bg-light');
                    card.style.boxShadow = 'none';
                }
            }
        });
    };
    poliRadios.forEach(radio => {
        radio.addEventListener('change', updatePoliSelection);
    });
    updatePoliSelection();

    // --- 5. SEARCH FILTER DI TABEL DATA ANGGOTA (ADMIN) ---
    const searchAnggota = document.getElementById('searchAnggota');
    if (searchAnggota) {
        searchAnggota.addEventListener('keyup', function () {
            const keyword = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableAnggota tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    }

    // --- 6. AUTO REFRESH MONITOR ANTREAN (Optional polling tiap 30 detik di index) ---
    const isLiveMonitor = document.querySelector('.live-monitor-badge');
    if (isLiveMonitor && !document.querySelector('form textarea:focus')) {
        setTimeout(() => {
            // Refresh halus jika tidak sedang mengisi input
            const activeElement = document.activeElement;
            const isTyping = activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA');
            if (!isTyping) {
                window.location.reload();
            }
        }, 30000);
    }
});
