import axios from 'axios'

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000',
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export type LoginResponse = {
  token: string
  user: {
    id: string
    nis: string
    nama: string
    role: 'guru' | 'siswa'
    kelas?: string | null
  }
}

export type DashboardStats = {
  total_siswa: number
  total_chat: number
  rata_rata_nilai: number
  total_pertemuan: number
}

export type PertanyaanGuru = {
  id: string
  teks: string
  waktu: string
  users?: {
    nama?: string
    nis?: string
    kelas?: string
  }
}

export type Siswa = {
  id: string
  nis: string
  nama: string
  kelas?: string | null
  sekolah?: string | null
  streak_hari?: number
  total_chat?: number
}

export type Pertemuan = {
  id: string
  nomor_urut: number
  judul: string
  deskripsi?: string | null
  warna_hex?: string | null
  daftar_topik?: unknown[]
}
