import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'
import { api } from '../lib/api'

// Halaman login guru — pakai API real
export default function LoginPage() {
  const [nis, setNis] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!nis || !password) { toast.error('NIS dan password harus diisi'); return }

    setLoading(true)
    try {
      const res = await api.post('/api/auth/login', { nis, password })
      const { token, user } = res.data

      if (user.role !== 'guru') {
        toast.error('Hanya guru yang bisa mengakses panel ini')
        return
      }

      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))
      toast.success(`Selamat datang, ${user.nama}!`)
      navigate('/')
    } catch (err: any) {
      toast.error(err.response?.data?.detail ?? 'Login gagal')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-[#F8F9FB] flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        <div className="text-center mb-8">
          <h1 className="text-2xl font-bold text-gray-800">Netlabs</h1>
          <p className="text-sm text-gray-500 mt-1">Panel Guru</p>
        </div>

        <form onSubmit={handleLogin} className="bg-white rounded-xl p-6 border border-gray-200">
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">NIS / ID Guru</label>
            <input type="text" value={nis} onChange={e => setNis(e.target.value)}
              placeholder="Masukkan NIS"
              className="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-[#2D7DD2] focus:outline-none" />
          </div>
          <div className="mb-6">
            <label className="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
            <input type="password" value={password} onChange={e => setPassword(e.target.value)}
              placeholder="Masukkan password"
              className="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-[#2D7DD2] focus:outline-none" />
          </div>
          <button type="submit" disabled={loading}
            className="w-full py-2.5 bg-[#2D7DD2] text-white text-sm font-medium rounded-lg hover:bg-[#2568b0] disabled:opacity-50">
            {loading ? 'Memproses...' : 'Masuk'}
          </button>
        </form>
        <p className="text-center text-xs text-gray-400 mt-6">SMK Bhakti Praja Dukuhwaru</p>
      </div>
    </div>
  )
}
