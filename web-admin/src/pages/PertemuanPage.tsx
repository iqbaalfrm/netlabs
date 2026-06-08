import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { ChevronRight, Plus } from 'lucide-react'

const DUMMY_PERTEMUAN = [
  { id: 'p1', nomor_urut: 1, judul: 'Pengenalan Jaringan & Kajian Syariah Teknologi', warna_hex: '#2D7DD2', topik: 4, siswa_selesai: 28 },
  { id: 'p2', nomor_urut: 2, judul: 'Pengalamatan IP & Mitigasi Risiko Aset Kripto', warna_hex: '#0F9B8E', topik: 4, siswa_selesai: 22 },
  { id: 'p3', nomor_urut: 3, judul: 'Konfigurasi IP & Keamanan Transaksi Muamalah', warna_hex: '#7B5EA7', topik: 3, siswa_selesai: 15 },
  { id: 'p4', nomor_urut: 4, judul: 'VLAN untuk Segmentasi Jaringan Syariah', warna_hex: '#F4A261', topik: 4, siswa_selesai: 8 },
  { id: 'p5', nomor_urut: 5, judul: 'Static Routing & Redundansi Data Kripto', warna_hex: '#E05263', topik: 3, siswa_selesai: 3 },
]

export default function PertemuanPage() {
  const navigate = useNavigate()
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ judul: '', nomor_urut: '' })

  return (
    <div>
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-bold text-gray-800">Pertemuan</h1>
          <p className="text-sm text-gray-500 mt-1">Kelola materi praktikum</p>
        </div>
        <button onClick={() => setShowForm(!showForm)}
          className="flex items-center gap-1.5 px-3 py-2 bg-[#2D7DD2] text-white text-sm font-medium rounded-lg hover:bg-[#2568b0] transition">
          <Plus size={16} /> Tambah
        </button>
      </div>

      {/* Form tambah (demo — hanya UI) */}
      {showForm && (
        <div className="mt-4 bg-white rounded-xl border border-gray-200 p-4">
          <h3 className="text-sm font-semibold text-gray-700 mb-3">Tambah Pertemuan Baru</h3>
          <div className="grid grid-cols-2 gap-3">
            <input type="number" placeholder="Nomor urut" value={form.nomor_urut}
              onChange={e => setForm({...form, nomor_urut: e.target.value})}
              className="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#2D7DD2] focus:outline-none" />
            <input type="text" placeholder="Judul pertemuan" value={form.judul}
              onChange={e => setForm({...form, judul: e.target.value})}
              className="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#2D7DD2] focus:outline-none" />
          </div>
          <div className="flex gap-2 mt-3">
            <button onClick={() => { setShowForm(false); setForm({judul:'',nomor_urut:''}) }}
              className="px-4 py-2 bg-[#2D7DD2] text-white text-sm rounded-lg">Simpan</button>
            <button onClick={() => setShowForm(false)}
              className="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg">Batal</button>
          </div>
        </div>
      )}

      {/* List pertemuan */}
      <div className="mt-4 space-y-2">
        {DUMMY_PERTEMUAN.map(p => (
          <div key={p.id}
            onClick={() => navigate(`/pertemuan/${p.id}`)}
            className="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 hover:border-[#2D7DD2]/40 hover:shadow-sm cursor-pointer transition">
            {/* Badge nomor */}
            <div className="w-9 h-9 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
              style={{ backgroundColor: p.warna_hex }}>
              {p.nomor_urut}
            </div>
            {/* Info */}
            <div className="flex-1 min-w-0">
              <p className="text-sm font-medium text-gray-800 truncate">{p.judul}</p>
              <p className="text-xs text-gray-400 mt-0.5">{p.topik} topik · {p.siswa_selesai}/32 siswa selesai</p>
            </div>
            <ChevronRight size={16} className="text-gray-400 flex-shrink-0" />
          </div>
        ))}
      </div>
    </div>
  )
}
