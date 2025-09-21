import React from 'react'
import { round2 } from '../utils/calc.js'

export default function CartTable({ lines, updateQty, removeLine })
{
	const thumbStyle = {

		width: 40, height: 40, objectFit: 'cover', borderRadius: 4, border: '1px solid #e5e5e5', background: '#fff'
	}

	return (

				<div className="card">
					<div className="card-header">
						Cart
					</div>
					<div className="card-body p-0">
						<div className="table-responsive">
							<table className="table table-sm table-striped mb-0">
								<thead className="thead-light">
									<tr>
										<th>#</th>
										<th>Img</th>
										<th>Product</th>
										<th className="text-right">Unit Price</th>
										<th className="text-right">Qty/KG</th>
										<th className="text-right">VAT %</th>
										<th className="text-right">Line Total</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									{lines.map((l, i) => {

										const line = l.unit_price * l.quantity
										const vat = line * (l.vat_percent || 0)/100
										const total = line + vat

										return (

													<tr key={i}>
													  <td>{ i + 1 }</td>
													  <td style={{ width: 50}}>
													  	{l.image_url ? <img src={l.image_url} alt={l.name} style={thumbStyle} /> : <div style={{ ...thumbStyle, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 10, color: '#999'}}>N/A</div>}
													  </td>

													  <td>
													  	<div>{l.name}</div>
													  	<small className="text-muted">Unit: {l.unit} | Stock: {l.stock}</small>
													  </td>

													  <td className="text-right">${round2(l.unit_price)}</td>
													  <td className="text-right" style={{ width: 120 }}>
													  	<input className="form-control form-control-sm text-right" value={l.quantity} onChange={(e)=>updateQty(i, e.target.value)} />
													  </td>

													  <td className="text-right">{round2(l.vat_percent || 0)}%</td>

													  <td className="text-right">${round2(total)}</td>

													  <td className="text-right">
													  	<button className="btn btn-sm btn-outline-danger" onClick={()=>removeLine(i)}>x</button>
													  </td>
													 </tr>
												)									})}
									{lines.length === 0 && (
										<tr>
											<td colSpan="8" className="text-center text-muted py-4">Cart is Empty</td>
										</tr>
									)}
								</tbody>
							</table>
						</div>	
					</div>	
				</div>
			)
}