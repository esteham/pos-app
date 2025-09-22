import React from "react";
import { Link, Navigate } from "react-router-dom";

function safeParse(json) {
  try {
    return json ? JSON.parse(json) : null;
  } catch {
    return null;
  }
}

export default function AdminLayout({ children }) {
  const user = safeParse(localStorage.getItem("pos_user"));
  const rawRole = ((user && (user.role ?? user.user_type ?? user.type)) || "")
    .toString()
    .toLowerCase();
  const isAdmin = [
    "admin",
    "manager",
    "super admin",
    "super_admin",
    "superadmin",
  ].includes(rawRole);

  if (!isAdmin) {
    return <Navigate to="/pos" replace />;
  }
  return (
    <div className="container-fluid">
      <div className="row">
        <aside className="col-md-3 col-lg-2 bg-light border-right min-vh-100 p-0">
          <div className="p-3 border-bottom bg-white">
            <h5 className="mb-0">Admin</h5>
            <small className="text-muted">Dashboard & Management</small>
          </div>
          <nav className="nav flex-column p-2">
            <span className="text-uppercase text-muted small px-2 mt-2">Inventory</span>
            <Link className="nav-link" to="/admin/products">Products</Link>
            <Link className="nav-link" to="/admin/stock">Add Stock</Link>
            <Link className="nav-link" to="/admin/categories">Add Category</Link>

            <span className="text-uppercase text-muted small px-2 mt-3">Suppliers & Purchases</span>
            <Link className="nav-link" to="/admin/suppliers">Suppliers</Link>
            <Link className="nav-link" to="/admin/purchase/new">New Purchase</Link>
            <Link className="nav-link" to="/admin/purchases">Purchase Summary</Link>

            <span className="text-uppercase text-muted small px-2 mt-3">Reports</span>
            <Link className="nav-link" to="/admin/today">Today's Report</Link>
            <Link className="nav-link" to="/admin/top">Top Sales</Link>
            <Link className="nav-link" to="/admin/reports/sales">Sales Report</Link>

            <span className="text-uppercase text-muted small px-2 mt-3">POS</span>
            <Link className="nav-link" to="/pos">Go to POS</Link>
          </nav>
        </aside>
        <main className="col-md-9 col-lg-10 p-3">
          {children}
        </main>
      </div>
    </div>
  );
}
