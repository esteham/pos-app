import React from 'react'

export default function PaymentSummary({ totals, discount, setDiscount, paidAmount, setPaidAmount, paymentMethod, setPaymentMethod, submitting, onSubmit, lastInvoice})
{
		
	const paid = Number(paidAmount || 0)
	const grand = Number(totals?.grand || 0)
	const change = Math.max(0, paid - grand)

	return (

				<div className="card">
					<div className="card-header">
						Payment Summary
					</div>

					<div className="card-body">
						<div className="d-flex justify-content-between">
							<div>Subtotal</div><div>&#2547;{totals.subtotal}</div>
						</div>

						<div className="d-flex justify-content-between">
							<div>Total VAT</div><div>&#2547;{totals.totalVat}</div>
						</div>
						<div className="form-group mt-2">
							<label>Discount</label>
							<input className="form-control" value={discount} onChange={e=>setDiscount(e.target.value)} />
						</div>

						<div className="d-flex justify-content-between">
							<div>Grand Total VAT</div><div>&#2547;{totals.grand}</div>
						</div>
						<hr />

						<div className="form-froup">
							<label>Paid Amount</label>
							<input className="form-control" value={paidAmount} onChange={ e=> setPaidAmount(e.target.value)} placeholder={totals.grand} />
						</div>

						<div className="form-group">
							<label>Change to Return</label>
							<input className="form-control" value={change.toFixed(2)} readOnly />
							<small className="text-muted">&#2547; Auto-calculated</small>
						</div>

						<div className="form-group">
						  <label>Payment Method</label>
						   <select className="form-control" value={paymentMethod} onChange={e=>setPaymentMethod(e.target.value)}>
							<option value="CASH">Cash</option>
							<option value="BKASH">bKash</option>
							<option value="CARD">Card</option>
						   </select>
						</div>

						<button className="btn btn-primary btn-block" disabled = {submitting} onClick={onSubmit}>{submitting ? 'Processing...' : 'Complete Sale (Enter)'}</button>

						{lastInvoice && (

							<div className="alert alert-success mt-3 mb-0">
								Last Invoice: <strong>{lastInvoice}</strong>
							</div>
						)}
					</div>
				</div>
			)
}