import { useState, useEffect } from 'react'
import { Users, MessageCircle, Award, BookOpen } from 'lucide-react'
import { api } from '../lib/api'

// Dummy data fallback saat backend belum ada
const DUMMY_STATS = { total_siswa: 32, total_chat: 124, rata_rata_nilai: 78, total_pertemuan: 8 }
const DUMMY_PERTANYAAN = [
  { nama: 'Iqbal', teks: 'Apa perbedaan IP public dan private?', waktu: '5 menit lalu' },
  { nama: 'Dina', teks: 'Cara konfigurasi VLAN di switch Cisco?', waktu: '12 menit lalu' },
  { nama: 'Rizky', teks: 'Kenapa ping ke gateway gagal?', waktu: '30 menit lalu' },
  { nama: 'Sari', teks: 'Apa fungsi subnet mask?', waktu: '1 jam lalu' },
  { nama: 'Andi', teks: 'Bagaimana cara static routing?', waktu: '2 jam lalu' },
]

export default function DashboardPage() {
  const [stats, setStats] = useState(DUMMY_STATS)
  const [pertanyaan, setPertanyaan] = useState(DUMMY_PERTANYAAN)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const ambilData = async () => {
      try {
        const [statsRes, pertanyaanRes] = await Promise.all([
          api.get('/api/guru/dashboard'),
          api.get('/api/guru/pertanyaan'),
        ])
        setStats(statsRes.data)
        if (pertanyaanRes.data?.data?.length > 0) {
          setPertanyaan(pertanyaanRes.data.data.map((p: any) => ({
            nama: p.users?.nama ?? 'Siswa',
            teks: p.teks,
            waktu: new Date(p.waktu).toLocaleTimeString('id-ID'),
          })))
        }
      } catch {
        // Gunakan dummy data jika API belum tersedia
      } finally {
        setLoading(false)
      }
    }
    ambilData()
  }, [])

  if (loading) return (
    <div className="flex items-center justify-center h-40 text-sm text-gray-400">Memuat...</div>
  )

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-800">Dashboard</h1>
      <p className="text-sm text-gray-500 mt-1">Ringkasan aktivitas siswa</p>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <StatCard icon={<Users size={18} />} label="Total Siswa" value={stats.total_siswa} />
        <StatCard icon={<MessageCircle size={18} />} label="Total Chat" value={stats.total_chat} />
        <StatCard icon={<Award size={18} />} label="Rata-rata Nilai" value={stats.rata_rata_nilai} />
        <StatCard icon={<BookOpen size={18} />} label="Pertemuan" value={stats.total_pertemuan} />
      </div>

      <div className="mt-8">
        <h2 className="text-sm font-semibold text-gray-800 mb-3">Pertanyaan Terbaru</h2>
        <div className="bg-white rounded-xl border border-gray-200 divide-y divide-gray-50">
          {pertanyaan.map((p, i) => (
            <div key={i} className="px-4 py-3 flex items-start gap-3">
              <div className="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-xs font-medium text-gray-500 flex-shrink-0">
                {p.nama[0]}
              </div>
              <div className="min-w-0">
                <p className="text-sm text-gray-800 truncate">{p.teks}</p>
                <p className="text-xs text-gray-400 mt-0.5">{p.nama} · {p.waktu}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

function StatCard({ icon, label, value }: { icon: React.ReactNode; label: string; value: number }) {
  return (
    <div className="bg-white rounded-xl border border-gray-200 p-4">
      <div className="flex items-center gap-3">
        <div className="w-9 h-9 bg-[#2D7DD2]/10 rounded-lg flex items-center justify-center text-[#2D7DD2]">
          {icon}
        </div>
        <div>
          <p className="text-lg font-bold text-gray-800">{value}</p>
          <p className="text-xs text-gray-500">{label}</p>
        </div>
      </div>
    </div>
  )
}
