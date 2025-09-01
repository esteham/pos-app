import React, { useMemo, useState } from 'react'
import ProductSearchBox from './ProductSearchBox.jsx'
import CartTable from './CartTable.jsx'
import PaymentSummary from './PaymentSummary.jsx'
import CustomerPhoneInput from './CustomerPhoneInput.jsx'
import { createSale } from '../api/ApiAxios.jsx'
import { computeTotals } from '../utils/Calc.jsx'

export default function POSPage()
{
	const [selected, setSelected] = useState(null)
	const [unitPrice, setUnitPrice] = useState('')
	const [qty, setQty] = useState('')
	const [vatPercent, setVatPercent] = useState('')
	const [lines, setLines] = useState([])
	const [discount, setDiscount] = useState(0)
	const [paidAmount, setPaidAmount] = useState(0)
	const [paymentMethod, setPaymentMethod] = useState('CASH')
	const [customer, setCustomer] = useState({ phone: '', name: ''})
	const [submitting, setSubmitting] = useState(false)
	const [invoice, setInvoice] = useState('')

	const totals = useMemo(() => computeTotals(lines, discount), [lines, discount])

	const onProductSelect = (p) =>
	{
		setSelected(p)
		setUnitPrice(String(p.Price || ''))
		setQty(p.unit === 'KG' ? '0.5' : '1')
		setVatPercent(String(p.vat_percent || 0))
	}

	const addLine = () =>
	{
		if(!selected) return alert('Select a product first')
		const q = parseFloat(qty)
		const up = parseFloat(unitPrice)
		const vp = parseFloat(vatPercent || 0)
		if(!q || q <= 0) return alert('Quantity must be > 0')  
		if(!up || up < 0) return alert('Unit price invalid')

		const newLine = {
			product_id: selected.id,
			name: selected.name,
			unit: selected.unit,
			unit_price: up,
			quantity: q,
			vat_percent: vp,
			stock: selected.stock,
			image_url: selected.image_url || null
		}

		setLines(prev => {
			const idx = prev.findIndex(l => l.product_id === newLine.product_id && l.unit_price === newLine.unit_price && l.vat_percent === newLine.vat_percent)

			if(idx >= 0)
			{
				const copy = [...prev]
				copy[idx] = {...copy[idx], quantity: copy[idx].quantity + newLine.quantity}
				return copy
			}

			return [...prev, newLine]
		})

		setSelected(null)
		setUnitPrice('')
		setQty('')
		setVatPercent('') 
	}

	const removeLine = (i) =>
	{
		setLines(prev => prev.filter((_, idx) => idx !== i))
	}

	const updateQty = (i, val) =>
	{
		const num = parseFloat(val || 0)
		setLines(prev => prev.map((l, idx) => idx === i ? {...l, quantity: num } : l))
	}

	const submitSale = async () => {
		if(lines.length === 0) return alert('Cart is Empty.')  // return 

		setSubmitting(true)

		try
		{
			const payload = {
				customer_phone: customer.phone || null,
				customer_name: customer.name || null,
				payment_method: paymentMethod,
				discount: Number(discount || 0),
				paid_amount: Number(paidAmount || totals.grand),
				items: lines.map(l => ({
					product_id: l.product_id,
					quantity: l.quantity,
					unit_price: l.unit_price,
					unit: l.unit,
					vat_percent: l.vat_percent 
				}))
			}

			const res = await createSale(payload)
			setInvoice(res.data?.invoice_no || '')
			alert('Sale completed! Invoice: ' + (res.data?.invoice_no || 'N/A'))
			setLines([]); setDiscount(0); setPaidAmount(0)
			setCustomer({ phone: '', name: ''})
		}
		catch(e)
		{
			const msg = e?.response?.data?.message || 'Failed to submit sale'
			alert(msg)
		}
		finally
		{
			setSubmitting(false)
		}
	}

	return (
		<div className="row">
			<div className="col-lg-8">
				<div className="card mb-3">
					<div className="card-header">
						Product Live Search
					</div>
					<div className="card-body">
						<ProductSearchBox onSelect={onProductSelect} />
						<hr />
						<div className="row">
							<div className="col-md-6">
								<label>Selected Product</label>
								<input className="form-control" readOnly value={selected ? `${selected.name} [${selected.sku}] (${selected.unit})` : ''} />
								{selected && ( <small className="text-muted"> Stock: {selected.stock} | Default VAT : {selected.vat_percent}%</small> )}
							</div>
							<div className="col-md-2">
								<label>Unit Price</label>
								<input className="form-control" value={unitPrice} onChange={e=>setUnitPrice(e.target.value)} />
							</div>
							<div className="col-md-2">
								<label>{selected?.unit==='KG' ? 'KG' :'Qty'}</label>
								<input className="form-control" value={qty} onChange={e=>setQty(e.target.value)} />
							</div>
							<div className="col-md-2">
								<label>VAT %</label>
								<input className="form-control" value={vatPercent} onChange={e=>setVatPercent(e.target.value)} />
							</div>
						</div>
						<button className="btn btn-success mt-2" onClick={addLine}>+ Add to Cart</button>
					</div>
				</div>

				<CartTable lines={lines} updateQty={updateQty} removeLine={removeLine} />
			</div>

			<div className="col-lg-4">
				<div className="card mb-2">
					<div className="card-header">
						Customer
					</div>
					<div className="card-body">
						<CustomerPhoneInput value={customer} onChange={setCustomer} />
					</div>
				</div>
				<PaymentSummary 
					totals={totals}
					discount={discount}
					setDiscount={setDiscount}
					paidAmount={paidAmount}
					setPaidAmount={setPaidAmount}
					paymentMethod={setPaymentMethod}
					submitting={submitting}
					onSubmit={submitSale}
					lastInvoice={invoice}
				/>
			</div>
		</div>
	)
}
