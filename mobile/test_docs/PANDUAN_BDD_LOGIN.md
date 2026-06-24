# Panduan Behavior Driven Development (BDD) - Fitur Login Netlabs

## Daftar Isi
1. [Pengantar BDD](#1-pengantar-bdd)
2. [Tools yang Digunakan](#2-tools-yang-digunakan)
3. [Setup Environment](#3-setup-environment)
4. [Gherkin Feature File](#4-gherkin-feature-file)
5. [Implementasi Test di Katalon Studio](#5-implementasi-test-di-katalon-studio)
6. [Menjalankan Test](#6-menjalankan-test)
7. [Analisis Hasil](#7-analisis-hasil)

---

## 1. Pengantar BDD

**Behavior Driven Development (BDD)** adalah pendekatan pengembangan software yang menggabungkan praktik Test Driven Development (TDD) dengan prinsip Domain Driven Design (DDD). BDD berfokus pada *behavior* (perilaku) sistem dari sudut pandang pengguna.

### Siklus BDD

```
Discovery → Formulation → Automation
```

| Tahap | Aktivitas | Output |
|-------|-----------|--------|
| Discovery | Diskusi requirement dengan stakeholder | User Story |
| Formulation | Menulis skenario dalam bahasa Gherkin | `.feature` file |
| Automation | Mengotomasi skenario menjadi test | Test script (Katalon) |

### Gherkin Syntax

Gherkin menggunakan keyword berikut:

| Keyword | Fungsi |
|---------|--------|
| `Feature` | Deskripsi fitur yang diuji |
| `Scenario` | Satu skenario test spesifik |
| `Given` | Precondition / keadaan awal |
| `When` | Aksi yang dilakukan user |
| `Then` | Hasil yang diharapkan (assertion) |
| `And` | Melanjutkan step sebelumnya |
| `Background` | Step yang dijalankan sebelum setiap Scenario |

---

## 2. Tools yang Digunakan

| Tool | Fungsi |
|------|--------|
| Katalon Studio | Test automation IDE (gratis untuk komunitas) |
| Appium | Driver mobile automation (built-in di Katalon) |
| Android SDK | Platform tools untuk koneksi device |
| APK Netlabs | Aplikasi yang akan ditest |

---

## 3. Setup Environment

### 3.1 Install Katalon Studio

1. Download Katalon Studio di https://katalon.com/download
2. Install dan buat akun (gratis)
3. Buka Katalon Studio

### 3.2 Konfigurasi Mobile Testing

1. Pastikan Android SDK terinstal (via Android Studio)
2. Aktifkan **USB Debugging** di device Android:
   - Settings → About Phone → Tap "Build Number" 7x
   - Settings → Developer Options → USB Debugging ON
3. Hubungkan device via USB
4. Verifikasi koneksi:
   ```bash
   adb devices
   ```

### 3.3 Setup Project Katalon

1. File → New → Project
2. Isi nama project: `Netlabs_BDD_Login`
3. Pilih tipe: **Mobile**
4. Klik OK

### 3.4 Install APK ke Device

```bash
adb install app-release.apk
```

APK tersedia di: `mobile/build/app/outputs/flutter-apk/app-release.apk`

---

## 4. Gherkin Feature File

### 4.1 Membuat Feature File di Katalon

1. Klik kanan folder **Include → features**
2. New → Feature File
3. Nama: `login.feature`
4. Paste isi dari `test_docs/login.feature`

### 4.2 Skenario yang Diuji

File `login.feature` berisi 7 skenario:

| # | Skenario | Tipe Test |
|---|----------|-----------|
| 1 | Login berhasil dengan credential valid | Positive |
| 2 | Login gagal - NIS kosong | Negative |
| 3 | Login gagal - password kosong | Negative |
| 4 | Login gagal - kedua field kosong | Negative |
| 5 | Login gagal - credential salah | Negative |
| 6 | Toggle visibility password | UI Behavior |
| 7 | Loading indicator saat login | UI Behavior |

---

## 5. Implementasi Test di Katalon Studio

### 5.1 Spy Mobile - Menangkap Object UI

1. Klik **Spy Mobile** di toolbar Katalon
2. Pilih device yang terhubung
3. Isi Application ID: `com.netlabs.netlabs`
4. Klik **Start**
5. Aplikasi akan terbuka di device
6. Capture object berikut:

| Object | Tipe | Cara Identifikasi |
|--------|------|-------------------|
| Field NIS | TextField | `hint: "Contoh: 2122100045"` |
| Field Kata Sandi | TextField | `hint: "••••••••"` |
| Tombol Masuk | ElevatedButton | `text: "Masuk"` |
| Icon Visibility | GestureDetector | icon visibility_off/visibility |
| Snackbar Message | Text | class SnackBar |

7. Save semua object ke **Object Repository → Mobile → Login**

### 5.2 Membuat Step Definitions

Buka folder **Include → scripts → groovy** dan buat class `LoginSteps.groovy`:

```groovy
import static com.kms.katalon.core.testobject.ObjectRepository.findTestObject
import com.kms.katalon.core.mobile.keyword.MobileBuiltInKeywords as Mobile
import cucumber.api.java.en.*

class LoginSteps {

    @Given("aplikasi Netlabs sudah terinstal di device")
    def aplikasiTerinstal() {
        Mobile.startApplication('com.netlabs.netlabs', false)
    }

    @Given("siswa berada di halaman login")
    def diHalamanLogin() {
        Mobile.waitForElementPresent(findTestObject('Mobile/Login/field_nis'), 10)
    }

    @Given("field NIS kosong")
    def fieldNISKosong() {
        Mobile.clearText(findTestObject('Mobile/Login/field_nis'), 0)
    }

    @Given("field Kata Sandi kosong")
    def fieldPasswordKosong() {
        Mobile.clearText(findTestObject('Mobile/Login/field_password'), 0)
    }

    @Given("field NIS berisi {string}")
    def fieldNISBerisi(String nis) {
        Mobile.setText(findTestObject('Mobile/Login/field_nis'), nis, 0)
    }

    @Given("field Kata Sandi berisi {string}")
    def fieldPasswordBerisi(String password) {
        Mobile.setText(findTestObject('Mobile/Login/field_password'), password, 0)
    }

    @When("siswa mengisi NIS dengan {string}")
    def isiNIS(String nis) {
        Mobile.setText(findTestObject('Mobile/Login/field_nis'), nis, 0)
    }

    @When("siswa mengisi Kata Sandi dengan {string}")
    def isiPassword(String password) {
        Mobile.setText(findTestObject('Mobile/Login/field_password'), password, 0)
    }

    @When("siswa menekan tombol {string}")
    def tekanTombol(String label) {
        Mobile.tap(findTestObject('Mobile/Login/btn_masuk'), 0)
    }

    @When("siswa menekan ikon visibility di field password")
    def tekanIconVisibility() {
        Mobile.tap(findTestObject('Mobile/Login/icon_visibility'), 0)
    }

    @Then("siswa diarahkan ke halaman Home/Dashboard")
    def diHalamanHome() {
        Mobile.waitForElementPresent(findTestObject('Mobile/Home/dashboard'), 10)
    }

    @Then("tidak muncul pesan error")
    def tidakAdaError() {
        Mobile.verifyElementNotPresent(findTestObject('Mobile/Login/snackbar'), 3)
    }

    @Then("muncul snackbar dengan pesan {string}")
    def munculSnackbar(String pesan) {
        Mobile.waitForElementPresent(findTestObject('Mobile/Login/snackbar'), 5)
        Mobile.verifyElementText(findTestObject('Mobile/Login/snackbar'), pesan)
    }

    @Then("siswa tetap di halaman login")
    def tetapDiLogin() {
        Mobile.verifyElementPresent(findTestObject('Mobile/Login/field_nis'), 3)
    }

    @Then("tombol {string} menampilkan loading indicator")
    def tombolLoading(String label) {
        Mobile.verifyElementPresent(findTestObject('Mobile/Login/loading_indicator'), 3)
    }

    @Then("tombol {string} tidak bisa ditekan selama loading")
    def tombolDisabled(String label) {
        Mobile.verifyElementAttributeValue(
            findTestObject('Mobile/Login/btn_masuk'), 'enabled', 'false', 0)
    }

    @Then("password ditampilkan dalam bentuk tersembunyi (dot)")
    def passwordHidden() {
        // Verify field type is password (obscured)
        Mobile.verifyElementAttributeValue(
            findTestObject('Mobile/Login/field_password'), 'password', 'true', 0)
    }

    @Then("password ditampilkan dalam bentuk teks biasa")
    def passwordVisible() {
        Mobile.verifyElementAttributeValue(
            findTestObject('Mobile/Login/field_password'), 'password', 'false', 0)
    }
}
```

### 5.3 Membuat Test Suite

1. Klik kanan **Test Suites** → New → Test Suite
2. Nama: `Login_BDD_Suite`
3. Tambahkan feature file `login.feature`
4. Mapping step definitions ke `LoginSteps.groovy`

---

## 6. Menjalankan Test

### 6.1 Menjalankan via Katalon

1. Buka Test Suite `Login_BDD_Suite`
2. Klik **Run** (ikon Play)
3. Pilih device Android yang terhubung
4. Katalon akan:
   - Install APK ke device
   - Jalankan setiap skenario secara berurutan
   - Capture screenshot di setiap step

### 6.2 Menjalankan Skenario Tertentu

- Klik kanan pada skenario di feature file → Run

### 6.3 Tips Debugging

- Gunakan **Record Mobile** untuk merekam interaksi manual lalu convert ke script
- Gunakan **Spy Mobile** jika object tidak terdeteksi
- Tambah `Mobile.delay(2)` jika ada timing issue

---

## 7. Analisis Hasil

### 7.1 Test Report

Setelah test selesai, Katalon generate report di folder **Reports**:
- Status setiap skenario (Pass/Fail)
- Screenshot di setiap step
- Log detail error jika ada

### 7.2 Mapping Hasil ke Gherkin

| Skenario | Expected Result | Kriteria Pass |
|----------|-----------------|---------------|
| Login berhasil | Navigasi ke Home | Halaman Home muncul |
| Field kosong | Snackbar error | Pesan "NIS dan Password harus diisi" |
| Credential salah | Snackbar error | Pesan "Login Gagal" |
| Toggle password | Text visible/hidden | Attribute password berubah |
| Loading indicator | Spinner muncul | Element loading_indicator present |

### 7.3 Traceability Matrix

```
User Story → Gherkin Scenario → Step Definition → Test Execution → Report
```

Setiap skenario Gherkin bisa di-trace balik ke requirement dan ke depan ke hasil test. Ini adalah inti dari **Living Documentation** dalam BDD.

---

## Struktur File Project Katalon

```
Netlabs_BDD_Login/
├── Include/
│   ├── features/
│   │   └── login.feature          ← Gherkin scenarios
│   └── scripts/groovy/
│       └── LoginSteps.groovy      ← Step definitions
├── Object Repository/
│   └── Mobile/
│       └── Login/
│           ├── field_nis
│           ├── field_password
│           ├── btn_masuk
│           ├── icon_visibility
│           ├── snackbar
│           └── loading_indicator
├── Test Suites/
│   └── Login_BDD_Suite
└── Reports/
```

---

## Credential untuk Testing

| Role | NIS | Password |
|------|-----|----------|
| Siswa | 2122100045 | siswa123 |

---

## Referensi

- [Katalon Docs - Mobile Testing](https://docs.katalon.com/docs/katalon-studio/get-started/mobile-testing)
- [Katalon Docs - BDD Testing](https://docs.katalon.com/docs/katalon-studio/get-started/bdd-testing-framework)
- [Gherkin Reference](https://cucumber.io/docs/gherkin/reference/)
- [Appium Inspector](http://appium.io/docs/en/tools/inspector/)
