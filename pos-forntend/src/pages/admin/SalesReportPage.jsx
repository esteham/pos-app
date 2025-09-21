import React, { useState } from 'react'
import { getSalesReport } from '../../api/client.js'

export default function SalesReportPage()
{
	const [from, setFrom] = useState('')
	const [to, setTo] = useState('')
	const [summary, setSummary] = useState(null)
	const [rows, setRows] = useState([])

	const run = async ()=>
	{
		const { data } = await getSalesReport({ from: from || undefined, to: to || undefined})
		setSummary(data.summary);
		setRows(data.rows || [])
	}

	return (

			<div className="card">
				<div className="card-header">
					<div className="form-inline">
						<label className="mr-2">From</label>
						<input type="date" className="form-control mr-2" value={from} onChange={e=>setFrom(e.target.value)} />

						<label className="mr-2">To</label>
						<input type="date" className="form-control mr-2" value={to} onChange={e=>setTo(e.target.value)} />
						<button className="btn btn-primary" onClick={run}>Run</button>
					</div>
				</div>

				<div className="card-body">
					{summary && (

						<div className="mb-3">
							<div className="d-flex justify-content-between">
								<div>Orders</div><div>{summary.orders}</div>
							</div>

							<div className="d-flex justify-content-between">
								<div>Grand Total</div><div>${summary.grand_total}</div>
							</div>

							<div className="d-flex justify-content-between">
								<div>Paid Total</div><div>{summary.paid_total}</div>
							</div>

							<div className="d-flex justify-content-between">
								<div>Discount</div><div>{summary.discount}</div>
							</div>
						</div>
					)}

					<div className="table-responsive">
						<table className="table table-sm">
							<thead className="thead-light">
								<tr>
									<th>#</th>
									<th>Invoice</th>
									<th>Date</th>
									<th className="text-right">Grand</th>
									<th className="text-right">Paid</th>
									<th>Method</th>
									<th>Customer</th>
								</tr>
							</thead>
							<tbody>
								{rows.map((r, i)=>(

									<tr key={r.invoice_no}>
										<td>{i+1}</td>
										<td>{r.invoice_no}</td>
										<td>{new Date(r.created_at).toLocaleString()}</td>
										<td className="text-right">${r.grand_total}</td>
										<td className="text-right">${r.paid_amount}</td>
										<td>{r.payment_method}</td>
										<td>{r.customer_phone || '_'}</td>
									</tr>
								))}

								{rows.length === 0 && <tr><td colSpan="7" className="text-center text-muted">No data found</td></tr>}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		)
}