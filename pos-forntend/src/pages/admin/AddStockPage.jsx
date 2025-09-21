import React, { useState } from 'react'
import ProductSearchBox from '../../components/ProductSearchBox.jsx'
import { addStock } from '../../api/client.js'


export default function AddStockPage()
{
	const [selected, setSelected] = useState(null)
	const [qty, setQty] = useState('1')
	const [note, setNote] = useState('')
	const [file, setFile] = useState(null)
	const [preview, setPreview] = useState(null)

	const apiBase = (import.meta.env.VITE_API_BASE_URL || window.location.origin).replace(/\/$/,'')

	const imgUrl = (p) => p?.image_url || (p?.image ? `${apiBase}/images/products/${p.image}`: null)

	const onPickFile = (e) =>
	{
		const f = e.target.files?.[0] || null
		setFile(f)
		setPreview(f ? URL.createObjectURL(f) : imgUrl(selected))
	} 

	const submit = async () => 
	{
		if(!selected) return alert('Select a product')
		const q = parseFloat(qty)
		if(!q || q <= 0) return alert('Quantity must be > 0')

		try
		{
			const fd = new FormData()
			fd.append('product_id', selected.id)
			fd.append('quantity', q)
			if(note) fd.append('note', note)
			if(file) fd.append('image', file)

			const res = await addStock(fd)
			alert('Stock Added!');

			const p = res?.data?.product
			setSelected(p || selected)
			setPreview(imgUrl(p || selected))
			setQty('1'); setNote(''); setFile(null)
		}
		catch(e)
		{
			alert(e?.response?.data?.message || 'Failed')
		}
	}

	return (

			<div className="row">
				<div className="col-lg-6">
					<div className="card">
						<div className="card-header">Add Stock</div>
						<div className="card-body">
							<ProductSearchBox onSelect={p=> { setSelected(p); setPreview(imgUrl(p))}} />
							{selected && (

								<div className="alert alert-info mt-3 d-flex align-items-center">
									<div className="mr-3">
										{preview ? 
											<img src={preview} alt={selected.name} style={{ width: 64, height: 64, objectFit: 'cover', border: '1px solid #eee', borderRadius: 4}} /> : <div style={{ width: 64, height: 64, border: '1px solid #eee', borderRadius:4, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize:12, color: '#999'}}>No Image</div>}
									</div>

									<div>
										<strong>{selected.name}</strong>(Unit: {selected.unit}) &bull; Stock: {selected.stock}
									</div>
								</div>
							)}

							<div className="form-group">
								<label>Product Image (jpg.jpeg/png/gif/webp, max 2MB)</label>
								<input id="prodImg" type="file" className="form-control-file" accept="image/*" onChange={onPickFile} />
								<small className="form-text text-muted">Optional, Existing image will be replaced</small>
							</div>

							<div className="form-group">
								<label>Quantity to Add</label>
								<input className="form-control" value={qty} onChange={e=>setQty(e.target.value)} />
							</div>

							<div className="form-group">
								<label>Note (optional)</label>
								<input className="form-control" value={note} onChange={e=>setNote(e.target.value)} />
							</div>
							<button className="btn btn-primary" onClick={submit}>Save</button>
						</div>
					</div>
				</div>
			</div>

			)
}