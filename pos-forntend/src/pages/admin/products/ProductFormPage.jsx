import React, { useEffect, useState } from 'react'
import { useNavigate, useParams} from 'react-router-dom'
import { createProduct, getProduct, updateProduct } from '../../../api/client.js'

export default function ProductFormPage({ mode })
{
	const isEdit = mode === 'edit'
	const { id } = useParams()
	const nav = useNavigate()
	const [f, setF] = useState({

		category_id: '', name: '', sku: '', barcode: '',
		unit: 'PCS', price: '0', vat_percent: '0', is_active: 1
	})

	const [file, setFile] = useState(null)
	const [preview, setPreview] = useState(null)

	const apiBase = (import.meta.env.VITE_API_BASE_URL || window.location.origin).replace(/\/$/,'')

	const imgUrl = (p) => p?.image_url || (p?.image ? `${apiBase}/images/products/${p.image}` : null)

	useEffect(() => {

		if(isEdit && id)
		{
			(async () => {

				const {data} = await getProduct(id)
				setF({

					category_id: data.category_id || '',
					name: data.name || '',
					sku: data.sku || '',
					barcode: data.barcode || '',
					unit: data.unit || 'PCS',
					price: data.price?.toString() || '0',
					vat_percent: data.vat_percent?.toString() || '0',
					is_active: data.is_active ? 1 : 0
				})

				setPreview(imgUrl(data))
			})()
		}
	}, [id, isEdit])

	const set = (k, v)=> setF(prev=>({...prev, [k]: v}))

	const submit = async () =>
	{
		try {
				const fd = new FormData()
				if(f.category_id) fd.append('category_id', f.category_id)
				fd.append('name', f.name)
				fd.append('sku', f.sku)
				if(f.barcode) fd.append('barcode', f.barcode)
				fd.append('unit', f.unit)
				fd.append('price', f.price)
				fd.append('vat_percent', f.vat_percent || 0)
				fd.append('is_active', f.is_active ? 1 : 0)
				if(file) fd.append('image', file)

				if(isEdit) await updateProduct(id, fd)
					else await createProduct(fd)

				alert('saved!')
				nav('/admin/products')
			} 
		catch(e) 
		{
			alert(e?.response?.data?.message || 'Saved Failed')
		}
	}

	return (

			<div className="card"> 
				<div className="card-header">
					{isEdit ? 'Edit' : 'Add'}
					Product
				</div>
				<div className="card-body">
					<div className="form-row">
						<div className="form-group col-md-3">
							<label>Category ID (Optional)</label>
							<input className="form-control" value={f.category_id} onChange={e=>set('category_id',e.target.value)} />
						</div>
						<div className="form-group col-md-5">
							<label>Name</label>
							<input className="form-control" value={f.name} onChange={e=>set('name', e.target.value)} />
						</div>
						<div className="form-group col-md-4">
							<label>SKU</label>
							<input className="form-control" value={f.sku} onChange={e=>set('sku', e.target.value)} />
						</div>
					</div>

					<div className="form-row">
						<div className="form-group col-md-4">
							<label>Barcode (Optional)</label>
							<input className="form-control" value={f.barcode} onChange={e=>set('barcode', e.target.value)} />
						</div>

						<div className="form-group col-md-2">
							<label>Unit</label>
							<select className="form-control" value={f.unit} onChange={e=>set('unit', e.target.value)}>
								<option value="PCS">PCS</option>
								<option value="KG">KG</option>
								<option value="LT">Litre</option>
							</select>
						</div>

						<div className="form-group col-md-3">
							<label>Price</label>
							<input className="form-control" type="number" step="0.01" value={f.price} onChange={e=>set('price', e.target.value)} />
						</div>

						<div className="form-group col-md-3">
							<label>VAT %</label>
							<input className="form-control" type="number" step="0.01" value={f.vat_percent} onChange={e=>set('vat_percent', e.target.value)} />
						</div>
					</div>

					<div className="form-row">
						<div className="form-group col-md-6">
							<label>Product Image</label>
							<input type="file" className="form-control-file" accept="image/*" onChange={e=>{ const ff=e.target.files?.[0] || null; setFile(ff);  setPreview(ff?URL.createObjectURL(ff):preview) }} />
							{preview && <img src={preview} alt="preview" style={{marginTop:8, width:96, height:96, objectFit:'cover', border: '1px solid #eee' }} />}
						</div>

						<div className="form-group col-md-6" style={{marginTop:30}}>
							<div className="form-check">
								<input id="na" className="form-check-input" type="checkbox" checked={!f.is_active} onChange={e=>set('is_active', e.target.value ? 0 : 1)} />
								<label htmlFor="na" className="form-check-label">
									Not Available (make product unavailable in store)
								</label>
							</div>
						</div>
					</div>

					<button className="btn btn-primary" onClick={submit}>{isEdit ? 'Update' : 'Create'}</button>
				</div>
			</div>

		)
}