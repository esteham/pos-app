import React, { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { listProducts, deleteProduct } from '../../../api/client.js'

export default function ProductsListPage()
{
	const [q, setQ] = useState('')
	const [rows, setRows] = useState([])
	const [meta, setMeta] = useState(null)
	const nav = useNavigate()

	const load = async (page=1) => {

		const { data } = await listProducts({q, page, per_page: 20})
		setRows(data.data || [])
		setMeta(data)
	}

	useEffect(() => {load()}, [])

	const onDelete = async (id) =>
	{
		if(!confirm('Delete this product?')) return
			try { await deleteProduct(id); await load(meta?.current_page || 1)} catch(e){ alert(e?.response?.data?.message || 'Delete failed')
		}
	}

	const apiBase = (import.meta.env.VITE_API_BASE_URL || window.location.origin).replace(/\/$/,'')

	const img = (p) => p.image_url || (p.image ? `${apiBase}/images/products/${p.image}` : '')

	return (

			<div className="card">
				<div className="card-header d-flex">
					<div className="mr-auto">
						<input className="form-control" placeholder="Search Name/SKU/barcode..." value={q} onChange={e=>setQ(e.target.value)} />
					</div>

					<button className="btn btn-secondary ml-2" onClick={() => load(1)}>Search</button>
					<Link className="btn btn-primary ml-2" to="/admin/products/new">+ Add Product</Link>
				</div>	

				<div className="card-body p-0">
					<table className="table table-sm mb-0">
						<thead className="thead-light">
							<tr>
								<th>#</th><th>Image</th><th>Name</th><th>SKU</th>
								<th>Unit</th><th className="text-right">Price</th>
								<th>VAT</th><th>Status</th><th>Actions</th>
							</tr>
						</thead>

						<tbody>
							{rows.map((p, i)=>(

								<tr key = {p.id}>
									<td>{(meta?.from || 1)+i}</td>
									<td>{p.image ? <img src={img(p)} style={{width: 40, height:40, objectFit: 'cover'}} />: '_'}</td>

									<td>{p.name}</td>
									<td>{p.sku}</td>
									<td>{p.unit}</td>
									<td className="text-right">${p.price}</td>
									<td>{p.vat_percent}</td>
									<td>{p.is_active ? <span className="badge badge-success">Available</span> : <span className="badge badge-secondary">Not Available</span>}</td>

									<td>
										<Link className="btn btn-sm btn-outline-primary me-2" to={`/admin/products/${p.id}/edit`}>Edit</Link>
										<button className="btn btn-sm btn-outline-danger" onClick={()=>onDelete(p.id)}>Delete</button>
									</td>
								</tr>
								))}

							{rows.length === 0 && <tr><td colSpan="9" className="text-center">No Products Found!</td></tr>}
						</tbody>
					</table>
				</div>

				{meta && (

					<div className="card-footer">
						<button className="btn btn-sm btn-light mr-2" disabled={!meta.prev_page_url} onClick={()=>load(meta.current_page-1)}>Prev</button>
						<span>Page {meta.current_page} of {meta.last_page}</span>

						<button className="btn btn-sm btn-light ml-2" disabled={!meta.next_page_url} onClick={()=>load(meta.current_page+1)}>Next</button>
					</div>
					)}
			</div>
		)

	}
