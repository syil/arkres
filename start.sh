#!/bin/bash
# Arkres Docker Hızlı Başlatma Scripti

echo "===================================="
echo "Arkres Docker Kurulum"
echo "===================================="
echo ""

# Yapılandırma dosyasını kontrol et
if [ ! -f "veri/ayarlar.json" ]; then
    echo "✓ Yapılandırma dosyası oluşturuluyor..."
    cp veri/ayarlar.json.example veri/ayarlar.json
    echo "  veri/ayarlar.json oluşturuldu."
    echo ""
else
    echo "✓ Yapılandırma dosyası mevcut."
    echo ""
fi

# Docker container'larını başlat
echo "✓ Docker container'ları başlatılıyor..."
docker compose up -d

# Container'ların başlamasını bekle
echo ""
echo "✓ Container'ların başlaması bekleniyor..."
sleep 5

# Durum kontrolü
echo ""
echo "✓ Container durumları:"
docker compose ps

echo ""
echo "===================================="
echo "Kurulum tamamlandı!"
echo "===================================="
echo ""
echo "Uygulamaya erişmek için:"
echo "http://localhost"
echo ""
echo "Logları görmek için:"
echo "docker compose logs -f"
echo ""
echo "Durdurmak için:"
echo "docker compose down"
echo ""
