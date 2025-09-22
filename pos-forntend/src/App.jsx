import React from "react";
import { Routes, Route, Link, useNavigate, Navigate } from "react-router-dom";
import LoginPage from "./pages/LoginPage.jsx";
import POSPage from "./pages/POSPage.jsx";
import { logout as doLogout } from "./api/client.js";

import AddStockPage from "./pages/admin/AddStockPage.jsx";
import TodayReportPage from "./pages/admin/TodayReportPage.jsx";
import TopSalePage from "./pages/admin/TopSalePage.jsx";
import SalesReportPage from "./pages/admin/SalesReportPage.jsx";
import PurchaseSummaryPage from "./pages/admin/PurchaseSummaryPage.jsx";
import ProductFormPage from "./pages/admin/products/ProductFormPage.jsx";
import ProductsListPage from "./pages/admin/products/ProductsListPage.jsx";
import AddCategoriesPage from "./pages/admin/AddCategoriesPage.jsx";

import SupplierListPage from "./pages/admin/suppliers/SupplierListPage.jsx";
import SupplierFormPage from "./pages/admin/suppliers/SupplierFormPage.jsx";
import PurchaseEntryPage from "./pages/admin/purchases/PurchaseEntryPage.jsx";
import AdminLayout from "./components/AdminLayout.jsx";
import AdminDashboard from "./pages/admin/AdminDashboard.jsx";

function RequireAuth({ children }) {
  const token = localStorage.getItem("pos_token");
  return token ? children : <Navigate to="/login" replace />;
}

function safeParse(json) {
  try {
    return json ? JSON.parse(json) : null;
  } catch {
    return null;
  }
}

export default function App() {
  const navigate = useNavigate();
  const handleLogout = () => {
    doLogout();
    localStorage.removeItem("pos_user");
    navigate("/login");
  };

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

  return (
    <div>
      <nav className="navbar navbar-expand navbar-dark bg-dark">
        <Link className="navbar-brand" to="/">
          Live POS
        </Link>
        <div className="navbar-nav">
          <Link className="nav-item nav-link" to="/pos">POS</Link>
          {isAdmin && <Link className="nav-item nav-link" to="/admin">Admin</Link>}
        </div>

        <div className="ml-auto navbar-nav">
          <button
            className="btn btn-sm btn-outline-light"
            onClick={handleLogout}
          >
            Logout
          </button>
        </div>
      </nav>

      <div className="container-fluid py-3">
        <Routes>
          <Route path="/login" element={<LoginPage />} />
          <Route
            path="/"
            element={
              <RequireAuth>
                {isAdmin ? <Navigate to="/admin" replace /> : <Navigate to="/pos" replace />}
              </RequireAuth>
            }
          />

          <Route
            path="/pos"
            element={
              <RequireAuth>
                {" "}
                <POSPage />
              </RequireAuth>
            }
          />

          <Route
            path="/admin"
            element={
              <RequireAuth>
                <AdminLayout>
                  <AdminDashboard />
                </AdminLayout>
              </RequireAuth>
            }
          />

          <Route
            path="/admin/stock"
            element={
              <RequireAuth>
                <AdminLayout>
                  <AddStockPage />
                </AdminLayout>
              </RequireAuth>
            }
          />

          <Route
            path="/admin/categories"
            element={
              <RequireAuth>
                <AdminLayout>
                  <AddCategoriesPage />
                </AdminLayout>
              </RequireAuth>
            }
          />
          <Route
            path="/admin/today"
            element={
              <RequireAuth>
                <AdminLayout>
                  <TodayReportPage />
                </AdminLayout>
              </RequireAuth>
            }
          />
          <Route
            path="/admin/top"
            element={
              <RequireAuth>
                <AdminLayout>
                  <TopSalePage />
                </AdminLayout>
              </RequireAuth>
            }
          />
          <Route
            path="/admin/reports/sales"
            element={
              <RequireAuth>
                <AdminLayout>
                  <SalesReportPage />
                </AdminLayout>
              </RequireAuth>
            }
          />
          <Route
            path="/admin/purchases"
            element={
              <RequireAuth>
                <AdminLayout>
                  <PurchaseSummaryPage />
                </AdminLayout>
              </RequireAuth>
            }
          />

          <Route
            path="/admin/products"
            element={
              <RequireAuth>
                <AdminLayout>
                  <ProductsListPage />
                </AdminLayout>
              </RequireAuth>
            }
          />

          <Route
            path="/admin/products/new"
            element={
              <RequireAuth>
                <AdminLayout>
                  <ProductFormPage mode="create" />
                </AdminLayout>
              </RequireAuth>
            }
          />

          <Route
            path="/admin/products/:id/edit"
            element={
              <RequireAuth>
                <AdminLayout>
                  <ProductFormPage mode="edit" />
                </AdminLayout>
              </RequireAuth>
            }
          />

          <Route
            path="/admin/suppliers"
            element={
              <RequireAuth>
                <AdminLayout>
                  <SupplierListPage mode="create" />
                </AdminLayout>
              </RequireAuth>
            }
          />
          <Route
            path="/admin/suppliers/new"
            element={
              <RequireAuth>
                <AdminLayout>
                  <SupplierFormPage mode="create" />
                </AdminLayout>
              </RequireAuth>
            }
          />
          <Route
            path="/admin/suppliers/:id/edit"
            element={
              <RequireAuth>
                <AdminLayout>
                  <SupplierFormPage mode="create" />
                </AdminLayout>
              </RequireAuth>
            }
          />
          <Route
            path="/admin/purchase/new"
            element={
              <RequireAuth>
                <AdminLayout>
                  <PurchaseEntryPage mode="create" />
                </AdminLayout>
              </RequireAuth>
            }
          />
        </Routes>
      </div>
    </div>
  );
}
