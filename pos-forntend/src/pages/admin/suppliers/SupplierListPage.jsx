import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { listSuppliers, deleteSupplier } from '../../../api/client.js'

export default function SupplierListPage()
{
	const [q, setQ] = useState('')
	const [rows, setRows] = useState([])
	const [meta, setMeta] = useState(null)

	const load = async (page=1) =>
	{
		const {data} = await listSuppliers({q, page, per_page: 20})
		setRows(data.data||[]);
		setMeta(data)
	}

	useEffect(()=>{ load()}, [])

	const onDelete = async (id) =>
	{
		if(!confirm('Delete this supplier?')) return
		try 
		{
			await deleteSupplier(id);
			await load(meta?.current_page||1)
		}

		catch(e)
		{
			alert('Delete Failed')
		}

	}

	return (

			<div className="card">
				<div className="card-header d-flex">
					<input className="form-control" placeholder="Search name/phone/company" value={q} onChange={e=>setQ(e.target.value)} />
					<button className="btn btn-secondary ml-2" onClick={()=>load(1)}>Search</button>
					<Link className="btn btn-primary ml-2" to="/admin/suppliers/new">+ Add Supplier</Link>
				</div>
				<div className="card-body p-0">
					<table className="table table-sm mb-0">
						<thead className="thead-light">
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Phone</th>
								<th>Company</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							{rows.map((s, i)=>(
								<tr key={s.id}>
									<td>{(meta?.from||1)+i}</td>
									<td>{s.name}</td>
									<td>{s.phone}</td>
									<td>{s.company}</td>
									<td>{s.is_active ? <span className="badge badge-success">Active</span> : <span className="badge badge-danger">Inactive</span>}</td>
									<td>
										<Link className="btn btn-sm btn-outline-primary mr-2" to={`/admin/suppliers/${s.id}/edit`}>Edit</Link>
										<button className="btn btn-sm btn-outline-danger" onClick={()=>onDelete(s.id)}>Delete</button>
									</td>
								</tr>
								))}
							{rows.length===0 && <tr><td colSpan="6" className="text-center text-muted">No Suppliers Found!</td></tr>}
						</tbody>	
					</table>
				</div>
				{meta && (

						<div className="card-footer">
							<button className="btn btn-sm btn-light mr-2" disabled={!meta.prev_page_url} onClick={()=>load(meta.current_page-1)}>Prev</button>
							<span>Page {meta.current_page} of {meta.last_page}</span>
							<button className="btn btn-sm btn-light ml-2" disabled={!meta.next_page_url} onClick={()=>load(meta.current_page+1)}>Next</button>				</div>
					)}	
			</div>
		)

}