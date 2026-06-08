import { Routes, Route, Navigate } from 'react-router-dom'
import Layout from './components/Layout'
import LoginPage from './pages/LoginPage'
import DashboardPage from './pages/DashboardPage'
import PertemuanPage from './pages/PertemuanPage'
import DetailPertemuanPage from './pages/DetailPertemuanPage'
import SiswaPage from './pages/SiswaPage'
import RagPage from './pages/RagPage'

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const token = localStorage.getItem('token')
  if (!token) return <Navigate to="/login" replace />
  return <Layout>{children}</Layout>
}

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route path="/" element={<ProtectedRoute><DashboardPage /></ProtectedRoute>} />
      <Route path="/pertemuan" element={<ProtectedRoute><PertemuanPage /></ProtectedRoute>} />
      <Route path="/pertemuan/:id" element={<ProtectedRoute><DetailPertemuanPage /></ProtectedRoute>} />
      <Route path="/siswa" element={<ProtectedRoute><SiswaPage /></ProtectedRoute>} />
      <Route path="/rag" element={<ProtectedRoute><RagPage /></ProtectedRoute>} />
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}
