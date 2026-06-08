import { useState } from 'react'
import { Upload, FileText, CheckCircle, Database, Trash2, Loader2 } from 'lucide-react'
import toast from 'react-hot-toast'

type Document = {
  id: string
  nama: string
  ukuran: string
  tanggal: string
  chunks: number
  status: 'proses' | 'siap'
}

const DUMMY_DOCUMENTS: Document[] = [
  { id: '1', nama: 'Modul_01_Pengenalan_Jaringan.pdf', ukuran: '2.4 MB', tanggal: '01 Juni 2026', chunks: 45, status: 'siap' },
  { id: '2', nama: 'Jobsheet_02_IP_Addressing.pdf', ukuran: '1.8 MB', tanggal: '03 Juni 2026', chunks: 32, status: 'siap' },
  { id: '3', nama: 'Modul_03_Konfigurasi_IP.pdf', ukuran: '3.1 MB', tanggal: '05 Juni 2026', chunks: 58, status: 'siap' },
]

export default function RagPage() {
  const [documents, setDocuments] = useState<Document[]>(DUMMY_DOCUMENTS)
  const [uploading, setUploading] = useState(false)
  const [dragActive, setDragActive] = useState(false)
  const [prosesStep, setProsesStep] = useState('')

  const handleDrag = (e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    if (e.type === "dragenter" || e.type === "dragover") {
      setDragActive(true)
    } else if (e.type === "dragleave") {
      setDragActive(false)
    }
  }

  const simulateProcessing = async (fileName: string, fileSize: string) => {
    setUploading(true)
    
    const steps = [
      'Mengunggah file ke server...',
      'Melakukan parsing teks PDF...',
      'Melakukan chunking dokumen (pemotongan teks)...',
      'Membuat text embedding vector...',
      'Melakukan indexing ke ChromaDB Vector Store...'
    ]

    for (const step of steps) {
      setProsesStep(step)
      await new Promise(r => setTimeout(r, 1000))
    }

    const newDoc: Document = {
      id: Date.now().toString(),
      nama: fileName,
      ukuran: fileSize,
      tanggal: new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }),
      chunks: Math.floor(Math.random() * 40) + 15,
      status: 'siap'
    }

    setDocuments(prev => [newDoc, ...prev])
    setUploading(false)
    setProsesStep('')
    toast.success(`Dokumen ${fileName} berhasil di-index ke database RAG!`)
  }

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    setDragActive(false)

    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      const file = e.dataTransfer.files[0]
      if (file.type !== "application/pdf") {
        toast.error("Hanya file PDF yang diperbolehkan!")
        return
      }
      const sizeMB = (file.size / (1024 * 1024)).toFixed(1) + " MB"
      simulateProcessing(file.name, sizeMB)
    }
  }

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      const file = e.target.files[0]
      if (file.type !== "application/pdf") {
        toast.error("Hanya file PDF yang diperbolehkan!")
        return
      }
      const sizeMB = (file.size / (1024 * 1024)).toFixed(1) + " MB"
      simulateProcessing(file.name, sizeMB)
    }
  }

  const handleDelete = (id: string, name: string) => {
    setDocuments(prev => prev.filter(doc => doc.id !== id))
    toast.success(`Dokumen ${name} berhasil dihapus dari database RAG.`)
  }

  const totalChunks = documents.reduce((acc, curr) => acc + curr.chunks, 0)

  return (
    <div>
      <h1 className="text-xl font-bold text-gray-800">Knowledge Base RAG</h1>
      <p className="text-sm text-gray-500 mt-1">Unggah modul dan jobsheet PDF sebagai sumber otak AI Tutor</p>

      {/* Info Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div className="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
          <div className="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
            <FileText size={18} />
          </div>
          <div>
            <p className="text-lg font-bold text-gray-800">{documents.length}</p>
            <p className="text-xs text-gray-500">Total Dokumen PDF</p>
          </div>
        </div>
        <div className="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
          <div className="w-9 h-9 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
            <Database size={18} />
          </div>
          <div>
            <p className="text-lg font-bold text-gray-800">{totalChunks}</p>
            <p className="text-xs text-gray-500">Total Vector Chunks</p>
          </div>
        </div>
        <div className="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
          <div className="w-9 h-9 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center">
            <CheckCircle size={18} />
          </div>
          <div>
            <p className="text-lg font-bold text-gray-800">ChromaDB</p>
            <p className="text-xs text-gray-500">Vector Store Status: Connected</p>
          </div>
        </div>
      </div>

      {/* Upload Zone */}
      <div className="mt-8">
        <div
          onDragEnter={handleDrag}
          onDragOver={handleDrag}
          onDragLeave={handleDrag}
          onDrop={handleDrop}
          className={`
            border-2 border-dashed rounded-xl p-8 text-center flex flex-col items-center justify-center transition
            ${dragActive ? 'border-blue-500 bg-blue-50/50' : 'border-gray-200 bg-white hover:border-blue-400'}
            ${uploading ? 'pointer-events-none opacity-80' : ''}
          `}
        >
          {uploading ? (
            <div className="flex flex-col items-center py-4">
              <Loader2 size={32} className="text-blue-500 animate-spin mb-3" />
              <p className="text-sm font-semibold text-gray-700">{prosesStep}</p>
              <p className="text-xs text-gray-400 mt-1">Harap tunggu, sistem sedang memproses data vektor...</p>
            </div>
          ) : (
            <>
              <div className="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-3">
                <Upload size={22} />
              </div>
              <p className="text-sm font-semibold text-gray-700">Tarik & lepas file PDF di sini</p>
              <p className="text-xs text-gray-400 mt-1 mb-4">atau klik untuk memilih file dari komputer Anda (Maksimal 10 MB)</p>
              <label className="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 cursor-pointer transition">
                Pilih File PDF
                <input type="file" accept=".pdf" className="hidden" onChange={handleFileChange} />
              </label>
            </>
          )}
        </div>
      </div>

      {/* Document List */}
      <div className="mt-8">
        <h2 className="text-sm font-semibold text-gray-800 mb-3">Dokumen Knowledge Base Terdaftar</h2>
        <div className="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 overflow-hidden">
          {documents.length === 0 ? (
            <div className="p-8 text-center text-gray-400 text-sm">Belum ada dokumen yang diunggah.</div>
          ) : (
            documents.map(doc => (
              <div key={doc.id} className="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                <div className="flex items-center gap-3">
                  <div className="w-8 h-8 bg-red-50 text-red-500 rounded flex items-center justify-center">
                    <FileText size={18} />
                  </div>
                  <div>
                    <p className="text-sm font-medium text-gray-800">{doc.nama}</p>
                    <p className="text-xs text-gray-400 mt-0.5">
                      {doc.ukuran} · {doc.tanggal} · <span className="font-medium text-blue-600">{doc.chunks} chunks</span>
                    </p>
                  </div>
                </div>
                <div className="flex items-center gap-3">
                  <span className="px-2.5 py-0.5 text-xs font-medium text-green-700 bg-green-50 rounded-full">
                    Ready
                  </span>
                  <button
                    onClick={() => handleDelete(doc.id, doc.nama)}
                    className="p-1 text-gray-400 hover:text-red-500 rounded hover:bg-gray-100 transition"
                    title="Hapus Dokumen"
                  >
                    <Trash2 size={16} />
                  </button>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  )
}
