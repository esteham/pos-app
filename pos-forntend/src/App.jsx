import React from 'react'
import { Routes, Route, Link, useNavigate, Navigate } from 'react-router-dom'
import LoginPage from './pages/LoginPage.jsx'
import POSPage from './pages/POSPage.jsx'
import { logout as doLogout } from './api/client.js'

import AddStockPage from './pages/admin/AddStockPage.jsx'
import TodayReportPage from './pages/admin/TodayReportPage.jsx'
import TopSalePage from './pages/admin/TopSalePage.jsx'
import SalesReportPage from './pages/admin/SalesReportPage.jsx'
import PurchaseSummaryPage from './pages/admin/PurchaseSummaryPage.jsx'
import ProductFormPage from './pages/admin/products/ProductFormPage.jsx'
import ProductsListPage from './pages/admin/products/ProductsListPage.jsx'

import SupplierListPage from './pages/admin/suppliers/SupplierListPage.jsx'
import SupplierFormPage from './pages/admin/suppliers/SupplierFormPage.jsx'
import PurchaseEntryPage from './pages/admin/purchases/PurchaseEntryPage.jsx'

function RequireAuth({ children })
{
	const token = localStorage.getItem('pos_token')
	return token ? children : <Navigate to="/login" replace />
}

function safeParse(json)
{
	try { return json ? JSON.parse(json): null} catch {return null }
}

export default function App()
{
	const navigate = useNavigate()
	const handleLogout = () =>
	{
		doLogout()
		localStorage.removeItem('pos_user')
		navigate('/login')
	}

	const user = safeParse(localStorage.getItem('pos_user'))

	const rawRole = ((user && (user.role ?? user.user_type ?? user.type)) || '').toString().toLowerCase()

	const isAdmin = ['admin','manager','super admin','super_admin','superadmin'].includes(rawRole)

	return (

			<div>
				<nav className="navbar navbar-expand navbar-dark bg-dark">
					<Link className="navbar-brand" to="/">Live POS</Link>
					<div className="navbar-nav">
						<Link className="nav-item nav-link" to="/pos">POS</Link>

						{isAdmin && (

							<div className="nav-item dropdown">
								<a
								 className="nav-link dropdown-toggle"
								 href="#"
								 id="adminMenu"
								 role="button"
								 data-toggle="dropdown"
								 aria-haspopup="true"
								 aria-expanded="false"
								>Admin
								</a>

								<div className="dropdown-menu" aria-labelledby="adminMenu">
									<Link className="dropdown-item" to="/admin/products">Products</Link>

									<Link className="dropdown-item" to="/admin/stock">Add Stock</Link>
									<div className="dropdown-divider"></div>
									<Link className="dropdown-item" to="/admin/today">Today's Report</Link>
									<Link className="dropdown-item" to="/admin/top">Top Sales</Link>
									<Link className="dropdown-item" to="/admin/reports/sales">Sales Report</Link>
									<Link className="dropdown-item" to="/admin/purchases">Purchase Summary</Link>

									<Link className="dropdown-item" to="/admin/suppliers">Suppliers</Link>
									<Link className="dropdown-item" to="/admin/purchase/new">New Purchase</Link>
								</div>
							</div>
						)}
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

						<Route path="/admin/stock" element={<RequireAuth><AddStockPage /></RequireAuth>} />
						<Route path="/admin/today" element={<RequireAuth><TodayReportPage /></RequireAuth>} />
						<Route path="/admin/top" element={<RequireAuth><TopSalePage /></RequireAuth>} />
						<Route path="/admin/reports/sales" element={<RequireAuth><SalesReportPage /></RequireAuth>} />
						<Route path="/admin/purchases" element={<RequireAuth><PurchaseSummaryPage /></RequireAuth>} /> 

						<Route path="/admin/products" element={<RequireAuth><ProductsListPage /></RequireAuth>} />

						<Route path="/admin/products/new" element={<RequireAuth><ProductFormPage mode="create" /></RequireAuth>} />

						<Route path="/admin/products/:id/edit" element={<RequireAuth><ProductFormPage mode="edit" /></RequireAuth>} />

						<Route path="/admin/suppliers" element={<RequireAuth><SupplierListPage mode="create" /></RequireAuth>} />
						<Route path="/admin/suppliers/new" element={<RequireAuth><SupplierFormPage mode="create" /></RequireAuth>} />
						<Route path="/admin/suppliers/:id/edit" element={<RequireAuth><SupplierFormPage mode="create" /></RequireAuth>} />
						<Route path="/admin/purchase/new" element={<RequireAuth><PurchaseEntryPage mode="create" /></RequireAuth>} />
					</Routes>
				</div>
			</div>

			)
}