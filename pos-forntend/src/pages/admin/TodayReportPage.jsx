import React, { useEffect, useState } from 'react'
import { getTodayReport } from '../../api/client.js'

export default function TodayReportPage()
{
	const [data, setData] = useState(null)

	useEffect(()=> { ( async ()=> {

		try { const { data } = await getTodayReport(); setData(data)} catch(_){}
	})()}, [])

	if(!data) return <div>Loading...</div>

	const s = data.stats

	return (

			<div className="row">
				<div className="col-lg-6">
					<div className="card mb-3">
						<div className="card-header">Today's Summary ({data.date})</div>
					</div>

						<div className="card-body">
							<div className="d-flex justify-content-between">
								<div>Orders</div>
								<div>{s.orders}</div>
							</div>
								<div className="d-flex justify-content-between">
									<div>Subtotal</div>
									<div>${s.subtotal}</div>
								</div>
								<div className="d-flex justify-content-between">
									<div>Total VAT</div>
									<div>${s.total_vat}</div>
								</div>
								<div className="d-flex justify-content-between">
									<div>Discount</div>
									<div>${s.discount}</div>
								</div>
								<div className="d-flex justify-content-between">
									<div>Grand Total</div>
									<div>${s.grand_total}</div>
								</div>
								<div className="d-flex justify-content-between">
									<div>Paid Amount</div>
									<div>${s.paid_amount}</div>
								</div>
							</div>
						</div>
					
					<div className="col-lg-6">
						<div className="card">
							<div className="card-header">
								Top Products (Qty)
							</div>

							<div className="card-body p-0">
								<table className="table table-sm mb-0">
									<thead className="thead-light">
										<tr><th>#</th><th>Product</th><th className="text-right">Qty</th><th className="text-right">Amount</th></tr>
									</thead>

									<tbody>
										{data.top.map((r, i)=>(

											<tr key={i}>
												<td>{ i + 1 }</td>
												<td>{ r.product_name }</td>
												<td className="text-right">{r.qty}
												</td>
												<td className="text-right">${r.amount}</td>
											</tr>
										))}

										{data.top.length === 0 &&  <tr><td colspan="4" className="text-center text-muted">No Sales</td></tr>}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

			
		)
}