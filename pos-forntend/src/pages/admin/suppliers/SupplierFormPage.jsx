import React, { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { createSupplier, getSupplier, updateSupplier} from '../../../api/client.js'

export default function SupplierFormPage({ mode })
{
	const isEdit = mode === 'edit'
	const { id } = useParams()
	const nav = useNavigate()

	const [f, setF] = useState({name: '', phone: '', email: '', company: '', tax_id: '', address: '', opening_balance: '0', is_active: 1})

	useEffect(()=>{

		if(isEdit && id)
		{
			(async () => {

				const {data} = await getSupplier(id)
				setF({

					name: data.name||'', phone:data.phone||'', email:data.email||'',company:data.company||'',tax_id:data.tax_id||'',address:data.address||'', opening_balance: (data.opening_balance ?? 0).toString(), is_active: data.is_active ? 1 : 0
				})
			})()
		}
	}, [id, isEdit])

	const set = (k, v)=>setF(prev=>({...prev, [k]:v}))

	const submit = async () =>{

		try 
		{
			if(isEdit) await updateSupplier(id, f)
				else await createSupplier(f)
					alert('Saved!')
				nav('/admin/suppliers')
		} 
		catch(e) 
		{
			alert(e?.response?.data?.message || 'Save failed')
			
		}
	}

	return (

			<div className="card">
				<div className="card-header">
					{isEdit?'Edit':'Add'}Supplier
				</div>
				<div className="card-body">
					<div className="form-row">
						<div className="form-group col-md-4">
							<label>Name</label>
							<input className="form-control" value={f.name} onChange={e=>set('name',e.target.value)} />
						</div>

						<div className="form-group col-md-4">
							<label>Phone</label>
							<input className="form-control" value={f.phone} onChange={e=>set('phone',e.target.value)} />
						</div>

						<div className="form-group col-md-4">
							<label>Email</label>
							<input className="form-control" value={f.email} onChange={e=>set('email',e.target.value)} />
						</div>

						<div className="form-group col-md-4">
							<label>Company</label>
							<input className="form-control" value={f.company} onChange={e=>set('company',e.target.value)} />
						</div>

						<div className="form-group col-md-4">
							<label>Tax ID</label>
							<input className="form-control" value={f.tax_id} onChange={e=>set('tax_id',e.target.value)} />
						</div>

						<div className="form-group col-md-4">
							<label>Opening Balance</label>
							<input className="form-control" type="number" step="0.01" value={f.opening_balance} onChange={e=>set('opening_balance',e.target.value)} />
						</div>
					 </div>

						<div className="form-group">
							<label>Address</label>
							<textarea className="form-control" rows="2" value={f.address} onChange={e=>set('address', e.target.value)} />
						</div>

						<div className="form-group col-md-3">					
							<input id="active" className="form-check-input" type="checkbox" checked={!!f.is_active} onChange={e=>set('is_active', e.target.value ? 1 : 0)} />

							<label htmlfor="active" className="form-check-label">Active</label>
						</div>

						<button className="btn btn-primary" onClick={submit}>{isEdit? 'Update' : 'Create'}</button>
					</div>				
			</div>
		)


}