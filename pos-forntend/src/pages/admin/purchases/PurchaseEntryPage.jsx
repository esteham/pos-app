import React, { useState } from 'react'
import { createPurchase } from '../../../api/client.js'
import { listSuppliers } from '../../../api/client.js'
import ProductSearchBox  from '../../../components/ProductSearchBox.jsx'

export default function PurchaseEntryPage()
{
	const [supplierId, setSupplierId] = useState('')
	const [supplierList, setSupplierList] = useState([])
	const [refNo, setRefNo] = useState('')
	const [items, setItems] = useState([])
	const [discount, setDiscount] = useState('0')
	const [paidAmount, setPaidAmount] = useState('0')
	const [method, setMethod] = useState('CASH')
	const [remarks, setRemarks] = useState('')

	const loadSupplier = async () =>
	{
		if(supplierList.length) return
		const {data} = await listSuppliers({per_page: 100})
		setSupplierList(data.data || [])
	}

	const addProduct = (p) =>
	{
		const exists = items.find(x=>x.product_id===p.id)
		if(exists)
		{
			setItems(items.map(x=> x.product_id === p.id ? {...x, quantity: x.quantity + 1} : x))
		}

		else
		{
			setItems([...items, {
				product_id: p.id, product_name: p.name, unit:p.unit,
				quantity: 1, unit_price: Number(p.price), vat_percent: Number(p.vat_percent || 0)
			}])
		}
	}

	const updateQty = (i, q) => setItems(items.map((x, idx)=> idx===i ? {...x, quantity: Number(q || 0)} : x))

	const removeLine = (i) => setItems(items.filter((_,idx)=> idx!==i))

	const subtotal = items.reduce((s,x)=> s + x.unit_price * x.quantity, 0)
	const totalVat = items.reduce((s,x)=> s + (x.unit_price * x.quantity)*(x.vat_percent/100), 0)
	const grand = Math.max(0, subtotal + totalVat - Number(discount || 0))

	const submit = async () =>
	{
		if(!supplierId) return alert('Select Supplier')
			if(items.length === 0) return alert('No Items')
				const payload = {

					supplier_id: supplierId,
					reference_no: refNo || undefined,
					discount: Number(discount || 0),
					paid_amount: Number(paidAmount || 0),
					payment_method: method,
					remarks: remarks || undefined,
					items: items.map(x=>({

						product_id: x.product_id,
						quantity: x.quantity,
						unit_price: x.unit_price,
						unit: x.unit,
						vat_percent: x.vat_percent
					})) 
				}

				try 
				{
					const {data} = await createPurchase(payload)
					alert(`Purchase saved: ${data.purchase_no}`)
					setSupplierId(''); setRefNo(''); setItems([]); setDiscount('0'); setPaidAmount('0'); setRemarks(''); 
				} 
				catch(e) 
				{
					alert(e?.response?.data?.message || "Failed to save purchase")
				}
	}

	return (

		<div className="row">
			<div className="col-lg-5">
				<div className="card">
					<div className="card-header">
					Supplier & Add Items
					</div>
					<div className="card-body">
						<div className="form-group">
							<label>Supplier</label>
							<select className="form-control" value={supplierId} onChange={e=>setSupplierId(e.target.value)} onFocus={loadSupplier}>

								<option value="">---select Option </option>
								{supplierList.map(s=><option key={s.id} value={s.id}>{s.name}{s.company ? `(${s.company})`: ''}</option>)}
							</select>
						</div>

						<div className="form-group">
							<label>Reference No (Vendor bill no)</label>
							<input className="form-control" value={refNo} onChange={e=>setRefNo(e.target.value)} />
						</div>

						<ProductSearchBox onSelect={addProduct} placeholder="Search product to add..." />
					</div>
				</div>
			</div>

			<div className="col-lg-7">
				<div className="card">
					<div className="card-header">
						Purchase Items
					</div>
					<div className="card-body p-0">
						<table className="table table-sm mb-0">
							<thead className="thead-light">
								<tr>
									<th>#</th>
									<th>Product</th>
									<th>Unit</th>
									<th className="text-right">Price</th>
									<th className="text-right">Qty</th>
									<th className="text-right">VAT %</th>
									<th className="text-right">Total</th>
									<th></th>
								</tr>
							</thead>

							<tbody>
								{items.map((x,i)=>{

									const total = x.unit_price*x.quantity + (x.unit_price*x.quantity)*(x.vat_percent/100)

									return (

											<tr key={i}>
												<td>{i+1}</td>
												<td>{x.product_name}</td>
												<td>{x.unit}</td>
												<td className="text-right">
													<input type="number" step="0.01" className="form-control form-control-sm text-right" value={x.unit_price} onChange={e=>setItems(items.map((l, idx)=> idx===i?{...l, unit_price:Number(e.target.value||0)}:l))} />
												</td>

												<td className = "text-right" style={{ width: 110 }}>
												 <input type="number" step="0.01" className="form-control form-control-sm text-right" value={x.quantity} onChange={e=>updateQty(i, e.target.value)} />
												</td>

												<td className = "text-right" style={{ width: 90 }}>
												 <input type="number" step="0.01" className="form-control form-control-sm text-right" value={x.vat_percent} onChange={e=>setItems(items.map((l, idx)=> idx === i?{...l, vat_percent: Number(e.target.value||0)}:l))} />
												</td>

												<td className="text-right">
													${total.toFixed(2)}
												</td>

												<td>
													<button className="btn btn-sm btn-outline-danger" onClick={ ()=> removeLine(i)}>x</button>
												</td>
											</tr>
										)
								})}

								{items.length===0 && <tr><td colSpan="8" className="text-center text-muted">No Items</td></tr>}
							</tbody>
						</table>
					</div>
					
					<div className="card-footer">
						<div className="d-flex justify-content-between">
							<div>
								Subtotal
							</div>
							<div>
								${subtotal.toFixed(2)}
							</div>
						</div>

						<div className="d-flex justify-content-between">
							<div>
								Total VAT
							</div>
							<div>
								${totalVat.toFixed(2)}
							</div>
						</div>

						<div className="form-group mt-2">
							<label>Discount</label>
							<input className="form-control" value={discount} onChange={e=>setDiscount(e.target.value)} />
						</div>

						<div className="d-flex justify-content-between font-weight-bold">
							<div>
								Grand
							</div>
							<div>
								${grand.toFixed(2)}
							</div>
						</div>
						<div className="form-group mt-2">
							<label>Paid Amount</label>
							<input className="form-control" value={paidAmount} onChange={e=>setPaidAmount(e.target.value)} placeholder={grand.toFixed(2)} />
						</div>

						<div className="form-group">
							<label>Payment Method</label>
							<select className="form-control" value={method} onChange={e=>setMethod(e.target.value)}>
							
							<option value="CASH">Cash</option>
							<option value="BKASH">bKash</option>
							<option value="CARD">Card</option>
							<option value="BANK">Bank</option>
							<option value="due">Due</option>
							</select>
						</div>

						<div className="form-group">
							<label>Remarks</label>
							<input className="form-control" value={remarks} onChange={e=>setRemarks(e.target.value)} />
						</div>

						<button className="btn btn-primary" onClick={submit}>Save Purchase</button>
					</div>
				</div>
			</div>
		</div>

	)
}