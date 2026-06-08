import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import toast from 'react-hot-toast'

// Halaman login guru — mode demo tanpa backend
export default function LoginPage() {
  const [nis, setNis] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!nis || !password) { toast.error('NIS dan password harus diisi'); return }

    setLoading(true)
    await new Promise(r => setTimeout(r, 800))

    // Demo: cek kredensial guru
    if (nis === 'GURU001' && password === 'guru123') {
      localStorage.setItem('token', 'demo-token-guru')
      localStorage.setItem('user', JSON.stringify({
        id: '1',
        nis: 'GURU001',
        nama: 'Pak Ahmad',
        role: 'guru',
        sekolah: 'SMK Bhakti Praja Dukuhwaru',
      }))
      toast.success('Selamat datang, Pak Ahmad!')
      navigate('/')
    } else {
      toast.error('NIS atau password salah. Gunakan GURU001 / guru123')
    }

    setLoading(false)
  }

  return (
    <div className="min-h-screen bg-[#F8F9FB] flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        <div className="text-center mb-8">
          <div className="w-12 h-12 bg-[#1A2B5F] rounded-xl flex items-center justify-center mx-auto mb-3">
            <span className="text-white font-bold text-lg">N</span>
          </div>
          <h1 className="text-2xl font-bold text-gray-800">Netlabs</h1>
          <p className="text-sm text-gray-500 mt-1">Panel Guru</p>
        </div>

        <form onSubmit={handleLogin} className="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">NIS / ID Guru</label>
            <input type="text" value={nis} onChange={e => setNis(e.target.value)}
              placeholder="Contoh: GURU001"
              className="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-[#2D7DD2] focus:outline-none focus:ring-1 focus:ring-[#2D7DD2]" />
          </div>
          <div className="mb-6">
            <label className="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
            <input type="password" value={password} onChange={e => setPassword(e.target.value)}
              placeholder="Masukkan password"
              className="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-[#2D7DD2] focus:outline-none focus:ring-1 focus:ring-[#2D7DD2]" />
          </div>
          <button type="submit" disabled={loading}
            className="w-full py-2.5 bg-[#2D7DD2] text-white text-sm font-medium rounded-lg hover:bg-[#2568b0] transition disabled:opacity-50">
            {loading ? 'Memproses...' : 'Masuk'}
          </button>
        </form>

        {/* Hint demo */}
        <div className="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
          <p className="text-xs text-blue-600 font-medium mb-1">Akun Demo:</p>
          <p className="text-xs text-blue-500">NIS: GURU001 | Password: guru123</p>
        </div>

        <p className="text-center text-xs text-gray-400 mt-4">SMK Bhakti Praja Dukuhwaru</p>
      </div>
    </div>
  )
}
