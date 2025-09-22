import React from "react";
import { Link } from "react-router-dom";

export default function AdminDashboard() {
  return (
    <div>
      <h3 className="mb-3">Admin Dashboard</h3>
      <div className="row">
        <div className="col-md-4 mb-3">
          <div className="card h-100">
            <div className="card-body">
              <h5 className="card-title">Inventory</h5>
              <ul className="mb-0">
                <li><Link to="/admin/products">Manage Products</Link></li>
                <li><Link to="/admin/stock">Add Stock</Link></li>
                <li><Link to="/admin/categories">Add Category</Link></li>
              </ul>
            </div>
          </div>
        </div>
        <div className="col-md-4 mb-3">
          <div className="card h-100">
            <div className="card-body">
              <h5 className="card-title">Suppliers & Purchases</h5>
              <ul className="mb-0">
                <li><Link to="/admin/suppliers">Suppliers</Link></li>
                <li><Link to="/admin/purchase/new">New Purchase</Link></li>
                <li><Link to="/admin/purchases">Purchase Summary</Link></li>
              </ul>
            </div>
          </div>
        </div>
        <div className="col-md-4 mb-3">
          <div className="card h-100">
            <div className="card-body">
              <h5 className="card-title">Reports</h5>
              <ul className="mb-0">
                <li><Link to="/admin/today">Today's Report</Link></li>
                <li><Link to="/admin/top">Top Sales</Link></li>
                <li><Link to="/admin/reports/sales">Sales Report</Link></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
