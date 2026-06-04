import { useState, useEffect } from 'react'
import { BookOpen, Plus, ChevronRight } from 'lucide-react'
import { useNavigate } from 'react-router-dom'
import { api } from '../lib/api'
import toast from 'react-hot-toast'

const DUMMY = [
  { id: '1', nomor_urut: 1, judul: 'Pengenalan Jaringan Komputer', warna_hex: '#2D7DD2' },
  { id: '2', nomor_urut: 2, judul: 'Pengalamatan IP (IP Addressing)', warna_hex: '#0F9B8E' },
  { id: '3', nomor_urut: 3, judul: 'Konfigurasi IP di Windows', warna_hex: '#7B5EA7' },
  { id: '4', nomor_urut: 4, judul: 'Implementasi VLAN', warna_hex: '#F4A261' },
  { id: '5', nomor_urut: 5, judul: 'Static Routing', warna_hex: '#E05263' },
]

export default function PertemuanPage() {
  const [pertemuan, setPertemuan] = useState(DUMMY)
  const [loading, setLoading] = useState(true)
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ judul: '', deskripsi: '', nomor_urut: '' })
  const [saving, setSaving] = useState(false)
  const navigate = useNavigate()

  useEffect(() => {
    api.get('/api/pertemuan').then(res => {
      if (res.data?.data?.length > 0) setPertemuan(res.data.data)
    }).catch(() => {}).finally(() => setLoading(false))
  }, [])

  const handleTambah = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!form.judul || !form.nomor_urut) { toast.error('Judul dan nomor harus diisi'); return }

    setSaving(true)
    try {
      const res = await api.post('/api/pertemuan', {
        judul: form.judul,
        deskripsi: form.deskripsi,
        nomor_urut: parseInt(form.nomor_urut),
      })
      setPertemuan(prev => [...prev, res.data.data[0]])
      setShowForm(false)
      setForm({ judul: '', deskripsi: '', nomor_urut: '' })
      toast.success('Pertemuan berhasil ditambahkan')
    } catch (err: any) {
      toast.error(err.response?.data?.detail ?? 'Gagal menambah pertemuan')
    } finally {
      setSaving(false)
    }
  }

  if (loading) return (
    <div className="flex items-center justify-center h-40 text-sm text-gray-400">Memuat...</div>
  )

  return (
    <div>
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-bold text-gray-800">Pertemuan</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola materi praktikum</p>
        </div>
        <button onClick={() => setShowForm(!showForm)}
          className="flex items-center gap-1.5 px-3 py-2 bg-[#2D7DD2] text-white text-sm font-medium rounded-lg hover:bg-[#2568b0]">
          <Plus size={16} /> Tambah
        </button>
      </div>

      {/* Form tambah pertemuan */}
      {showForm && (
        <form onSubmit={handleTambah} className="mt-4 bg-white rounded-xl border border-gray-200 p-4">
          <h3 className="text-sm font-semibold text-gray-800 mb-3">Tambah Pertemuan</h3>
          <div className="grid grid-cols-2 gap-3">
            <input type="number" placeholder="Nomor urut" value={form.nomor_urut}
              onChange={e => setForm({ ...form, nomor_urut: e.target.value })}
              className="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#2D7DD2] focus:outline-none" />
            <input type="text" placeholder="Judul pertemuan" value={form.judul}
              onChange={e => setForm({ ...form, judul: e.target.value })}
              className="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#2D7DD2] focus:outline-none" />
            <textarea placeholder="Deskripsi (opsional)" value={form.deskripsi}
              onChange={e => setForm({ ...form, deskripsi: e.target.value })}
              className="col-span-2 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#2D7DD2] focus:outline-none resize-none h-20" />
          </div>
          <div className="flex gap-2 mt-3">
            <button type="submit" disabled={saving}
              className="px-4 py-2 bg-[#2D7DD2] text-white text-sm font-medium rounded-lg hover:bg-[#2568b0] disabled:opacity-50">
              {saving ? 'Menyimpan...' : 'Simpan'}
            </button>
            <button type="button" onClick={() => setShowForm(false)}
              className="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
              Batal
            </button>
          </div>
        </form>
      )}

      {/* List pertemuan */}
      <div className="mt-4 space-y-2">
        {pertemuan.length === 0 ? (
          <div className="bg-white rounded-xl border border-gray-200 p-8 text-center">
            <BookOpen size={32} className="mx-auto text-gray-300 mb-2" />
            <p className="text-sm text-gray-400">Belum ada pertemuan. Tambahkan pertemuan baru.</p>
          </div>
        ) : pertemuan.map(p => (
          <div key={p.id}
            onClick={() => navigate(`/pertemuan/${p.id}`)}
            className="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 hover:border-[#2D7DD2]/40 hover:shadow-sm cursor-pointer transition">
            <div className="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
              style={{ backgroundColor: p.warna_hex ?? '#2D7DD2' }}>
              {p.nomor_urut}
            </div>
            <span className="flex-1 text-sm font-medium text-gray-800">{p.judul}</span>
            <ChevronRight size={16} className="text-gray-400" />
          </div>
        ))}
      </div>
    </div>
  )
}
