import { useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { ArrowLeft, Plus, Trash2 } from 'lucide-react'

// Data dummy per pertemuan
const DUMMY_DATA: Record<string, { judul: string; warna: string; topik: string[]; soal: { pertanyaan: string; jawaban_benar: string }[] }> = {
  p1: {
    judul: 'Pengenalan Jaringan Komputer',
    warna: '#2D7DD2',
    topik: ['Pengertian Jaringan Komputer', 'Jenis Jaringan (LAN, MAN, WAN)', 'Topologi Jaringan', 'Perangkat Keras Jaringan'],
    soal: [
      { pertanyaan: 'Apa yang dimaksud jaringan komputer?', jawaban_benar: 'a' },
      { pertanyaan: 'Jaringan satu kota disebut?', jawaban_benar: 'b' },
      { pertanyaan: 'Topologi paling umum?', jawaban_benar: 'c' },
      { pertanyaan: 'Perangkat yang hubungkan dua jaringan?', jawaban_benar: 'c' },
      { pertanyaan: 'Contoh WAN?', jawaban_benar: 'c' },
    ]
  },
  p2: {
    judul: 'Pengalamatan IP (IP Addressing)',
    warna: '#0F9B8E',
    topik: ['Pengertian IP Address', 'Kelas IP Address (A, B, C)', 'IP Public vs IP Private', 'Subnetting Dasar'],
    soal: [
      { pertanyaan: 'Berapa bit IPv4?', jawaban_benar: 'b' },
      { pertanyaan: 'IP 192.168.1.1 kelas?', jawaban_benar: 'c' },
      { pertanyaan: 'IP Private?', jawaban_benar: 'b' },
      { pertanyaan: 'Subnet mask kelas C?', jawaban_benar: 'c' },
      { pertanyaan: 'Tujuan subnetting?', jawaban_benar: 'b' },
    ]
  },
  p3: {
    judul: 'Konfigurasi IP di Windows',
    warna: '#7B5EA7',
    topik: ['Setting IP Manual di Windows', 'Verifikasi dengan CMD', 'Troubleshooting Koneksi'],
    soal: [
      { pertanyaan: 'Perintah lihat IP?', jawaban_benar: 'b' },
      { pertanyaan: 'Perintah test koneksi?', jawaban_benar: 'b' },
      { pertanyaan: 'IP loopback?', jawaban_benar: 'c' },
      { pertanyaan: 'Ping gateway gagal berarti?', jawaban_benar: 'b' },
      { pertanyaan: 'Fungsi default gateway?', jawaban_benar: 'b' },
    ]
  },
  p4: { judul: 'Implementasi VLAN', warna: '#F4A261', topik: ['Pengertian VLAN', 'Konfigurasi Switch', 'Inter-VLAN Routing', 'Verifikasi VLAN'], soal: [] },
  p5: { judul: 'Static Routing', warna: '#E05263', topik: ['Konsep Routing', 'Konfigurasi Static Route', 'Verifikasi Routing Table'], soal: [] },
}

export default function DetailPertemuanPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const [tab, setTab] = useState<'topik' | 'kuis'>('topik')
  const [showTopikForm, setShowTopikForm] = useState(false)
  const [showSoalForm, setShowSoalForm] = useState(false)
  const [judulTopik, setJudulTopik] = useState('')

  const data = DUMMY_DATA[id ?? 'p1']
  if (!data) return <div>Pertemuan tidak ditemukan</div>

  const nomor = id?.replace('p', '') ?? '1'

  return (
    <div>
      {/* Header */}
      <div className="flex items-center gap-3 mb-6">
        <button onClick={() => navigate('/pertemuan')}
          className="p-1.5 rounded-lg hover:bg-gray-100 transition">
          <ArrowLeft size={18} className="text-gray-600" />
        </button>
        <div>
          <p className="text-xs text-gray-400">Pertemuan {nomor}</p>
          <h1 className="text-xl font-bold text-gray-800">{data.judul}</h1>
        </div>
      </div>

      {/* Tab */}
      <div className="flex gap-2 mb-6">
        {(['topik', 'kuis'] as const).map(t => (
          <button key={t} onClick={() => setTab(t)}
            className={`px-4 py-2 text-sm font-medium rounded-lg transition ${
              tab === t ? 'bg-[#2D7DD2] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'
            }`}>
            {t === 'topik' ? `Topik (${data.topik.length})` : `Soal Kuis (${data.soal.length})`}
          </button>
        ))}
      </div>

      {/* Tab Topik */}
      {tab === 'topik' && (
        <div>
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-sm font-semibold text-gray-700">Daftar Topik</h2>
            <button onClick={() => setShowTopikForm(!showTopikForm)}
              className="flex items-center gap-1 text-sm text-[#2D7DD2] font-medium hover:text-[#2568b0]">
              <Plus size={14} /> Tambah
            </button>
          </div>

          {showTopikForm && (
            <div className="mb-4 bg-white rounded-xl border border-gray-200 p-4">
              <input placeholder="Judul topik baru" value={judulTopik} onChange={e => setJudulTopik(e.target.value)}
                className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#2D7DD2] focus:outline-none mb-3" />
              <div className="flex gap-2">
                <button onClick={() => { setShowTopikForm(false); setJudulTopik('') }}
                  className="px-4 py-2 bg-[#2D7DD2] text-white text-sm rounded-lg">Simpan</button>
                <button onClick={() => setShowTopikForm(false)}
                  className="px-4 py-2 border border-gray-200 text-sm rounded-lg">Batal</button>
              </div>
            </div>
          )}

          <div className="space-y-2">
            {data.topik.map((t, i) => (
              <div key={i} className="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3 group">
                <span className="w-6 h-6 bg-[#2D7DD2]/10 text-[#2D7DD2] rounded-md flex items-center justify-center text-xs font-bold flex-shrink-0">{i + 1}</span>
                <span className="flex-1 text-sm text-gray-800">{t}</span>
                <button className="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 transition">
                  <Trash2 size={14} />
                </button>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Tab Soal Kuis */}
      {tab === 'kuis' && (
        <div>
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-sm font-semibold text-gray-700">Soal Kuis</h2>
            <button onClick={() => setShowSoalForm(!showSoalForm)}
              className="flex items-center gap-1 text-sm text-[#2D7DD2] font-medium">
              <Plus size={14} /> Tambah Soal
            </button>
          </div>

          {data.soal.length === 0 ? (
            <div className="bg-white rounded-xl border border-gray-200 p-8 text-center">
              <p className="text-sm text-gray-400">Belum ada soal. Klik "Tambah Soal" untuk mulai.</p>
            </div>
          ) : (
            <div className="space-y-3">
              {data.soal.map((s, i) => (
                <div key={i} className="bg-white rounded-xl border border-gray-200 p-4">
                  <div className="flex items-start justify-between">
                    <p className="text-sm font-medium text-gray-800">{i + 1}. {s.pertanyaan}</p>
                    <button className="text-red-400 hover:text-red-600 ml-2"><Trash2 size={14} /></button>
                  </div>
                  <div className="mt-2">
                    <span className="text-xs text-[#2D7DD2] font-medium bg-[#2D7DD2]/10 px-2 py-1 rounded">
                      Jawaban: {s.jawaban_benar.toUpperCase()}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  )
}
