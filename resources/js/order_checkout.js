document.addEventListener('DOMContentLoaded', function() {
    const deliveryZoneSelect = document.getElementById('delivery_zone_name');
    const desiredTimeSelect = document.getElementById('desired_delivery_time_slot'); // 希望配達時間スロットのselect要素
    const estimatedTimeDisplay = document.getElementById('estimated-time-display'); // 予測配達時間表示用のspan要素
    const earliestArrivalMessage = document.getElementById('earliest-arrival-message'); // 最短到着時刻メッセージ表示用のp要素

    // HTMLのdata属性からデータを読み取るための要素
    const shopDataElement = document.getElementById('shop-data');
    const deliveryZonesDataElement = document.getElementById('delivery-zones-data');
    const deliveryConfigElement = document.getElementById('delivery-config');

    // ★★★DOM要素の存在チェック★★★
    // 必要な全てのDOM要素が取得できているかを確認します。
    // どれか一つでもnullであれば、エラーをコンソールに出力し、処理を中断します。
    if (!deliveryZoneSelect || !desiredTimeSelect || !estimatedTimeDisplay || !earliestArrivalMessage || !shopDataElement || !deliveryZonesDataElement || !deliveryConfigElement) {
        console.error('必要なDOM要素が見つかりません。ページ構造を確認してください。');
        // ユーザーには表示されないエラーメッセージですが、開発者向けに詳細を出力
        console.error('deliveryZoneSelect:', deliveryZoneSelect);
        console.error('desiredTimeSelect:', desiredTimeSelect);
        console.error('estimatedTimeDisplay:', estimatedTimeDisplay);
        console.error('earliestArrivalMessage:', earliestArrivalMessage);
        console.error('shopDataElement:', shopDataElement);
        console.error('deliveryZonesDataElement:', deliveryZonesDataElement);
        console.error('deliveryConfigElement:', deliveryConfigElement);
        return;
    }

    // ★★★data属性から値を取得★★★
    // HTMLに埋め込まれたdata属性から、JavaScriptで利用する設定値やデータを取得します。
    // parseFloatは文字列を数値に変換します。変換できない場合はNaNになります。
    const shopLat = parseFloat(shopDataElement.dataset.lat);
    const shopLon = parseFloat(shopDataElement.dataset.lon);
    // JSON.parseはJSON文字列をJavaScriptオブジェクトに変換します。
    const deliveryZones = JSON.parse(deliveryZonesDataElement.dataset.zones);

    const basePreparationTime = parseFloat(deliveryConfigElement.dataset.basePrepTime);
    const deliverySpeedPerKm = parseFloat(deliveryConfigElement.dataset.deliverySpeedPerKm);
    const peakHours = JSON.parse(deliveryConfigElement.dataset.peakHours);
    const peakSurcharge = parseFloat(deliveryConfigElement.dataset.peakSurcharge);
    const bufferMin = parseFloat(deliveryConfigElement.dataset.bufferMin);
    const bufferMax = parseFloat(deliveryConfigElement.dataset.bufferMax);
    const timeSlots = JSON.parse(deliveryConfigElement.dataset.timeSlots); // 希望配達時間スロットの表示名

    // 取得したデータのバリデーション
    // 店舗の緯度経度がNaN（数値ではない）場合や、配達エリアが空の場合、処理を中断します。
    if (isNaN(shopLat) || isNaN(shopLon) || Object.keys(deliveryZones).length === 0) {
        estimatedTimeDisplay.textContent = '店舗の場所情報または配達エリア情報が不足しています。';
        earliestArrivalMessage.textContent = ''; // メッセージをクリア
        console.error('店舗の緯度経度または配達エリアデータが不正です。');
        console.error('shopLat:', shopLat, 'shopLon:', shopLon, 'deliveryZones:', deliveryZones);
        return;
    }

    /**
     * 2点間の距離をkmで計算する（ハバーサインの公式）
     * @param {number} lat1 緯度1
     * @param {number} lon1 経度1
     * @param {number} lat2 緯度2
     * @param {number} lon2 経度2
     * @returns {number} 距離 (km)
     */
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const earthRadius = 6371; // 地球の半径 (km)

        // 緯度・経度をラジアンに変換
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        const distance = earthRadius * c; // 距離 (km)
        return distance;
    }

    /**
     * 予測配達時間を計算し、表示を更新するメイン関数
     */
    function calculateAndDisplayEstimatedTime() {
        const selectedZoneName = deliveryZoneSelect.value;
        const selectedTimeSlotKey = desiredTimeSelect.value; // 選択された希望配達時間スロットのキー (例: "12:30" or "ASAP")

        // 配達エリアが選択されていない場合、エラーメッセージを表示して中断
        if (!selectedZoneName || !deliveryZones[selectedZoneName]) {
            estimatedTimeDisplay.textContent = '配達エリアを選択してください。';
            earliestArrivalMessage.textContent = ''; // 最短到着時刻メッセージをクリア
            return;
        }

        const destLat = deliveryZones[selectedZoneName].latitude;
        const destLon = deliveryZones[selectedZoneName].longitude;

        // 基本の所要時間を計算 (移動時間 + 準備時間)
        const distanceKm = calculateDistance(shopLat, shopLon, destLat, destLon);
        let baseDurationMinutes = basePreparationTime + (distanceKm * deliverySpeedPerKm);

        // ピーク時間帯の判定と加算
        const now = new Date(); // 現在時刻
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        const currentTimeInMinutes = currentHour * 60 + currentMinute;

        let peakSurchargeApplied = 0;
        peakHours.forEach(period => {
            const [startStr, endStr] = period.split('-');
            const [startHour, startMinute] = startStr.split(':').map(Number);
            const [endHour, endMinute] = endStr.split(':').map(Number);

            const startTimeInMinutes = startHour * 60 + startMinute;
            const endTimeInMinutes = endHour * 60 + endMinute;

            // 現在時刻がピーク時間帯に含まれるかを判定
            // 終了時刻が翌日0時の場合 (例: 23:00-00:00) の考慮
            if (startTimeInMinutes > endTimeInMinutes) { // 例: 23:00-01:00 のように日付をまたぐ場合
                if (currentTimeInMinutes >= startTimeInMinutes || currentTimeInMinutes <= endTimeInMinutes) {
                    peakSurchargeApplied = peakSurcharge;
                }
            } else { // 通常の時間帯 (例: 12:00-13:00)
                if (currentTimeInMinutes >= startTimeInMinutes && currentTimeInMinutes <= endTimeInMinutes) {
                    peakSurchargeApplied = peakSurcharge;
                }
            }
        });
        baseDurationMinutes += peakSurchargeApplied;

        // ランダムなバッファを加算
        const randomBuffer = Math.floor(Math.random() * (bufferMax - bufferMin + 1)) + bufferMin;
        let totalDurationMinutes = Math.round(baseDurationMinutes + randomBuffer);

        // 最短到着時刻を計算 (現在時刻 + 総所要時間)
        const earliestArrival = new Date(now.getTime() + totalDurationMinutes * 60 * 1000);
        const earliestArrivalHour = String(earliestArrival.getHours()).padStart(2, '0');
        const earliestArrivalMinute = String(earliestArrival.getMinutes()).padStart(2, '0');
        const earliestArrivalTimeStr = `${earliestArrivalHour}:${earliestArrivalMinute}`;

        // 希望配達時間スロットが「ASAP」（できるだけ早く）の場合
        if (selectedTimeSlotKey === 'ASAP' || selectedTimeSlotKey === '') { // 空文字列もASAP扱い
            estimatedTimeDisplay.textContent = `約 ${totalDurationMinutes} 分`;
            earliestArrivalMessage.textContent = `最短到着時刻: ${earliestArrivalTimeStr}頃`;
        } else {
            // 特定の希望配達時間スロットが選択されている場合
            const desiredTimeParts = selectedTimeSlotKey.split(':').map(Number);
            const desiredHour = desiredTimeParts[0];
            const desiredMinute = desiredTimeParts[1];

            // 希望配達時刻のDateオブジェクトを作成 (今日の日付で)
            const desiredArrivalTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), desiredHour, desiredMinute, 0);

            // もし希望配達時刻が現在時刻より過去であれば、翌日の同じ時間に設定
            // (ユーザーが今日中に間に合わない過去の時間を指定した場合の考慮)
            if (desiredArrivalTime.getTime() < now.getTime() && (desiredHour < currentHour || (desiredHour === currentHour && desiredMinute < currentMinute))) {
                 desiredArrivalTime.setDate(desiredArrivalTime.getDate() + 1);
            }

            // 希望配達時刻が最短到着時刻より前の場合
            if (desiredArrivalTime.getTime() < earliestArrival.getTime()) {
                estimatedTimeDisplay.textContent = `約 ${totalDurationMinutes} 分`; // 所要時間は変わらない
                earliestArrivalMessage.textContent = `最短到着時刻: ${earliestArrivalTimeStr}頃 (ご希望の時間には間に合いません)`;
                // ここでユーザーに視覚的なフィードバック（例：テキスト色変更、アラートなど）を追加することも可能
            } else {
                // 希望配達時刻が最短到着時刻以降の場合
                // ここでは、希望配達時刻を優先して表示
                const displaySlotName = timeSlots[selectedTimeSlotKey] || selectedTimeSlotKey;
                estimatedTimeDisplay.textContent = `${displaySlotName}頃`;
                earliestArrivalMessage.textContent = `最短到着時刻: ${earliestArrivalTimeStr}頃`;
            }
        }
    }

    // イベントリスナー
    // 配達エリア選択が変更されたときに予測時間を更新
    deliveryZoneSelect.addEventListener('change', calculateAndDisplayEstimatedTime);
    // 希望配達時間選択が変更されたときに予測時間を更新
    desiredTimeSelect.addEventListener('change', calculateAndDisplayEstimatedTime);

    // ページロード時にも初期予測を計算して表示
    calculateAndDisplayEstimatedTime();
});
