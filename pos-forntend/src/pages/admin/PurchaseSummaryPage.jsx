import React, { useEffect, useMemo, useState } from 'react'
import { listPurchase, getPurchaseSummary, listSuppliers} from '../../api/client.js'

export default function PurchaseSummaryPage()
{
	
	const [from, setFrom] = useState('')
	const [to, setTo] = useState('')
	const [supplierId, setSupplierId] = useState('')
	const [suppliers, setSuppliers] = useState([])
	const [rows, setRows] = useState([])
	const [meta, setMeta] = useState(null)
	const [sum, setSum] = useState(null)
	const [loading, setLoading] = useState(false)

	const loadSuppliers = async () =>
	{
		if(suppliers.length) return
			const { data } = await listSuppliers({ per_page: 100})
		setSuppliers(data.data || [])
	}

	const params = useMemo(() => ({

		from: from || undefined,
		to: to || undefined,
		supplier_id: supplierId || undefined,
		per_page: 20,		
	}), [from, to, supplierId])

	const load = async (page = 1) =>
	{
		setLoading(true)
		try 
		{
			const [listRes, sumRes] = await Promise.all([

					listPurchase({...params, page}),
					getPurchaseSummary(params),
				])
			setRows(listRes.data?.data || [])
			setMeta(listRes.data || null)
			setSum(sumRes.data?.summary || null)
		} 
		catch(e) 
		{
			alert(e?.response?.data?.message || 'Failed to load')
		}

		finally
		{
			setLoading(false)
		}		

	}

	useEffect(() => { load(1) }, [])

	const run = () => load(1)

	const reset = () =>
	{
		setFrom(''); setTo(''); setSupplierId('')
		setTimeout(()=>load(1), 0)
	}

	const csvExport = () =>
	{
		const header = ['Date','Purchase No','Supplier', 'Method', 'Reference', 'Items', 'Grand', 'Paid', 'Due']

		const lines = rows.map(r => {

			const date = new Date(r.purchase_date || r.created_at).toLocaleString()
			const supp = r.supplier ? (r.supplier.name + (r.supplier.company ? `(${r.supplier.company})` : '')) : ''

			const items = r.items_count ?? ''
			const grand = Number(r.grand_total || 0).toFixed(2)
			const paid = Number(r.paid_amount || 0).toFixed(2)
			const due = (Number(r.grand_total || 0) - Number(r.paid_amount || 0)).toFixed(2)

			return [date, r.purchase_no, supp, r.payment_method, r.reference_no || '', items, grand, paid, due]
		})

		const footer = sum ? [

					[],[],[],[], 'Totals:',
					(rows.reduce((s, r)=>s + (r.items_count|| 0), 0)).toString(),
					sum.grand_total.toFixed(2),
					sum.paid_total.toFixed(2),
					sum.due_total.toFixed(2)
				] : null

		const data = [header, ...lines]

		if(footer) data.push(footer)

		const csv = data.map(row =>

				row.map(v => {

					const s = (v ?? '').toString()
					return /[",\n"]/.test(s) ? `"${s.replace(/"/g,'""')}"`: s
				}).join(',')
			).join('\n')

		const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
		const url = URL.createObjectURL(blob);
		const a = document.createElement('a')
		a.href = url
		a.download = `purchase_summary_${Date.now()}.csv`
		a.click()
		URL.revokeObjectURL(url)
	}


	return (

			<div className="card">
				<div className="card-header">
					<div className="form-row">
						<div className="form-group col-md-3">
							<label>From</label>
							<input type="date" className="form-control" value={from} onChange={e=>setFrom(e.target.value)} />
						</div>

						<div className="form-group col-md-3">
							<label>To</label>
							<input type="date" className="form-control" value={to} onChange={e=>setTo(e.target.value)} />
						</div>

						<div className="form-group col-md-4">
							<label>Supplier</label>
							<select className="form-control" value={supplierId} onChange={e=>setSupplierId(e.target.value)} onFocus={loadSuppliers}>

								<option value="">---All Suppliers---</option>
								{suppliers.map(s=>(

										<option key={s.id} value={s.id}>{s.name}{s.company ? `(${s.company})` : ''}
										</option>
									))}
							</select>
						</div>

						<div className="form-group col-md-2 d-flex align-items-end">

							<button className="btn btn-light mr-2" onClick={run} disabled={loading}>{loading ? 'Loading...' : 'Run'}</button>
							<button className="btn btn-light mr-2" onClick={reset}>Reset</button>
							<button className="btn btn-success mr-2" onClick={csvExport} disabled={!rows.length}>Export Execl</button>
						</div>						
					</div>
				</div>

				<div className="card-body">
					{sum && (

							<div className="row">
								<div className="col-md-2 mb-2">
									<div className="p-2 border rounded">
										<div className="text-muted">Orders</div>
										<div className="h5 mb-0">{sum.orders}</div>
									</div>									
								</div>
								<div className="col-md-2 mb-2">
								  <div className="p-2 border rounded">
									<div className="text-muted">Subtotal</div>
									<div className="h5 mb-0">${sum.subtotal.toFixed(2)}</div>
									</div>
								</div>

								<div className="col-md-2 mb-2">
								  <div className="p-2 border rounded">
									<div className="text-muted">VAT</div>
									<div className="h5 mb-0">${sum.total_vat.toFixed(2)}</div>
									</div>
								</div>

								<div className="col-md-2 mb-2">
								  <div className="p-2 border rounded">
									<div className="text-muted">Discount</div>
									<div className="h5 mb-0">${sum.discount.toFixed(2)}</div>
									</div>
								</div>

								<div className="col-md-2 mb-2">
								  <div className="p-2 border rounded">
									<div className="text-muted">Grand</div>
									<div className="h5 mb-0">${sum.grand_total.toFixed(2)}</div>
									</div>
								</div>

								<div className="col-md-2 mb-2">
								  <div className="p-2 border rounded">
									<div className="text-muted">Paid / Due</div>
					<div className="mb-0">${sum.paid_total.toFixed(2)} / ${sum.due_total.toFixed(2)}</div>
									</div>
								</div>
							</div>
						)}					
				</div>

				<div className="table-responsive">
				 <table className="table table-sm mb-0">
				 		<thead className="thead-light">
				 			<tr>
				 				<th>#</th>
				 				<th>Date</th>
				 				<th>Purchase No</th>
				 				<th>Supplier</th>
				 				<th>Method</th>
				 				<th>Reference</th>
				 				<th className="text-right">Items</th>
				 				<th className="text-right">Grand</th>
				 				<th className="text-right">Paid</th>
				 				<th className="text-right">Due</th>
				 			</tr>
				 		</thead>
				 		<tbody>
				 			{rows.map((r, i)=>{

				 				const idx = (meta?.from || 1) + i
				 				const date = new Date(r.purchase_date || r.created_at).toLocaleString()
				 				const supp = r.supplier ? (r.supplier.name + (r.supplier.company ? `(${r.supplier.company})` : '')) : ''

				 				const due = (Number(r.grand_total || 0) - Number(r.paid_amount || 0)).toFixed(2)

				 				return (

				 						<tr key = {r.id}>
				 							<td>{idx}</td>
				 							<td>{date}</td>
				 							<td>{r.purchase_no}</td>
				 							<td>{supp}</td>
				 							<td>{r.payment_method}</td>
				 							<td>{r.reference_no || '_'}</td>
				 							<td className="text-right">{r.items_count ?? '_'}</td>
				 							<td className="text-right">${Number(r.grand_total || 0).toFixed(2)}</td>
				 							<td className="text-right">${Number(r.paid_amount || 0).toFixed(2)}</td>
				 							<td className="text-right">${due}</td>
				 						</tr>
				 					)
				 			})}

				 			{rows.length === 0 && ( <tr><td className="text-center text-muted" colSpan="10">No Purchase</td></tr>
				 			)}
				 		</tbody>
					</table>
				</div>

				{meta && (

					<div className="card-footer">
						<button className="btn btn-sm btn-light mr-2" disabled={!meta.prev_page_url} onClick={()=> load(meta.current_page - 1)}>Prev</button>
						<span>Page {meta.current_page} of {meta.last_page}</span>

						<button className="btn btn-sm btn-light mr-2" disabled={!meta.next_page_url} onClick={()=> load(meta.current_page + 1)}>Next</button>
					</div>

					)}

			</div>
		)
}