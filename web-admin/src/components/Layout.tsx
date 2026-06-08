import { NavLink, useNavigate, useLocation } from 'react-router-dom'
import { LayoutDashboard, BookOpen, Users, Database, LogOut, Menu, X } from 'lucide-react'
import { useState } from 'react'

const menuItems = [
  { to: '/', icon: LayoutDashboard, label: 'Dashboard' },
  { to: '/pertemuan', icon: BookOpen, label: 'Pertemuan' },
  { to: '/siswa', icon: Users, label: 'Data Siswa' },
  { to: '/rag', icon: Database, label: 'Knowledge Base RAG' },
]

// Layout utama — sidebar + topbar + konten
export default function Layout({ children }: { children: React.ReactNode }) {
  const navigate = useNavigate()
  const location = useLocation()
  const [sidebarOpen, setSidebarOpen] = useState(false)

  const user = JSON.parse(localStorage.getItem('user') ?? '{}')

  const logout = () => {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    navigate('/login')
  }

  const getBreadcrumb = () => {
    const path = location.pathname
    if (path === '/') return 'Dashboard'
    if (path.startsWith('/pertemuan')) return 'Pertemuan'
    if (path.startsWith('/siswa')) return 'Data Siswa'
    if (path.startsWith('/rag')) return 'Knowledge Base RAG'
    return 'Netlabs'
  }

  return (
    <div className="flex h-screen bg-[#F8F9FB] overflow-hidden">
      {/* Overlay mobile */}
      {sidebarOpen && (
        <div className="fixed inset-0 bg-black/40 z-20 lg:hidden"
          onClick={() => setSidebarOpen(false)} />
      )}

      {/* Sidebar */}
      <aside className={`
        fixed lg:relative z-30 h-full w-56 bg-[#1A2B5F] text-white flex flex-col
        transition-transform duration-200
        ${sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
      `}>
        {/* Logo */}
        <div className="p-5 border-b border-white/10 flex items-center justify-between">
          <div>
            <h1 className="text-base font-bold tracking-tight">Netlabs</h1>
            <p className="text-[11px] text-white/40 mt-0.5">Panel Guru</p>
          </div>
          <button onClick={() => setSidebarOpen(false)} className="lg:hidden text-white/40 hover:text-white">
            <X size={18} />
          </button>
        </div>

        {/* Menu */}
        <nav className="flex-1 p-3 space-y-0.5 overflow-y-auto">
          {menuItems.map(({ to, icon: Icon, label }) => (
            <NavLink key={to} to={to} end={to === '/'}
              onClick={() => setSidebarOpen(false)}
              className={({ isActive }) =>
                `flex items-center gap-2.5 px-3 py-2.5 text-[13px] rounded-lg transition
                ${isActive ? 'bg-[#2D7DD2] text-white' : 'text-white/60 hover:text-white hover:bg-white/8'}`
              }>
              <Icon size={16} />
              {label}
            </NavLink>
          ))}
        </nav>

        {/* Profil guru */}
        <div className="p-3 border-t border-white/10">
          <div className="flex items-center gap-2.5 px-2 py-1.5 mb-1">
            <div className="w-7 h-7 bg-white/10 rounded-full flex items-center justify-center text-xs font-medium">
              {(user.nama?.[0] ?? 'G').toUpperCase()}
            </div>
            <div className="min-w-0">
              <p className="text-[12px] font-medium truncate">{user.nama ?? 'Guru'}</p>
              <p className="text-[11px] text-white/40">Pengajar</p>
            </div>
          </div>
          <button onClick={logout}
            className="flex items-center gap-2 w-full px-3 py-2 text-[12px] text-white/50 hover:text-white rounded-lg hover:bg-white/8 transition">
            <LogOut size={14} /> Keluar
          </button>
        </div>
      </aside>

      {/* Konten */}
      <div className="flex-1 flex flex-col min-w-0 overflow-hidden">
        {/* Topbar */}
        <header className="h-14 bg-white border-b border-gray-200 flex items-center px-4 gap-3 flex-shrink-0">
          <button onClick={() => setSidebarOpen(true)} className="lg:hidden text-gray-500 hover:text-gray-700">
            <Menu size={20} />
          </button>
          <h2 className="text-sm font-semibold text-gray-700">{getBreadcrumb()}</h2>
        </header>

        <main className="flex-1 overflow-y-auto p-4 md:p-6">
          {children}
        </main>
      </div>
    </div>
  )
}
