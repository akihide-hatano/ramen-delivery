document.addEventListener('DOMContentLoaded', function() {
    const deliveryZoneSelect = document.getElementById('delivery_zone_name');
    const estimatedTimeSpan = document.getElementById('estimated-time');

    // HTMLからdata属性で読み取るための変数を定義
    const shopDataElement = document.getElementById('shop-data');
    const deliveryZonesDataElement = document.getElementById('delivery-zones-data');
    const deliveryConfigElement = document.getElementById('delivery-config');

    // ★★★ここから追加★★★
    console.log('--- DOM Element Check ---');
    console.log('deliveryZoneSelect:', deliveryZoneSelect);
    console.log('estimatedTimeSpan:', estimatedTimeSpan);
    console.log('shopDataElement:', shopDataElement);
    console.log('deliveryZonesDataElement:', deliveryZonesDataElement);
    console.log('deliveryConfigElement:', deliveryConfigElement);
    console.log('-------------------------');
    // ★★★ここまで追加★★★

    if (!deliveryZoneSelect || !estimatedTimeSpan || !shopDataElement || !deliveryZonesDataElement || !deliveryConfigElement) {
        console.error('必要なDOM要素が見つかりません。');
        return;
    }

    // ここから、data属性から値を取得
    const shopLat = parseFloat(shopDataElement.dataset.lat);
    const shopLon = parseFloat(shopDataElement.dataset.lon);
    const deliveryZones = JSON.parse(deliveryZonesDataElement.dataset.zones);

    const basePreparationTime = parseFloat(deliveryConfigElement.dataset.basePrepTime);
    const deliverySpeedPerKm = parseFloat(deliveryConfigElement.dataset.deliverySpeedPerKm);
    const peakHours = JSON.parse(deliveryConfigElement.dataset.peakHours);
    const peakSurcharge = parseFloat(deliveryConfigElement.dataset.peakSurcharge);
    const bufferMin = parseFloat(deliveryConfigElement.dataset.bufferMin);
    const bufferMax = parseFloat(deliveryConfigElement.dataset.bufferMax);

    // ★★★ここから追加★★★
    console.log('--- Parsed Data Check ---');
    console.log('shopLat:', shopLat);
    console.log('shopLon:', shopLon);
    console.log('deliveryZones:', deliveryZones);
    console.log('basePreparationTime:', basePreparationTime);
    console.log('deliverySpeedPerKm:', deliverySpeedPerKm);
    console.log('peakHours:', peakHours);
    console.log('peakSurcharge:', peakSurcharge);
    console.log('bufferMin:', bufferMin);
    console.log('bufferMax:', bufferMax);
    console.log('-------------------------');
    // ★★★ここまで追加★★★

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return earthRadius * c;
    }

    function calculateAndDisplayEstimatedTime() {
        const selectedZoneName = deliveryZoneSelect.value;
        if (!selectedZoneName || isNaN(shopLat) || isNaN(shopLon) || !deliveryZones[selectedZoneName]) {
            if (estimatedTimeSpan) {
                estimatedTimeSpan.textContent = '計算できませんでした。';
            }
            return;
        }

        const destLat = deliveryZones[selectedZoneName].latitude;
        const destLon = deliveryZones[selectedZoneName].longitude;

        let estimatedTime = basePreparationTime + (calculateDistance(shopLat, shopLon, destLat, destLon) * deliverySpeedPerKm);

        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();

        peakHours.forEach(period => {
            const [startStr, endStr] = period.split('-');
            const [startHour, startMinute] = startStr.split(':').map(Number);
            const [endHour, endMinute] = endStr.split(':').map(Number);

            const startTimeInMinutes = startHour * 60 + startMinute;
            const endTimeInMinutes = endHour * 60 + endMinute;
            const currentTimeInMinutes = currentHour * 60 + currentMinute;

            // 終了時刻が00:00の場合 (翌日の0時) を考慮
            if (endTimeInMinutes === 0) {
                if (currentTimeInMinutes >= startTimeInMinutes || currentTimeInMinutes <= endTimeInMinutes) {
                    estimatedTime += peakSurcharge;
                }
            } else if (currentTimeInMinutes >= startTimeInMinutes && currentTimeInMinutes <= endTimeInMinutes) {
                estimatedTime += peakSurcharge;
            }
        });

        estimatedTime += Math.floor(Math.random() * (bufferMax - bufferMin + 1)) + bufferMin;

        if (estimatedTimeSpan) {
            estimatedTimeSpan.textContent = Math.round(estimatedTime);
        }
    }

    deliveryZoneSelect.addEventListener('change', calculateAndDisplayEstimatedTime);
    calculateAndDisplayEstimatedTime();
});