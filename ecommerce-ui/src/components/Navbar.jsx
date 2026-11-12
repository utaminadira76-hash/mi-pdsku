// src/components/Navbar.jsx
// Navigasi atas + pemilih user aktif.

import { NavLink } from 'react-router-dom'

const USERS = [1, 2, 3]

export default function Navbar({ userId, onUserChange }) {
  return (
    <header className="navbar">
      <nav>
        <div className="brand">
          {/* Logo SVG Tas Belanja - Warna Hijau */}
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            width="28" 
            height="28" 
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="#10b981" 
            strokeWidth="2.5" 
            strokeLinecap="round" 
            strokeLinejoin="round" 
            className="logo-icon"
          >
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
          </svg>
          <strong>Toko Online</strong>
        </div>
        
        <div className="nav-links">
          <NavLink to="/" end>
            Produk
          </NavLink>
          <NavLink to="/cart">Keranjang</NavLink>
        </div>
      </nav>
      
      <label className="user">
        User ID
        <select
          value={userId}
          onChange={(e) => onUserChange(Number(e.target.value))}
        >
          {USERS.map((id) => (
            <option key={id} value={id}>
              User {id}
            </option>
          ))}
        </select>
      </label>
    </header>
  )
}