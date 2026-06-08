import { Users, MessageCircle, Award, BookOpen } from 'lucide-react'

// Data dummy untuk demo
const DUMMY_STATS = { total_siswa: 32, total_chat: 124, rata_rata_nilai: 82, total_pertemuan: 5 }
const DUMMY_PERTANYAAN = [
  { nama: 'Muhammad Iqbal', teks: 'Bagaimana hukum Fiqh Muamalah terkait penggunaan bandwidth?', waktu: '5 menit lalu' },
  { nama: 'Dina Amelia', teks: 'Cara mitigasi risiko kebocoran data wallet kripto menggunakan VLAN?', waktu: '12 menit lalu' },
  { nama: 'Rizky Pratama', teks: 'Kenapa ping ke server Sharia Core Banking mengalami RTO?', waktu: '30 menit lalu' },
  { nama: 'Sari Wulandari', teks: 'Bagaimana alokasi subnet IP kelas C untuk mengamankan data transaksi syariah?', waktu: '1 jam lalu' },
  { nama: 'Andi Setiawan', teks: 'Bagaimana keandalan static routing dibanding dynamic untuk transaksi kripto?', waktu: '2 jam lalu' },
]

export default function DashboardPage() {
  return (
    <div>
      <h1 className="text-xl font-bold text-gray-800">Dashboard</h1>
      <p className="text-sm text-gray-500 mt-1">Ringkasan aktivitas siswa</p>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <StatCard icon={<Users size={18} />} label="Total Siswa" value={DUMMY_STATS.total_siswa} />
        <StatCard icon={<MessageCircle size={18} />} label="Total Chat" value={DUMMY_STATS.total_chat} />
        <StatCard icon={<Award size={18} />} label="Rata-rata Nilai" value={DUMMY_STATS.rata_rata_nilai} />
        <StatCard icon={<BookOpen size={18} />} label="Pertemuan" value={DUMMY_STATS.total_pertemuan} />
      </div>

      <div className="mt-8">
        <h2 className="text-sm font-semibold text-gray-800 mb-3">Pertanyaan Siswa Terbaru</h2>
        <div className="bg-white rounded-xl border border-gray-200 divide-y divide-gray-50">
          {DUMMY_PERTANYAAN.map((p, i) => (
            <div key={i} className="px-4 py-3 flex items-start gap-3">
              <div className="w-7 h-7 bg-[#2D7DD2]/10 rounded-full flex items-center justify-center text-xs font-bold text-[#2D7DD2] flex-shrink-0">
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
