import React from 'react'
import { Routes, Route, Link, useNavigate, Navigate } from 'react-router-dom'
import LoginPage from './components/auth/Login.jsx'
import POSPage from './components/pages/POSPage.jsx'
import { logout as doLogout } from './components/api/ApiAxios.jsx'


function RequireAuth({ children })
{
	const token = localStorage.getItem('pos_token')
	return token ? children : <Navigate to="/login" replace />
} 

export default function App()
{
	const navigate = useNavigate()
	const handleLogout = () =>
	{
		doLogout()
		navigate('/login')
	}

	return (

			<div>
				<nav className="navbar navbar-expand navbar-dark bg-dark">
					<Link className="navbar-brand" to="/">Live POS</Link>
					<div className="navbar-nav">
						<Link className="nav-item nav-link" to="/pos">POS</Link>
					</div>
					<div className="ml-auto navbar-nav">
						<button className="btn btn-sm btn-outline-light" onClick={handleLogout}>Logout</button>
					</div>	
				</nav>

				<div className="container-fluid py-3">
					<Routes>
						<Route path="/login" element={<LoginPage />} />
						<Route path="/" element={<RequireAuth> <POSPage /></RequireAuth>} />

						<Route path="/pos" element={<RequireAuth> <POSPage /></RequireAuth>} />
					</Routes>
				</div>
			</div>

			)
}