# GitHub Actions Workflows

## Docker Build and Push

Bu workflow, projenin Docker image'ini otomatik olarak build edip Docker Hub'a push eder.

### Özellikler

- **Otomatik Tetikleme**: 
  - `main` veya `master` branch'ine push yapıldığında
  - Version tag'leri oluşturulduğunda (örn: `v1.0.0`)
  - Pull request açıldığında (sadece build, push yapılmaz)
  - Manuel olarak workflow_dispatch ile

- **Multi-platform Support**: 
  - linux/amd64
  - linux/arm64

- **Akıllı Tag'leme**:
  - `latest` - ana branch için
  - `main` veya `master` - branch adı
  - `v1.0.0` - semantic version tag'leri için
  - `1.0` - major.minor version
  - `1` - major version

### Gerekli GitHub Secrets

Bu workflow'un çalışması için repository'nizde şu secrets'ları tanımlamanız gerekir:

1. `DOCKER_USERNAME`: Docker Hub kullanıcı adınız
2. `DOCKER_PASSWORD`: Docker Hub access token'ınız (şifre yerine token kullanmanız önerilir)

#### Secrets Nasıl Eklenir?

1. GitHub repository'nizde **Settings** > **Secrets and variables** > **Actions**'a gidin
2. **New repository secret** butonuna tıklayın
3. İlk secret için:
   - Name: `DOCKER_USERNAME`
   - Value: Docker Hub kullanıcı adınız
4. İkinci secret için:
   - Name: `DOCKER_PASSWORD`
   - Value: Docker Hub access token'ınız

#### Docker Hub Access Token Nasıl Oluşturulur?

1. [Docker Hub](https://hub.docker.com/)'a giriş yapın
2. Sağ üst köşedeki profil resminize tıklayıp **Account Settings**'e gidin
3. **Security** sekmesine tıklayın
4. **New Access Token** butonuna tıklayın
5. Token'a bir isim verin (örn: "GitHub Actions")
6. **Read, Write, Delete** yetkilerini seçin
7. **Generate** butonuna tıklayın
8. Oluşturulan token'ı kopyalayın (bir daha göremezsiniz!)

### Kullanım

#### Otomatik Build ve Push

Workflow otomatik olarak çalışır. `main` veya `master` branch'ine kod push ettiğinizde:

```bash
git add .
git commit -m "Your commit message"
git push origin main
```

Image Docker Hub'da `syil/arkres:latest` olarak publish edilir.

#### Version Tag ile Release

Yeni bir version release etmek için:

```bash
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

Bu şu tag'leri oluşturur:
- `syil/arkres:v1.0.0`
- `syil/arkres:1.0`
- `syil/arkres:1`
- `syil/arkres:latest`

#### Manuel Çalıştırma

GitHub'da **Actions** sekmesinden workflow'u manuel olarak da çalıştırabilirsiniz:

1. Repository'de **Actions** sekmesine gidin
2. Sol menüden **Docker Build and Push** workflow'unu seçin
3. **Run workflow** butonuna tıklayın
4. Branch'i seçin ve **Run workflow**'a tıklayın

### Docker Image Kullanımı

Build edilen image'i kullanmak için:

```bash
docker pull syil/arkres:latest
docker run -d -p 80:80 syil/arkres:latest
```

Veya docker-compose.yml dosyanızda:

```yaml
services:
  web:
    image: syil/arkres:latest
    ports:
      - "80:80"
```

### Sorun Giderme

#### "Error: Cannot perform an interactive login from a non TTY device"

Secrets'ların doğru tanımlanmadığını gösterir. `DOCKER_USERNAME` ve `DOCKER_PASSWORD` secrets'larını kontrol edin.

#### "denied: requested access to the resource is denied"

Docker Hub kullanıcı adı veya token'ın yanlış olduğunu gösterir. Secrets'ları kontrol edin.

#### Build Başarısız

Logs'larda hatayı görmek için:
1. **Actions** sekmesine gidin
2. Başarısız workflow run'a tıklayın
3. Başarısız job'a tıklayın
4. Hata mesajlarını inceleyin
