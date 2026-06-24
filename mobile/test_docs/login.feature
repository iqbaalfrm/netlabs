# language: id
Feature: Login Siswa
  Sebagai siswa SMK
  Saya ingin login ke aplikasi Netlabs
  Agar saya bisa mengakses materi praktikum jaringan komputer

  Background:
    Given aplikasi Netlabs sudah terinstal di device
    And siswa berada di halaman login

  Scenario: Login berhasil dengan NIS dan password valid
    Given field NIS kosong
    And field Kata Sandi kosong
    When siswa mengisi NIS dengan "2122100045"
    And siswa mengisi Kata Sandi dengan "siswa123"
    And siswa menekan tombol "Masuk"
    Then siswa diarahkan ke halaman Home/Dashboard
    And tidak muncul pesan error

  Scenario: Login gagal karena field NIS kosong
    Given field NIS kosong
    And field Kata Sandi berisi "siswa123"
    When siswa menekan tombol "Masuk"
    Then muncul snackbar dengan pesan "NIS dan Password harus diisi"
    And siswa tetap di halaman login

  Scenario: Login gagal karena field password kosong
    Given field NIS berisi "2122100045"
    And field Kata Sandi kosong
    When siswa menekan tombol "Masuk"
    Then muncul snackbar dengan pesan "NIS dan Password harus diisi"
    And siswa tetap di halaman login

  Scenario: Login gagal karena kedua field kosong
    Given field NIS kosong
    And field Kata Sandi kosong
    When siswa menekan tombol "Masuk"
    Then muncul snackbar dengan pesan "NIS dan Password harus diisi"
    And siswa tetap di halaman login

  Scenario: Login gagal karena credential salah
    Given field NIS kosong
    And field Kata Sandi kosong
    When siswa mengisi NIS dengan "0000000000"
    And siswa mengisi Kata Sandi dengan "salah123"
    And siswa menekan tombol "Masuk"
    Then muncul snackbar dengan pesan "Login Gagal"
    And siswa tetap di halaman login

  Scenario: Toggle visibility password
    Given field Kata Sandi berisi "siswa123"
    And password ditampilkan dalam bentuk tersembunyi (dot)
    When siswa menekan ikon visibility di field password
    Then password ditampilkan dalam bentuk teks biasa
    When siswa menekan ikon visibility lagi
    Then password ditampilkan dalam bentuk tersembunyi (dot)

  Scenario: Loading indicator saat proses login
    When siswa mengisi NIS dengan "2122100045"
    And siswa mengisi Kata Sandi dengan "siswa123"
    And siswa menekan tombol "Masuk"
    Then tombol "Masuk" menampilkan loading indicator
    And tombol "Masuk" tidak bisa ditekan selama loading
