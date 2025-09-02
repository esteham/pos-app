/* eslint-disable no-unused-vars */
import React from "react";

export default function PaymentSummary({
  totals,
  discount,
  setDiscount,
  paidAmount,
  setPaidAmount,
  paymentMethod,
  setPaymentMethod,
  submitting,
  onSubmit,
  lastInvoice,
}) {
  return (
    <div className="card">
      <div className="card-header">Payment Summary</div>

      <div className="card-body">
        <div className="d-flex justify-content-between">
          <div>Subtotal</div>
          <div>${totals.subtotal}</div>
        </div>

        <div className="d-flex justify-content-between">
          <div>Total VAT</div>
          <div>${totals.totalVat}</div>
        </div>
        <div className="form-group mt-2">
          <label>Discount</label>
          <input
            className="form-control"
            value={discount}
            onChange={(e) => setDiscount(e.target.value)}
          />
        </div>

        <div className="d-flex justify-content-between">
          <div>Grand Total VAT</div>
          <div>${totals.grand}</div>
        </div>
        <hr />

        <div className="form-group">
          <label>Payment Method</label>
          <select
            className="form-control"
            value={paymentMethod}
            onChange={(e) => setPaymentMethod(e.target.value)}
          >
            <option value="cash">Cash</option>
            <option value="internet_banking">bKash</option>
            <option value="credit_card">Card</option>
            <option value="bank_transfer">Bank</option>
          </select>
        </div>

        <button
          className="btn btn-primary btn-block"
          disabled={submitting}
          onClick={onSubmit}
        >
          {submitting ? "Processing..." : "Complete Sale (Enter)"}
        </button>

        {lastInvoice && (
          <div className="alert alert-success mt-3 mb-0">
            Last Invoice: <strong>{lastInvoice}</strong>
          </div>
        )}
      </div>
    </div>
  );
}
