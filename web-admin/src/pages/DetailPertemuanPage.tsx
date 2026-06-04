import { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { ArrowLeft, Plus, Trash2 } from 'lucide-react'
import { api } from '../lib/api'
import toast from 'react-hot-toast'

type Topik = { id: string; judul: string; nomor_urut: number }
type Soal = { id: string; pertanyaan: string; pilihan_a: string; pilihan_b: string; pilihan_c: string; pilihan_d: string; jawaban_benar: string }

export default function DetailPertemuanPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const [tab, setTab] = useState<'topik' | 'kuis'>('topik')
  const [pertemuan, setPertemuan] = useState<any>(null)
  const [topikList, setTopikList] = useState<Topik[]>([])
  const [soalList, setSoalList] = useState<Soal[]>([])
  const [loading, setLoading] = useState(true)

  // Form tambah topik
  const [showTopikForm, setShowTopikForm] = useState(false)
  const [formTopik, setFormTopik] = useState({ judul: '', isi_materi: '' })
  const [savingTopik, setSavingTopik] = useState(false)

  // Form tambah soal
  const [showSoalForm, setShowSoalForm] = useState(false)
  const [formSoal, setFormSoal] = useState({ pertanyaan: '', pilihan_a: '', pilihan_b: '', pilihan_c: '', pilihan_d: '', jawaban_benar: 'a', penjelasan: '' })
  const [savingSoal, setSavingSoal] = useState(false)

  useEffect(() => {
    if (!id) return
    Promise.all([
      api.get(`/api/pertemuan/${id}`),
      api.get(`/api/topik/${id}`),
      api.get(`/api/kuis/guru/soal?pertemuan_id=${id}`),
    ]).then(([pRes, tRes, sRes]) => {
      setPertemuan(pRes.data.data)
      setTopikList(tRes.data.data || [])
      setSoalList(sRes.data.data || [])
    }).catch(() => {
      // Dummy fallback
      setPertemuan({ judul: 'Pertemuan', nomor_urut: 1 })
    }).finally(() => setLoading(false))
  }, [id])

  const tambahTopik = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!formTopik.judul) { toast.error('Judul harus diisi'); return }
    setSavingTopik(true)
    try {
      await api.post('/api/topik', { pertemuan_id: id, judul: formTopik.judul, isi_materi: formTopik.isi_materi, nomor_urut: topikList.length + 1 })
      const res = await api.get(`/api/topik/${id}`)
      setTopikList(res.data.data || [])
      setFormTopik({ judul: '', isi_materi: '' })
      setShowTopikForm(false)
      toast.success('Topik ditambahkan')
    } catch { toast.error('Gagal menambah topik') }
    finally { setSavingTopik(false) }
  }

  const hapusTopik = async (topikId: string) => {
    if (!confirm('Hapus topik ini?')) return
    try {
      await api.delete(`/api/topik/${topikId}`)
      setTopikList(prev => prev.filter(t => t.id !== topikId))
      toast.success('Topik dihapus')
    } catch { toast.error('Gagal menghapus') }
  }

  const tambahSoal = async (e: React.FormEvent) => {
    e.preventDefault()
    setSavingSoal(true)
    try {
      await api.post('/api/kuis/guru/soal', { ...formSoal, pertemuan_id: id })
      const res = await api.get(`/api/kuis/guru/soal?pertemuan_id=${id}`)
      setSoalList(res.data.data || [])
      setFormSoal({ pertanyaan: '', pilihan_a: '', pilihan_b: '', pilihan_c: '', pilihan_d: '', jawaban_benar: 'a', penjelasan: '' })
      setShowSoalForm(false)
      toast.success('Soal ditambahkan')
    } catch { toast.error('Gagal menambah soal') }
    finally { setSavingSoal(false) }
  }

  const hapusSoal = async (soalId: string) => {
    if (!confirm('Hapus soal ini?')) return
    try {
      await api.delete(`/api/kuis/guru/soal/${soalId}`)
      setSoalList(prev => prev.filter(s => s.id !== soalId))
      toast.success('Soal dihapus')
    } catch { toast.error('Gagal menghapus') }
  }

  if (loading) return <div className="flex items-center justify-center h-40 text-sm text-gray-400">Memuat...</div>

  return (
    <div>
      {/* Header */}
      <div className="flex items-center gap-3 mb-6">
        <button onClick={() => navigate('/pertemuan')} className="p-1.5 rounded-lg hover:bg-gray-200 transition">
          <ArrowLeft size={18} className="text-gray-600" />
        </button>
        <div>
          <p className="text-xs text-gray-400">Pertemuan {pertemuan?.nomor_urut}</p>
          <h1 className="text-xl font-bold text-gray-800">{pertemuan?.judul}</h1>
        </div>
      </div>

      {/* Tab */}
      <div className="flex gap-2 mb-6">
        {(['topik', 'kuis'] as const).map(t => (
          <button key={t} onClick={() => setTab(t)}
            className={`px-4 py-2 text-sm font-medium rounded-lg transition ${tab === t ? 'bg-[#2D7DD2] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'}`}>
            {t === 'topik' ? `Topik (${topikList.length})` : `Soal Kuis (${soalList.length})`}
          </button>
        ))}
      </div>

      {/* Tab Topik */}
      {tab === 'topik' && (
        <div>
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-sm font-semibold text-gray-700">Daftar Topik</h2>
            <button onClick={() => setShowTopikForm(!showTopikForm)}
              className="flex items-center gap-1.5 px-3 py-1.5 bg-[#2D7DD2] text-white text-sm rounded-lg">
              <Plus size={14} /> Tambah
            </button>
          </div>
          {showTopikForm && (
            <form onSubmit={tambahTopik} className="bg-white rounded-xl border border-gray-200 p-4 mb-4">
              <input placeholder="Judul topik" value={formTopik.judul} onChange={e => setFormTopik({...formTopik, judul: e.target.value})}
                className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-2 focus:border-[#2D7DD2] focus:outline-none" />
              <textarea placeholder="Isi materi..." value={formTopik.isi_materi} onChange={e => setFormTopik({...formTopik, isi_materi: e.target.value})}
                className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-3 h-24 resize-none focus:border-[#2D7DD2] focus:outline-none" />
              <div className="flex gap-2">
                <button type="submit" disabled={savingTopik} className="px-4 py-2 bg-[#2D7DD2] text-white text-sm rounded-lg disabled:opacity-50">
                  {savingTopik ? 'Menyimpan...' : 'Simpan'}
                </button>
                <button type="button" onClick={() => setShowTopikForm(false)} className="px-4 py-2 border border-gray-200 text-sm rounded-lg">Batal</button>
              </div>
            </form>
          )}
          <div className="space-y-2">
            {topikList.length === 0 ? (
              <div className="bg-white rounded-xl border border-gray-200 p-8 text-center text-sm text-gray-400">Belum ada topik</div>
            ) : topikList.map((t, i) => (
              <div key={t.id} className="bg-white rounded-xl border border-gray-200 px-4 py-3 flex items-center gap-3">
                <span className="w-6 h-6 bg-[#2D7DD2]/10 text-[#2D7DD2] rounded-md flex items-center justify-center text-xs font-bold">{i + 1}</span>
                <span className="flex-1 text-sm text-gray-800">{t.judul}</span>
                <button onClick={() => hapusTopik(t.id)} className="text-red-400 hover:text-red-600 p-1"><Trash2 size={14} /></button>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Tab Soal Kuis */}
      {tab === 'kuis' && (
        <div>
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-sm font-semibold text-gray-700">Soal Kuis</h2>
            <button onClick={() => setShowSoalForm(!showSoalForm)}
              className="flex items-center gap-1.5 px-3 py-1.5 bg-[#2D7DD2] text-white text-sm rounded-lg">
              <Plus size={14} /> Tambah Soal
            </button>
          </div>
          {showSoalForm && (
            <form onSubmit={tambahSoal} className="bg-white rounded-xl border border-gray-200 p-4 mb-4 space-y-2">
              <textarea placeholder="Pertanyaan" value={formSoal.pertanyaan} onChange={e => setFormSoal({...formSoal, pertanyaan: e.target.value})}
                className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm h-16 resize-none focus:border-[#2D7DD2] focus:outline-none" />
              {(['a','b','c','d'] as const).map(h => (
                <input key={h} placeholder={`Pilihan ${h.toUpperCase()}`} value={formSoal[`pilihan_${h}` as keyof typeof formSoal]}
                  onChange={e => setFormSoal({...formSoal, [`pilihan_${h}`]: e.target.value})}
                  className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#2D7DD2] focus:outline-none" />
              ))}
              <select value={formSoal.jawaban_benar} onChange={e => setFormSoal({...formSoal, jawaban_benar: e.target.value})}
                className="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#2D7DD2] focus:outline-none">
                <option value="a">Jawaban Benar: A</option>
                <option value="b">Jawaban Benar: B</option>
                <option value="c">Jawaban Benar: C</option>
                <option value="d">Jawaban Benar: D</option>
              </select>
              <div className="flex gap-2 pt-1">
                <button type="submit" disabled={savingSoal} className="px-4 py-2 bg-[#2D7DD2] text-white text-sm rounded-lg disabled:opacity-50">
                  {savingSoal ? 'Menyimpan...' : 'Simpan'}
                </button>
                <button type="button" onClick={() => setShowSoalForm(false)} className="px-4 py-2 border border-gray-200 text-sm rounded-lg">Batal</button>
              </div>
            </form>
          )}
          <div className="space-y-3">
            {soalList.length === 0 ? (
              <div className="bg-white rounded-xl border border-gray-200 p-8 text-center text-sm text-gray-400">Belum ada soal kuis</div>
            ) : soalList.map((s, i) => (
              <div key={s.id} className="bg-white rounded-xl border border-gray-200 p-4">
                <div className="flex items-start justify-between mb-3">
                  <p className="text-sm font-medium text-gray-800">{i + 1}. {s.pertanyaan}</p>
                  <button onClick={() => hapusSoal(s.id)} className="text-red-400 hover:text-red-600 ml-2 flex-shrink-0"><Trash2 size={14} /></button>
                </div>
                <div className="grid grid-cols-2 gap-1.5">
                  {(['a','b','c','d'] as const).map(h => (
                    <div key={h} className={`px-3 py-1.5 rounded-lg text-xs ${s.jawaban_benar === h ? 'bg-emerald-50 text-emerald-700 font-medium' : 'bg-gray-50 text-gray-600'}`}>
                      {h.toUpperCase()}. {s[`pilihan_${h}` as keyof Soal]} {s.jawaban_benar === h && '✓'}
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
