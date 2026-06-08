import { useState } from 'react'
import { Search } from 'lucide-react'

const DUMMY_SISWA = [
  { id: '1', nis: '2122100045', nama: 'Muhammad Iqbal', kelas: 'XI TKJ 2', total_chat: 124, streak_hari: 7 },
  { id: '2', nis: '2122100012', nama: 'Dina Amelia', kelas: 'XI TKJ 2', total_chat: 89, streak_hari: 5 },
  { id: '3', nis: '2122100023', nama: 'Rizky Pratama', kelas: 'XI TKJ 1', total_chat: 56, streak_hari: 3 },
  { id: '4', nis: '2122100034', nama: 'Sari Wulandari', kelas: 'XI TKJ 2', total_chat: 102, streak_hari: 6 },
  { id: '5', nis: '2122100056', nama: 'Andi Setiawan', kelas: 'XI TKJ 1', total_chat: 34, streak_hari: 2 },
  { id: '6', nis: '2122100067', nama: 'Putri Rahayu', kelas: 'XI TKJ 2', total_chat: 78, streak_hari: 4 },
  { id: '7', nis: '2122100078', nama: 'Budi Santoso', kelas: 'XI TKJ 1', total_chat: 45, streak_hari: 3 },
  { id: '8', nis: '2122100089', nama: 'Lina Marlina', kelas: 'XI TKJ 2', total_chat: 91, streak_hari: 5 },
]

export default function SiswaPage() {
  const [cari, setCari] = useState('')

  const filtered = DUMMY_SISWA.filter(s =>
    s.nama.toLowerCase().includes(cari.toLowerCase()) || s.nis.includes(cari)
  )

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-800">Data Siswa</h1>
      <p className="text-sm text-gray-500 mt-1">Pantau aktivitas dan nilai siswa</p>

      <div className="mt-6 relative max-w-xs">
        <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
        <input type="text" value={cari} onChange={e => setCari(e.target.value)}
          placeholder="Cari nama atau NIS..."
          className="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-[#2D7DD2] focus:outline-none" />
      </div>

      <div className="mt-4 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table className="w-full text-sm">
          <thead className="bg-gray-50">
            <tr>
              <th className="text-left px-4 py-2.5 text-xs font-medium text-gray-500">Nama</th>
              <th className="text-left px-4 py-2.5 text-xs font-medium text-gray-500">NIS</th>
              <th className="text-left px-4 py-2.5 text-xs font-medium text-gray-500">Kelas</th>
              <th className="text-left px-4 py-2.5 text-xs font-medium text-gray-500">Chat</th>
              <th className="text-left px-4 py-2.5 text-xs font-medium text-gray-500">Streak</th>
            </tr>
          </thead>
          <tbody>
            {filtered.length === 0 ? (
              <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-400 text-sm">Tidak ditemukan</td></tr>
            ) : filtered.map(s => (
              <tr key={s.id} className="border-t border-gray-50 hover:bg-gray-50">
                <td className="px-4 py-3 font-medium text-gray-800">{s.nama}</td>
                <td className="px-4 py-3 text-gray-500">{s.nis}</td>
                <td className="px-4 py-3 text-gray-500">{s.kelas}</td>
                <td className="px-4 py-3 text-gray-500">{s.total_chat}</td>
                <td className="px-4 py-3 text-gray-500">🔥 {s.streak_hari} hari</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}
