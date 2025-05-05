function detectDeviceType() {
    const ua = navigator.userAgent.toLowerCase();
    if (/tablet|ipad|playbook|silk/.test(ua)) {
        return 'Tablet';
    }
    if (/mobi|android|iphone/.test(ua)) {
        return 'Mobile';
    }
    return 'Desktop';
}

document.addEventListener("DOMContentLoaded", function () {
    const data = {
        page_url: window.location.href,
        referrer: document.referrer || 'Acesso Direto',
        user_agent: navigator.userAgent,
        device_type: detectDeviceType()
    };

    fetch('/monitoramento_estatisticas/public/track', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).catch(error => {
        console.error('Erro ao enviar dados de monitoramento:', error);
    });
});
