import React, { useEffect, useState } from 'react'
import { getTopSales } from '../../api/client.js'

export default function TopSalePage()
{
	const [days, setDays] = useState(7)
	const [rows, setRows] = useState([])

	const load = async (d=days)=> {

		const {data} = await getTopSales(d)
		setRows(data.rows || [])
	}

	useEffect(()=>{ load() }, [])

	return (

			<div className="card">
				<div className="card-header d-flex align-item-center">
					<div className="mr-2">Top Sales (by Qty)</div>
					<div className="ml-auto">
						<input className="form-control" style={{ width: 120}} type="number" value={days} onChange={e=>setDays(e.target.value)} />
					</div>
					<button className="btn btn-secondary ml-2" onclick={()=>load(days)}>Refresh</button>
				</div>

				<div className="card-body p-0">
					<table className="table table-sm mb-0">
						<thead className="thead-light">
							<tr><th>#</th><th>Products</th><th className="text-right">Qty</th><th className="text-right">Amount</th></tr>
						</thead>
						<tbody>
							{rows.map((r, i)=>(

								<tr key={i}>
									<td>{i + 1 }</td>
									<td>{r.product_name}</td>
									<td className="text-right">{r.qty}</td>
									<td className="text-right">${r.amount}</td>
								</tr>
								))}
							{rows.length === 0 && <tr><td colspan="4" className="text-center text-muted">No data found</td></tr>}
						</tbody>
					</table>
				</div>
			</div>
		)

}