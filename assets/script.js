document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-location');
    if (!btn) return;

    btn.addEventListener('click', () => {
        if (!('geolocation' in navigator)) {
            alert('Seu navegador não suporta geolocalização.');
            return;
        }

        // Feedback visual enquanto busca
        btn.disabled = true;
        btn.textContent = '⏳ Buscando localização...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                // Redireciona para a mesma página com lat/lon
                const url = new URL(window.location.href);
                url.searchParams.set('lat', lat);
                url.searchParams.set('lon', lon);
                window.location.href = url.toString();
            },
            (error) => {
                btn.disabled = false;
                btn.textContent = '📍 Usar minha localização';

                let msg = 'Não foi possível obter sua localização.';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        msg = 'Permissão de localização negada.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        msg = 'Informação de localização indisponível.';
                        break;
                    case error.TIMEOUT:
                        msg = 'Tempo esgotado ao buscar localização.';
                        break;
                }
                alert(msg);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    });
});