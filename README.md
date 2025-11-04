# Arkres - Docker ile Kurulum

Bu proje PHP ve MySQL kullanarak çalışan bir arkaplan resimleri paylaşım uygulamasıdır.

## Gereksinimler

- Docker
- Docker Compose

## Hızlı Başlangıç

Hızlı başlatma scripti ile tek komutta kurulum yapabilirsiniz:

```bash
./start.sh
```

## Kurulum Adımları (Manuel)

### 1. Yapılandırma Dosyasını Oluşturun

Öncelikle veritabanı yapılandırma dosyasını oluşturun:

```bash
cp veri/ayarlar.json.example veri/ayarlar.json
```

Gerekirse `veri/ayarlar.json` dosyasındaki ayarları düzenleyin.

### 2. Docker Container'ları Başlatın

```bash
docker compose up -d
```

Bu komut:
- MySQL veritabanı container'ını başlatır
- PHP + Apache web sunucusu container'ını başlatır
- Gerekli bağlantıları kurar

### 3. Uygulamaya Erişin

Tarayıcınızda şu adresi açın:

```
http://localhost
```

## Yönetim Komutları

### Container'ları Durdurmak

```bash
docker compose down
```

### Container'ları Yeniden Başlatmak

```bash
docker compose restart
```

### Logları Görüntülemek

```bash
# Tüm servislerin logları
docker compose logs -f

# Sadece web servisi
docker compose logs -f web

# Sadece veritabanı
docker compose logs -f db
```

### MySQL'e Bağlanmak

```bash
docker exec -it arkres_mysql mysql -u arkres_user -p arkres_db
```

(Şifre sorulduğunda: `arkres_password`)

## Yapılandırma

### Veritabanı Ayarları

Veritabanı ayarları `veri/ayarlar.json` dosyasında bulunur:

```json
{
  "Veritabani": {
    "Sunucu": "db",
    "KullaniciAdi": "arkres_user",
    "Sifre": "arkres_password",
    "VtAdi": "arkres_db",
    "KarakterSeti": "utf8"
  }
}
```

### Port Değiştirme

Eğer 80 portu kullanımda ise, `docker-compose.yml` dosyasındaki web servisi portunu değiştirebilirsiniz:

```yaml
ports:
  - "8080:80"  # 8080 portunu kullanmak için
```

## Veritabanı Yapısı

İlk çalıştırmada `init.sql` dosyası otomatik olarak çalıştırılır ve temel tablo yapısını oluşturur. Eğer farklı bir veritabanı yapısına ihtiyacınız varsa, `init.sql` dosyasını düzenleyebilirsiniz.

## Sorun Giderme

### Port 80 kullanımda hatası

Eğer 80 portu başka bir uygulama tarafından kullanılıyorsa:

1. `docker-compose.yml` dosyasında web servisi portunu değiştirin
2. Veya port 80'i kullanan uygulamayı durdurun

### Veritabanı bağlantı hatası

- Container'ların çalıştığından emin olun: `docker-compose ps`
- Veritabanı ayarlarının doğru olduğunu kontrol edin
- Container'ları yeniden başlatın: `docker-compose restart`

## Geliştirme

Kod değişiklikleri otomatik olarak container'a yansır çünkü volume olarak bağlanmıştır. PHP dosyalarını düzenledikten sonra tarayıcıyı yenilemeniz yeterlidir.
