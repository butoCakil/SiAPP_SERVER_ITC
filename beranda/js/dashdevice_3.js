function toggleDetails(id) {
    const wrapper = document.getElementById('detail-' + id);
    const arrow = document.getElementById('arrow-' + id);
    const smBtn = document.getElementById('btn-sm-group-' + id);
    // <button id="delete-device" class="btn btn-reboot" style="display: none;">🗑 Delete Device</button>
    const delBtn = document.getElementById('delete-device-' + id);

    wrapper.classList.toggle('show');
    arrow.classList.toggle('rotate');
    smBtn.classList.toggle('hide');
    delBtn.classList.toggle('showThis');
    delBtn.style.display = delBtn.classList.contains('showThis') ? 'inline-block' : 'none';
}

// Fungsi untuk kirim perintah via fetch + alert sukses/gagal
async function sendCommand(url) {
    // Buat objek URL
    const urlObj = new URL(url, window.location.origin); // agar URL relatif tetap bisa
    const params = new URLSearchParams(urlObj.search);

    // Hapus token
    params.delete('token');

    // Ubah param menjadi string "key=value" per baris
    const paramStr = Array.from(params.entries())
        .map(([key, value]) => `${key}=${value}`)
        .join('\n');

    try {
        const response = await fetch(url);
        if (response.ok) {
            alert("✅ Perintah berhasil dikirim!\n" + paramStr);
        } else {
            alert("⚠️ Gagal mengirim perintah ke perangkat.\n" + paramStr);
        }
    } catch (err) {
        alert("❌ Terjadi kesalahan koneksi: " + err.message);
    } finally {
        // Kembali ke halaman dashdevice
        location.href = 'dashdevice.php';
    }
}

// Fungsi interpolasi warna antar level
function getColor(percent) {
    const colors = [
        { stop: 0, color: [255, 0, 0] },       // merah 
        { stop: 20, color: [255, 50, 0] },     // merah menyala
        { stop: 40, color: [255, 150, 0] },    // oranye
        { stop: 60, color: [255, 255, 0] },    // kuning
        { stop: 80, color: [37, 211, 102] },   // hijau WhatsApp
        { stop: 100, color: [0, 170, 255] }    // biru
    ];

    let lower = colors[0], upper = colors[colors.length - 1];
    for (let i = 0; i < colors.length - 1; i++) {
        if (percent >= colors[i].stop && percent <= colors[i + 1].stop) {
            lower = colors[i];
            upper = colors[i + 1];
            break;
        }
    }

    const t = (percent - lower.stop) / (upper.stop - lower.stop);
    const r = Math.round(lower.color[0] + t * (upper.color[0] - lower.color[0]));
    const g = Math.round(lower.color[1] + t * (upper.color[1] - lower.color[1]));
    const b = Math.round(lower.color[2] + t * (upper.color[2] - lower.color[2]));
    return `rgb(${r}, ${g}, ${b})`;
}

// Fungsi update semua RSSI bar
function updateRssiBars() {
    document.querySelectorAll('.rssi-fill').forEach(el => {
        const percent = parseFloat(el.dataset.rssi) || 0;
        el.style.width = percent + "%";
        el.style.backgroundColor = getColor(percent);
    });
}

// Fungsi update waktu terakhir refresh
function updateLastRefresh() {
    const now = new Date();
    const hh = now.getHours().toString().padStart(2, '0');
    const mm = now.getMinutes().toString().padStart(2, '0');
    const ss = now.getSeconds().toString().padStart(2, '0');
    document.getElementById('last-refresh').textContent = `${hh}:${mm}:${ss}`;
}

// Fungsi refresh container device
function refreshDeviceContainer() {
    const overlay = document.getElementById('loading-overlay');
    overlay.style.display = 'flex';

    fetch('device_card.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.text();
        })
        .then(html => {
            document.querySelector('.device-container').innerHTML = html;
            updateRssiBars();      // update RSSI bar setelah HTML baru dimasukkan
            updateLastRefresh();   // update waktu refresh
        })
        .catch(error => {
            console.error('Gagal memuat data:', error);
            document.querySelector('.device-container').innerHTML =
                `<div style="text-align:center; color:red;">❌ Gagal memuat data (${error.message})</div>`;
        })
        .finally(() => {
            overlay.style.display = 'none';
        });
}

// Saat halaman pertama kali dimuat
window.addEventListener('load', () => {
    refreshDeviceContainer();
});

// Auto refresh tiap 60 detik
setInterval(refreshDeviceContainer, 30000);
