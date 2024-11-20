// public/js/gps-location.js

document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk mendapatkan lokasi
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Simpan koordinat ke form hidden inputs
                    updateCoordinates(position.coords.latitude, position.coords.longitude);
                },
                function(error) {
                    console.log("Error getting location: ", error);
                }
            );
        }
    }

    // Fungsi untuk update koordinat
    function updateCoordinates(latitude, longitude) {
        // Cari input fields untuk latitude dan longitude
        const latitudeInput = document.querySelector('input[name="latitude"]');
        const longitudeInput = document.querySelector('input[name="longitude"]');
        
        if (latitudeInput && longitudeInput) {
            latitudeInput.value = latitude;
            longitudeInput.value = longitude;
        }
    }

    // Observer untuk memantau perubahan DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    // Cek apakah modal absensi dibuka
                    if (node.classList && (
                        node.classList.contains('fi-modal') || 
                        node.classList.contains('fi-form-component')
                    )) {
                        getLocation();
                    }
                });
            }
        });
    });

    // Konfigurasi observer
    const config = {
        childList: true,
        subtree: true
    };

    // Mulai observasi
    observer.observe(document.body, config);

    // Ambil lokasi saat halaman pertama kali dimuat
    getLocation();

    // Tambahkan event listener untuk modal clock in/out
    document.addEventListener('click', function(e) {
        if (e.target && (
            e.target.closest('[data-action="clock_in"]') || 
            e.target.closest('[data-action="clock_out"]')
        )) {
            setTimeout(getLocation, 500); // Delay sedikit untuk memastikan modal sudah terbuka
        }
    });
});